<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use Illuminate\Http\Request;

class TransactionsController extends Controller
{
    public static function main()
    {
        return view('transactions.main');
    }
    public static function list()
    {
        $transactions = Helper::getPaginatedTransactions();
        return response()->json([
            'success' => 1,
            'current_page' => $transactions->currentPage(),
            'html' => view('transactions.list', ['transactions' => $transactions])->render()
        ]);
    }

    public static function get($id)
    {
        info('get'.$id);
    }
    public static function add($id)
    {
        info('add'.$id);
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
