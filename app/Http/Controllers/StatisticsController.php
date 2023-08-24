<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use App\Models\User;
use DatePeriod;
use DateInterval;
use App\Models\Products;
use App\Models\TransactedItems;
use App\Models\Transactions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StatisticsController extends Controller
{
    public static function main(){
        $products =  Products::where('user_id', auth()->user()->id)->where('active', 1)->orderBy('name')->get();
        $user = User::where('id', auth()->user()->id)->select('type', 'cif')->first();
        return view('statistics.main', ['products' => $products, 'user' => $user]);
    }

    public static function validateVAT($companyVat = 'RO25323520') {
        $countryCode = substr($companyVat, 0, 2);
        $vatNumber = substr($companyVat, 2);

        $url = 'http://ec.europa.eu/taxation_customs/vies/checkVatService.wsdl';
        $client = new \SoapClient($url);

        $action = 'urn:checkVat';
        $client->__setSoapHeaders(new \SoapHeader($url, 'Action', $action));

        $request = [
            'vatNumber' => $vatNumber,
            'countryCode' => $countryCode,
        ];

        try {
            $operation = 'checkVat';
            $result = $client->__soapCall($operation, [$request]);

            if($result->valid){
                return ['error' => 0, 'valid-vat' => true];
            } else {
                return ['error' => 0, 'valid-vat' => false];
            }
        } catch (\SoapFault $e) {
            return ['error' => 1, 'message' => $e->getMessage()];
        } catch (\Exception $e) {
            return ['error' => 1, 'message' => $e->getMessage()];
        }
    }

    public static function chartDailyAmount(){
        $availableData = Transactions::selectRaw('DATE(transactions.created_at) AS date, COALESCE(SUM(transacted_items.amount), 0) AS total_amount')
            ->leftJoin('transacted_items', 'transactions.id', '=', 'transacted_items.transaction_id')
            ->whereBetween('transactions.created_at', [request()->filter_date_start, gmdate('Y-m-d H:i:s', strtotime(request()->filter_date_end.' + 1 day'))])
            ->where('transactions.user_id', auth()->user()->id)
            ->where('transactions.active', 1)
            ->when(!empty(request()->filter_product), function($query){
                $query->where('transacted_items.product_id', request()->filter_product);
            })
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $interval = new DateInterval('P1D');
        $date_range = new DatePeriod(date_create(request()->filter_date_start), $interval, date_create(gmdate('Y-m-d H:i:s', strtotime(request()->filter_date_end.' + 1 day'))));

        $chartData = [];

        foreach ($date_range as $date) {
            $chartData[$date->format('Y-m-d')] = (object)[
                'label' => $date->format('Y-m-d'),
                'value' => 0
            ];
        }
        foreach($availableData as $data) {
            $chartData[$data->date] = (object)[
                'label' => $data->date,
                'value' => $data->total_amount
            ];
        }
        return response()->json([
            'success' => 1,
            'chartData' => [array_values($chartData)]
        ]);
    }
    public static function chartDailyStock(){

    }
    public static function chartTotalAmount(){

    }
    public static function chartProfitPerProduct(){
        $perProductSQL = DB::table('transactions')->selectRaw('
                transacted_items.product_id,
                SUM(IF(transacted_items.amount > 0 OR (transacted_items.amount = 0 AND transacted_items.quantity <= 0), transacted_items.amount, 0)) as positive_amount,
                SUM(transacted_items.amount) as total_amount,
                SUM(IF(transacted_items.amount > 0 OR (transacted_items.amount = 0 AND transacted_items.quantity <= 0), transacted_items.quantity, 0)) as negative_quantity,
                SUM(transacted_items.quantity) as total_quantity
            ')
            ->leftJoin('transacted_items', 'transactions.id', '=', 'transacted_items.transaction_id')
            ->whereBetween('transactions.created_at', [request()->filter_date_start, gmdate('Y-m-d H:i:s', strtotime(request()->filter_date_end.' + 1 day'))])
            ->where('transactions.user_id', auth()->user()->id)
            ->where('transactions.active', 1)
            ->where('transacted_items.target_type', 'product')
            ->groupBy('transacted_items.product_id');

        $averageValuesAllProducts = DB::table(DB::raw('(' . $perProductSQL->toSql() . ') as per_product'))
            ->mergeBindings($perProductSQL)
            ->selectRaw('
                product_id,
                positive_amount,
                (total_amount - positive_amount) as negative_amount,
                (positive_amount/negative_quantity) as average_sell_price_per_unit,
                ((total_amount - positive_amount)/(total_quantity - negative_quantity)) as average_buy_price_per_unit
            ');

        $averageValuesGeneral = DB::table(DB::raw('(' . $averageValuesAllProducts->toSql() . ') as per_product'))
            ->mergeBindings($averageValuesAllProducts)
            ->selectRaw('
                AVG(average_sell_price_per_unit) as sell,
                AVG(average_buy_price_per_unit) as buy
            ')->first();

        if(!empty(request()->filter_product)) {
            $averageValuesPerProduct = DB::table(DB::raw('(' . $averageValuesAllProducts->toSql() . ') as per_product'))
                ->mergeBindings($averageValuesAllProducts)
                ->selectRaw('
                    average_sell_price_per_unit as sell,
                    average_buy_price_per_unit as buy
                ')
                ->where('product_id', request()->filter_product)
                ->first();
        }else{
            $averageValuesPerProduct = [(object)['sell' => 0, 'buy' => 0]];
        }
        $chartData = [
            [
                (object)[
                    'label' => 'buy',
                    'value' => abs($averageValuesGeneral->buy ?? 0),
                    'color' => 'blue'
                ],
                (object)[
                    'label' => 'profit',
                    'value' => abs($averageValuesGeneral->sell ?? 0) - abs($averageValuesGeneral->buy ?? 0)
                ]
            ],
            [
                (object)[
                    'label' => 'buy',
                    'value' => abs($averageValuesPerProduct->buy ?? 0),
                    'color' => 'blue'
                ],
                (object)[
                    'label' => 'profit',
                    'value' => abs($averageValuesPerProduct->sell ?? 0) - abs($averageValuesPerProduct->buy ?? 0)
                ]
            ]
        ];

        return response()->json([
            'success' => 1,
            'chartData' => $chartData
        ]);
    }
}
