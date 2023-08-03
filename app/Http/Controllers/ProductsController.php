<?php

namespace App\Http\Controllers;

use App\Models\Products;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ProductsController extends Controller
{
    public static function main()
    {
        return view('products.main');
    }

    public static function list()
    {
        $perPage = 2;
        $products = Products::where('user_id', auth()->user()->id)->paginate($perPage);
        return response()->json([
            'success' => 1,
            'current_page' => $products->currentPage(),
            'html' => view('products.list', ['products' => $products])->render()
        ]);
    }

    public static function get($id)
    {
        info('get'.$id);
    }
    public static function add()
    {
        info('add');
        $data = [
            'user_id' => auth()->user()->id,
            'name' => request()->name,
            'sku' => request()->sku,
        ];

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'sku' => ['required', 'string', 'max:255', 'unique:products'],
        ];

        $validator = Validator::make($data, $rules);

        if($validator->fails()){
            info('fails');
            info($data);
            return response()->json([
                'success' => 0,
                'messages' => $validator->errors(),
            ]);
        }else{
            info('success');
            info($data);
            DB::table('products')->insert($data);
            $products = Products::where('user_id', auth()->user()->id)->get();

            return response()->json([
                'success' => 1,
                'messages' => ['Product created successfully!'],
                'html' => view('products.list', ['products' => $products])->render()
            ]);
        }

    }

    public static function edit($id)
    {
        info('edit'.$id);
    }

    public static function delete($id)
    {
        info('delete'.$id);
    }
}
