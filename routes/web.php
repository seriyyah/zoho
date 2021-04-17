<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ZohoCrmController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('zoho');
});

// Zoho Auth
Route::post('zohoauth', [ZohoCrmController::class, 'oauth'])->name('zohoauth');
Route::get('zohotoken', [ZohoCrmController::class, 'generateToken'])->name('zohotoken');
// Zoho Deal
Route::get('deals', [ZohoCrmController::class, 'getDeals'])->name('deals');
Route::post('adddeal', [ZohoCrmController::class, 'addDeal'])->name('adddeal');
