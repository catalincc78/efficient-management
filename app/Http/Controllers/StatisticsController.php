<?php

namespace App\Http\Controllers;

use App\Models\Products;
use App\Models\TransactedItems;
use App\Models\Transactions;
use Illuminate\Http\Request;

class StatisticsController extends Controller
{
    public static function main(){
        $products = Products::all();
        return view('statistics.main', ['products' => $products]);
    }
    public static function list(){
        $chartData = Transactions::selectRaw('DATE(transactions.created_at) AS date, COALESCE(SUM(transacted_items.amount), 0) AS total_amount')
            ->leftJoin('transacted_items', function ($join){
                $join->on('transactions.id', '=', 'transacted_items.transaction_id')
                    ->whereBetween('transactions.created_at', [request()->filter_date_start, gmdate('Y-m-d H:i:s', strtotime(request()->filter_date_end.' + 1 day'))]);
            })
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return response()->json([
            'success' => 1,
            'html' => view('statistics.list')->render(),
            'chartData' => $chartData
        ]);
    }
}
