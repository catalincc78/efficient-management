<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use App\Models\Products;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ProductsController extends Controller
{
    public static function main()
    {
        $products = Products::where('user_id', auth()->user()->id)->where('active', 1)->get();
        return view('products.main', ['products' => $products]);
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
        $product = Products::where('user_id', auth()->user()->id)->where('active', 1)->find($id);
        return response()->json([
            'success' => $product ? 1 : 0,
            'product' => $product
        ]);
    }
    private static function saveProduct($id = 0)
    {
        try {
            $data = [
                'user_id' => auth()->user()->id,
                'name' => request()->name,
                'sku' => request()->sku,
            ];
            if ($id) {
                $currentProduct = Products::find($id);
            }
            $rules = [
                'name' => ['required', 'string', 'max:255'],
                'sku' => ['required', 'string', 'max:255', 'unique:products,sku' . ($id ? ',' . $currentProduct->id : '')],
            ];

            $errorMessages = [
                'name' => __('The <strong>name</strong> field is required'),
                'sku' => __('The <strong>sku</strong> field is required'),
                'sku.unique' => __('The sku needs to be unique')
            ];

            $validator = Validator::make($data, $rules, $errorMessages);

            if ($validator->fails()) {
                return response()->json([
                    'success' => 0,
                    'messages' => $validator->errors(),
                ]);
            } else {
                if (!empty($id)) {
                    DB::table('products')->where('id', $id)->where('user_d', auth()->user()->id)->update($data);
                } else {
                    DB::table('products')->insert($data);
                }
                $products = Helper::getPaginatedProducts();
                return response()->json([
                    'success' => 1,
                    'messages' => [__('Product ' . (empty($id) ? 'created' : 'updated') . ' successfully!')],
                    'html' => view('products.list', ['products' => $products])->render()
                ]);
            }
        } catch(\Exception $e) {
            Log::error('Add/Edit Product: ' . $e->getMessage());
        }
        return response()->json([
            'success' => 0,
            'errors' => 1,
            'messages' => [__('Something went wrong while ' . (empty($id) ? 'adding' : 'updating') . ' the product')]
        ]);
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
        Products::where('id', $id)->where('user_id', auth()->user()->id)->update(['active' => 0]);
        $products = Helper::getPaginatedProducts();
        return response()->json([
            'success' => 1,
            'messages' => [__('Product has been deleted successfully!')],
            'html' => view('products.list', ['products' => $products])->render()
        ]);
    }
}
