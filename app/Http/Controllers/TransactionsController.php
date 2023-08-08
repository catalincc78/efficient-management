<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use App\Models\Products;
use App\Models\Transactions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class TransactionsController extends Controller
{
    public static function main(){
        $products = Products::all();
        return view('transactions.main', ['products' => $products]);
    }
    public static function list(){
        $transactions = Helper::getPaginatedTransactions();
        return response()->json([
            'success' => 1,
            'current_page' => $transactions->currentPage(),
            'html' => view('transactions.list', ['transactions' => $transactions])->render()
        ]);
    }

    public static function get($id){
        $transaction = Transactions::with(['transacted_items'])->find($id);
        $products = Products::all();
        $transactedItemsHTML = '';
        foreach($transaction->transacted_items as $item){
            $transactedItemsHTML .= view('transactions.transacted_items.edit', ['item' => $item, 'products' => $products])->render();
        }
        return response()->json([
            'success' => 1,
            'transaction' => $transaction,
            'html' => $transactedItemsHTML
        ]);
    }

    private static function saveTransaction($id = 0){
        $isEdit = !empty($id);
        $inputData = request()->input();
        if(empty($inputData['target_type'])){
            return response()->json([
                'success' => 0,
                'messages' => ['general' => __('You must add at least one item.')],
            ]);
        }
        $rules = [];
        foreach ($inputData['target_type'] as $index => $targetType) {
            if(empty($inputData['product_id'][$index])){
                $inputData['product_id'][$index] = null;
            }
            $itemRules = [
                'target_type.' . $index => ['required', 'in:product,activity'],
                'product_id.' . $index => ['nullable', 'required_if:target_type.' . $index . ',product', 'numeric'],
                'quantity.' . $index => ['nullable', 'required_if:target_type.' . $index . ',product', 'numeric'],
                'activity.' . $index => ['nullable', 'required_if:target_type.' . $index . ',activity', 'string', 'max:1000'],
                'amount.' . $index => ['required', 'numeric'],
                'is_amount_positive.' . $index => ['required', 'numeric', 'in:0,1'],
            ];
            $rules = array_merge($rules, $itemRules);
        }

        $validator = Validator::make($inputData, $rules);

        if($validator->fails()) {
            info($validator->errors());
            return response()->json([
                'success' => 0,
                'messages' => $validator->errors(),
            ]);
        }
        $data = [
            'user_id' => auth()->user()->id
        ];

        if($isEdit){
            DB::table('transactions')->where('id' , $id)->update($data);
        }else{
            $id = DB::table('transactions')->insertGetId($data);
        }

        DB::table('transacted_items')->where('transaction_id', $id)->delete();

        foreach ($inputData['target_type'] as $index => $targetType) {
            $data = [
                'transaction_id' => $id,
                'target_type' => $inputData['target_type'][$index],
                'product_id' => $inputData['product_id'][$index],
                'quantity' => ($inputData['is_amount_positive'][$index] ? -1 : 1) * $inputData['quantity'][$index] ,
                'activity' => $inputData['activity'][$index],
                'amount' => ($inputData['is_amount_positive'][$index] ? 1 : -1) * $inputData['amount'][$index]  ,
            ];
            DB::table('transacted_items')->insert($data);
        }

        $transactions = Helper::getPaginatedTransactions();
        info(view('transactions.list', ['transactions' => $transactions])->render());
        return response()->json([
            'success' => 1,
            'messages' => ['Transaction ' . ($isEdit ? 'updated' : 'created') . ' successfully!'],
            'html' => view('transactions.list', ['transactions' => $transactions])->render()
        ]);
    }
    public static function add(){
        return self::saveTransaction();
    }

    public static function edit($id){
        return self::saveTransaction($id);
    }

    public static function delete($id){
        Transactions::where('id', $id)->update(['active' => 0]);
        $transactions = Helper::getPaginatedTransactions();
        return response()->json([
            'success' => 1,
            'messages' => ['Transaction has been deleted successfully!'],
            'html' => view('transactions.list', ['transactions' => $transactions])->render()
        ]);
    }
}
