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
    // Metodă pentru afișarea paginii principale de statistici
    public static function main(){
        // Obține produsele active ale utilizatorului curent și tipul de utilizator
        $products =  Products::where('user_id', auth()->user()->id)->where('active', 1)->orderBy('name')->get();
        $user = User::where('id', auth()->user()->id)->select('type', 'cif')->first();

        // Returnează view-ul 'statistics.main' cu datele obținute
        return view('statistics.main', ['products' => $products, 'user' => $user]);
    }

    // Metodă pentru validarea unui număr de TVA folosind serviciul VIES
    public static function validateVAT($companyVat = 'RO25323520') {
        // Extrage codul țării și numărul de TVA din string
        $countryCode = substr($companyVat, 0, 2);
        $vatNumber = substr($companyVat, 2);

        // URL-ul serviciului VIES
        $url = 'http://ec.europa.eu/taxation_customs/vies/checkVatService.wsdl';
        $client = new \SoapClient($url);

        $action = 'urn:checkVat';
        $client->__setSoapHeaders(new \SoapHeader($url, 'Action', $action));

        // Construiește cererea SOAP
        $request = [
            'vatNumber' => $vatNumber,
            'countryCode' => $countryCode,
        ];

        try {
            // Efectuează apelul SOAP și returnează rezultatele
            $operation = 'checkVat';
            $result = $client->__soapCall($operation, [$request]);

            // Verifică dacă numărul de TVA este valid și întoarce rezultatul
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

    // Metodă pentru generarea datelor necesare pentru diagrama zilnică a sumelor
    public static function chartDailyAmount(){
        // Obține datele disponibile pentru diagrama
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

        // Inițializează un interval de date și generează structura inițială a datelor pentru diagramă
        $interval = new DateInterval('P1D');
        $date_range = new DatePeriod(date_create(request()->filter_date_start), $interval, date_create(gmdate('Y-m-d H:i:s', strtotime(request()->filter_date_end.' + 1 day'))));

        $chartData = [];

        foreach ($date_range as $date) {
            $chartData[$date->format('Y-m-d')] = (object)[
                'label' => $date->format('Y-m-d'),
                'value' => 0
            ];
        }

        // Actualizează datele cu valorile disponibile
        foreach($availableData as $data) {
            $chartData[$data->date] = (object)[
                'label' => $data->date,
                'value' => $data->total_amount
            ];
        }

        // Returnează datele în format JSON
        return response()->json([
            'success' => 1,
            'chartData' => [array_values($chartData)]
        ]);
    }

    public static function chartDailyStock(){

    }
    public static function chartTotalAmount(){

    }

    // Metodă pentru generarea datelor necesare pentru diagrama profitului per produs
    public static function chartProfitPerProduct(){
        // Construiește interogarea pentru obținerea valorilor necesare
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

        // Construiește interogarea pentru obținerea valorilor medii
        $averageValuesAllProducts = DB::table(DB::raw('(' . $perProductSQL->toSql() . ') as per_product'))
            ->mergeBindings($perProductSQL)
            ->selectRaw('
                product_id,
                positive_amount,
                (total_amount - positive_amount) as negative_amount,
                (positive_amount/negative_quantity) as average_sell_price_per_unit,
                ((total_amount - positive_amount)/(total_quantity - negative_quantity)) as average_buy_price_per_unit
            ');

        // Obține valorile medii generale
        $averageValuesGeneral = DB::table(DB::raw('(' . $averageValuesAllProducts->toSql() . ') as per_product'))
            ->mergeBindings($averageValuesAllProducts)
            ->selectRaw('
                AVG(average_sell_price_per_unit) as sell,
                AVG(average_buy_price_per_unit) as buy
            ')->first();

        // Obține valorile medii pentru un produs specific (dacă este specificat)
        if(!empty(request()->filter_product)) {
            $averageValuesPerProduct = DB::table(DB::raw('(' . $averageValuesAllProducts->toSql() . ') as per_product'))
                ->mergeBindings($averageValuesAllProducts)
                ->selectRaw('
                    average_sell_price_per_unit as sell,
                    average_buy_price_per_unit as buy
                ')
                ->where('product_id', request()->filter_product)
                ->first();
        } else {
            $averageValuesPerProduct = [(object)['sell' => 0, 'buy' => 0]];
        }

        // Generează datele necesare pentru diagramă
        $chartData = [
            self::generateDataSets($averageValuesGeneral),
            self::generateDataSets($averageValuesPerProduct)
        ];

        // Returnează datele în format JSON
        return response()->json([
            'success' => 1,
            'chartData' => $chartData
        ]);
    }

    // Metodă privată pentru generarea seturilor de date necesare pentru diagrama profitului per produs
    private static function generateDataSets($averageValues){
        // Calculează profitul și crează seturile de date corespunzătoare
        $profit = abs($averageValues->sell ?? 0) - abs($averageValues->buy ?? 0);
        $datasetProfit =  (object)[
            'label' => $profit > 0 ? 'profit' : 'loss',
            'value' => abs($profit),
            'color' => $profit > 0 ? 'green' : 'red'
        ];
        $datasetX = (object)[
            'label' => $profit > 0 ? 'buy' : 'sell',
            'value' => $profit > 0 ? abs($averageValues->buy ?? 0) : abs($averageValues->sell ?? 0) ,
            'color' => $profit > 0 ? 'yellow' : 'blue'
        ];

        // Returnează seturile de date în funcție de direcția profitului
        return $profit > 0 ? [$datasetProfit, $datasetX] : [$datasetX, $datasetProfit] ;
    }
}
