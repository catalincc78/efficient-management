<?php

namespace App\Http\Controllers;

use DatePeriod;
use DateInterval;
use App\Models\Products;
use App\Models\TransactedItems;
use App\Models\Transactions;
use Illuminate\Http\Request;

class StatisticsController extends Controller
{
    public static function main(){
        $products =  Products::where('user_id', auth()->user()->id)->where('active', 1)->get();
        return view('statistics.main', ['products' => $products]);
    }
    public static function list(){
        $availableData = Transactions::selectRaw('DATE(transactions.created_at) AS date, COALESCE(SUM(transacted_items.amount), 0) AS total_amount')
            ->leftJoin('transacted_items', function ($join){
                $join->on('transactions.id', '=', 'transacted_items.transaction_id');
            })
            ->whereBetween('transactions.created_at', [request()->filter_date_start, gmdate('Y-m-d H:i:s', strtotime(request()->filter_date_end.' + 1 day'))])
            ->where('transactions.user_id', auth()->user()->id)
            ->where('transactions.active', 1)
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $interval = new DateInterval('P1D');
        $date_range = new DatePeriod(date_create(request()->filter_date_start), $interval, date_create(gmdate('Y-m-d H:i:s', strtotime(request()->filter_date_end.' + 1 day'))));

        $chartData = [];

        foreach ($date_range as $date) {
            $chartData[$date->format('Y-m-d')] = (object)[
                'date' => $date->format('Y-m-d'),
                'total_amount' => 0
            ];
        }
        foreach($availableData as $data) {
            info($data);
            $chartData[$data->date] = (object)[
                'date' => $data->date,
                'total_amount' => $data->total_amount
            ];
        }
        return response()->json([
            'success' => 1,
            'chartData' => array_values($chartData)
        ]);
    }
}
