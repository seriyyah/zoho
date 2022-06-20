<?php

use App\Http\Controllers\ZohoLeadsController;
use com\zoho\crm\api\record\Leads;
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
Route::get('/', [ZohoAuthController::class, 'index'])->name('home');
Route::post('zoho-auth', [ZohoAuthController::class, 'authZoho'])->name('zoho_auth');
Route::get('zoho-token-generate', [ZohoAuthController::class, 'generateToken'])->name('zoho-token-generate');

// Zoho Deal
Route::get('deals', [ZohoDealController::class, 'getDeals'])->name('deals');
Route::post('add-deal', [ZohoDealController::class, 'addDeal'])->name('add-deal');

//Zoho Leads

Route::get('leads', [ZohoLeadsController::class, 'index'])->name('leads');
Route::post('add-leads', [ZohoLeadsController::class, 'store'])->name('add-leads');
Route::get('edit-lead', [ZohoLeadsController::class, 'show'])->name('edit-lead');
