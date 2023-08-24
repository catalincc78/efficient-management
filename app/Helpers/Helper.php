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
        $perPage = 4;
        $products =  Products::where('user_id', auth()->user()->id)->where('active', 1);
        $paginated = $products->orderBy('name')->paginate($perPage);
        if($paginated->lastPage() < $paginated->currentPage()){
            Paginator::currentPageResolver(function() use($paginated){ return $paginated->lastPage();});
            $paginated = $products->paginate($perPage);
        }
        return $paginated;
    }
    public static function getPaginatedTransactions(){
        $perPage = 4;
        $transactions = Transactions::with(['transacted_items'])
            ->where('user_id', auth()->user()->id)
            ->where('active', 1)
            ->where('created_at', '>', request()->filter_date_start)
            ->where('created_at', '<', gmdate('Y-m-d H:i:s', strtotime(request()->filter_date_end.' + 1 day')))
            ->orderBy('created_at', 'ASC');
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
    public static function generateSKU()
    {
        $data = time().rand(100000,999999);;
        assert(strlen($data) == 16);

        $data[6] = chr(ord($data[6]) & 0x0f | 0x40); // set version to 0100
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80); // set bits 6-7 to 10

        return vsprintf('SKU%s%s%s%s%s%s%s%s', str_split(bin2hex($data), 4));
    }

    public static function getSqlWithBindings($query, $bExecuted = false){
        if($bExecuted){
            $sql = $query->sql;
            $bindings = $query->bindings;
        }else{
            $sql = $query->toSql();
            $bindings = $query->getBindings();
        }

        try{
            $s = vsprintf(str_replace('?', '%s', $sql), collect($bindings)->map(function ($binding) {
                return is_numeric($binding) ? $binding : "'{$binding}'";
            })->toArray());
        }catch(Exception $e){
            $s = $sql."\n\n".print_r($bindings, true);
        }

        return $s;
    }
}
