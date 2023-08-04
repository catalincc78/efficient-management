<?php

namespace App\Helpers;

use App\Models\Products;
use Illuminate\Pagination\Paginator;

class Helper
{
    public static function getPaginatedProducts($arFilters = [])
    {
        $perPage = 2;
        $products =  Products::where('user_id', auth()->user()->id)->paginate($perPage);
        info($products->lastPage() . ' vs ' . $products->currentPage());
        if($products->lastPage() < $products->currentPage()){
            Paginator::currentPageResolver(function() use($products){ return $products->lastPage();});
            $products =  Products::where('user_id', auth()->user()->id)->paginate($perPage);
        }
        return $products;
    }

}
