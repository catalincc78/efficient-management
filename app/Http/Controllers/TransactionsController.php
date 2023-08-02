<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TransactionsController extends Controller
{
    public static function list()
    {
        info('list');
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
