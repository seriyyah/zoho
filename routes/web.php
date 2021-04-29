<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ZohoAuthController;
use App\Http\Controllers\ZohoDealController;

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

// Zoho Auth
Route::get('/', [ZohoAuthController::class, 'view'])->name('/');
Route::post('zohoauth', [ZohoAuthController::class, 'oauth'])->name('zohoauth');
Route::get('zohotoken', [ZohoAuthController::class, 'generateToken'])->name('zohotoken');

// Zoho Deal
Route::get('deals', [ZohoDealController::class, 'getDeals'])->name('deals');
Route::post('adddeal', [ZohoDealController::class, 'addDeal'])->name('adddeal');
