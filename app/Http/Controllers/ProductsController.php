<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
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
        $products = Helper::getPaginatedProducts();
        return response()->json([
            'success' => 1,
            'current_page' => $products->currentPage(),
            'html' => view('products.list', ['products' => $products])->render()
        ]);
    }

    public static function get($id)
    {
        return response()->json([
            'success' => 1,
            'product' => Products::find($id)
        ]);
    }
    private static function saveProduct($id = 0)
    {
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
            return response()->json([
                'success' => 0,
                'messages' => $validator->errors(),
            ]);
        }else{
            if(!empty($id)){
                DB::table('products')->where('id' , $id)->update($data);
            }else{
                DB::table('products')->insert($data);
            }
            $products = Helper::getPaginatedProducts();
            return response()->json([
                'success' => 1,
                'messages' => ['Product ' . (empty($id) ? 'created' : 'updated') . ' successfully!'],
                'html' => view('products.list', ['products' => $products])->render()
            ]);
        }

    }
    public static function add()
    {
        return self::saveProduct();
    }

    public static function edit($id)
    {
        return self::saveProduct($id);
    }

    public static function delete($id)
    {
        Products::where('id', $id)->update(['active' => 0]);
        $products = Helper::getPaginatedProducts();
        return response()->json([
            'success' => 1,
            'messages' => ['Product has been deleted successfully!'],
            'html' => view('products.list', ['products' => $products])->render()
        ]);
    }
}
