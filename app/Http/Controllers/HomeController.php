<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use App\Models\Products;
use App\Models\Transactions;
use Database\Seeders\ProductSeeder;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $products = Products::where('user_id', auth()->user()->id)->where('active', 1)->get();
        $transactions = $transactions = Transactions::with(['transacted_items'])
            ->where('user_id', auth()->user()->id)
            ->where('active', 1)->get();
        return view('home', ['products' => $products,
                                  'transactions' => $transactions]);
    }
}
