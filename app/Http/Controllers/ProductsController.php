<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use App\Models\Products;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ProductsController extends Controller
{
    // Metoda pentru afișarea paginii principale a produselor
    public static function main()
    {
        // Obține produsele active ale utilizatorului curent
        $products = Products::where('user_id', auth()->user()->id)->where('active', 1)->get();
        return view('products.main', ['products' => $products]);
    }

    // Metoda pentru furnizarea listei de produse într-un format JSON paginat
    public static function list()
    {
        $products = Helper::getPaginatedProducts();
        return response()->json([
            'success' => 1,
            'current_page' => $products->currentPage(),
            'html' => view('products.list', ['products' => $products])->render()
        ]);
    }

    // Metoda pentru obținerea detaliilor unui produs specific
    public static function get($id)
    {
        // Obține informații despre un produs în funcție de ID și utilizatorul curent
        $product = Products::where('user_id', auth()->user()->id)->where('active', 1)->find($id);
        // Returnează răspunsul JSON cu succesul și detaliile produsului
        return response()->json([
            'success' => $product ? 1 : 0,
            'product' => $product
        ]);
    }
    // Metoda privată pentru salvarea unui produs, fie prin adăugare, fie prin editare
    private static function saveProduct($id = 0)
    {
        try {
            // Construiește datele pentru validarea și salvarea produsului
            $data = [
                'user_id' => auth()->user()->id,
                'name' => request()->name,
                'sku' => request()->sku,
            ];
            // Dacă există un ID, obține produsul curent pentru a gestiona actualizarea
            if ($id) {
                $currentProduct = Products::find($id);
            }
            // Definirea regulilor de validare pentru datele produsului
            $rules = [
                'name' => ['required', 'string', 'max:255'],
                'sku' => ['required', 'string', 'max:255', 'unique:products,sku' . ($id ? ',' . $currentProduct->id : '')],
            ];
            // Mesaje personalizate de eroare pentru regulile de validare
            $errorMessages = [
                'name' => __('The <strong>name</strong> field is required'),
                'sku' => __('The <strong>sku</strong> field is required'),
                'sku.unique' => __('The sku needs to be unique')
            ];
            // Validează datele folosind regulile definite
            $validator = Validator::make($data, $rules, $errorMessages);
            // Verifică dacă validarea a eșuat și returnează un răspuns JSON corespunzător
            if ($validator->fails()) {
                return response()->json([
                    'success' => 0,
                    'messages' => $validator->errors(),
                ]);
            } else {
                // Dacă validarea a trecut, salvează sau actualizează produsul în funcție de existența unui ID
                if (!empty($id)) {
                    DB::table('products')->where('id', $id)->where('user_d', auth()->user()->id)->update($data);
                } else {
                    DB::table('products')->insert($data);
                }
                // Obține produsele paginate după salvare pentru a actualiza lista
                $products = Helper::getPaginatedProducts();
                // Returnează un răspuns JSON cu succesul, mesajele și HTML-ul actualizat pentru lista de produse
                return response()->json([
                    'success' => 1,
                    'messages' => [__('Product ' . (empty($id) ? 'created' : 'updated') . ' successfully!')],
                    'html' => view('products.list', ['products' => $products])->render()
                ]);
            }
        } catch(\Exception $e) {
            // Gestionează cazul în care apare o excepție și înregistrează eroarea în jurnalul de erori
            Log::error('Add/Edit Product: ' . $e->getMessage());
        }
        // Returnează un răspuns JSON în cazul în care ceva a mers greșit în timpul adăugării sau actualizării produsului
        return response()->json([
            'success' => 0,
            'errors' => 1,
            'messages' => [__('Something went wrong while ' . (empty($id) ? 'adding' : 'updating') . ' the product')]
        ]);
    }
    // Metodă pentru adăugarea unui produs
    public static function add()
    {
        // Folosește metoda privată pentru salvarea produsului (adăugare)
        return self::saveProduct();
    }

    public static function edit($id)
    {
        // Folosește metoda privată pentru salvarea produsului (editare)
        return self::saveProduct($id);
    }
    // Metodă pentru ștergerea unui produs
    public static function delete($id)
    {
        // Marchează produsul ca inactiv în loc să-l șteargă fizic
        Products::where('id', $id)->where('user_id', auth()->user()->id)->update(['active' => 0]);
        // Obține produsele paginate după ștergere pentru a actualiza lista
        $products = Helper::getPaginatedProducts();
        // Returnează un răspuns JSON cu succesul, mesajele și HTML-ul actualizat pentru lista de produse
        return response()->json([
            'success' => 1,
            'messages' => [__('Product has been deleted successfully!')],
            'html' => view('products.list', ['products' => $products])->render()
        ]);
    }
}
