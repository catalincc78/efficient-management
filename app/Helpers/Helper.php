<?php

namespace App\Helpers;

use App\Http\Controllers\TransactionsController;
use App\Models\Products;
use App\Models\Transactions;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\DB;

class Helper
{
    public static function getPaginatedProducts($arFilters = []){
        $perPage = 2;
        $products =  Products::where('user_id', auth()->user()->id)->where('active', 1);
        $paginated = $products->paginate($perPage);
        if($paginated->lastPage() < $paginated->currentPage()){
            Paginator::currentPageResolver(function() use($paginated){ return $paginated->lastPage();});
            $paginated = $products->paginate($perPage);
        }
        return $paginated;
    }
    public static function getPaginatedTransactions(){
        $perPage = 2;
        $transactions = Transactions::with(['transacted_items'])
            ->where('user_id', auth()->user()->id)
            ->where('active', 1)
            ->where('created_at', '>', request()->filter_date_start)
            ->where('created_at', '<', gmdate('Y-m-d H:i:s', strtotime(request()->filter_date_end.' + 1 day')));
        if(!empty(request()->filter_product)){
            $transactions->whereHas('transacted_items', function($query){
                $query->whereHas('product', function($query){
                   $query->where('product_id', request()->filter_product);
                });
            });
        }

        $paginated = $transactions->paginate($perPage);
        if($paginated->lastPage() < $paginated->currentPage()){
            Paginator::currentPageResolver(function() use($paginated){ return $paginated->lastPage();});
            $paginated = $transactions->paginate($perPage);
        }
        return $paginated;
    }

}
