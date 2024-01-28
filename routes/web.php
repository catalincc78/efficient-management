<?php

use Illuminate\Support\Facades\Route;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/





Auth::routes();
Route::get('/profile', 'Auth\RegisterController@showRegistrationForm')->name('profile');
Route::middleware(['auth'])->group(function () {
    Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
    Route::get('/products', 'ProductsController@main')->name('product.main');
    Route::prefix('product')->group(function () {
        Route::get('/', 'ProductsController@list')->name('product.list');
        Route::get('/{id}', 'ProductsController@get')->name('product.get');
        Route::post('/', 'ProductsController@add')->name('product.add');
        Route::put('/{id}', 'ProductsController@edit')->name('product.edit');
        Route::delete('/{id}', 'ProductsController@delete')->name('product.delete');
    });

    Route::get('/transactions', 'TransactionsController@main')->name('transaction.main');
    Route::prefix('transaction')->group(function () {
        Route::get('/', 'TransactionsController@list')->name('transaction.list');
        Route::get('/{id}', 'TransactionsController@get')->name('transaction.get');
        Route::post('/', 'TransactionsController@add')->name('transaction.add');
        Route::put('/{id}', 'TransactionsController@edit')->name('transaction.edit');
        Route::delete('/{id}', 'TransactionsController@delete')->name('transaction.delete');
    });

    Route::get('/statistics', 'StatisticsController@main')->name('statistic.main');
    Route::get('/validate-vat', 'StatisticsController@validateVAT')->name('statistic.check-vat');
    Route::prefix('statistic')->group(function () {
        Route::get('/chart-daily-amount', 'StatisticsController@chartDailyAmount')->name('statistic.chart.daily-amount');
        Route::get('/chart-daily-stock', 'StatisticsController@chartDailyStock')->name('statistic.chart.daily-stock');
        Route::get('/chart-total-amount', 'StatisticsController@chartTotalAmount')->name('statistic.chart.total-amount');
        Route::get('/chart-profit-per-product', 'StatisticsController@chartProfitPerProduct')->name('statistic.chart.profit-per-product');
    });
});
