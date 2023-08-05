<?php

namespace App\Helpers;

use App\Http\Controllers\TransactionsController;
use App\Models\Products;
use App\Models\Transactions;
use Illuminate\Pagination\Paginator;

class Helper
{
    public static function getPaginatedProducts($arFilters = [])
    {
        $perPage = 2;
        $products =  Products::where('user_id', auth()->user()->id)->paginate($perPage);
        if($products->lastPage() < $products->currentPage()){
            Paginator::currentPageResolver(function() use($products){ return $products->lastPage();});
            $products =  Products::where('user_id', auth()->user()->id)->paginate($perPage);
        }
        return $products;
    }
    public static function getPaginatedTransactions($arFilters = [])
    {
        $perPage = 2;
        $transactions=  Transactions::where('user_id', auth()->user()->id)->paginate($perPage);
        if($transactions->lastPage() < $transactions->currentPage()){
            Paginator::currentPageResolver(function() use($transactions){ return $transactions->lastPage();});
            $transactions =  Transactions::where('user_id', auth()->user()->id)->paginate($perPage);
        }
        return $transactions;
    }

}
