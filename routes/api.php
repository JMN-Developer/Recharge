<?php

use App\Http\Controllers\ApiTestController;
use App\Http\Controllers\BangladeshRechargeController;
use App\Http\Controllers\DtOneController;
use App\Http\Controllers\RechargeController;
use App\Models\Offer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
 */

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::post('/recharge', [RechargeController::class, 'index']);

// edit by shuvo
Route::post('/send', [RechargeController::class, 'fcmSend']);

Route::post('/operator', [RechargeController::class, 'operator']);

Route::post('/response', [RechargeController::class, 'response']);

Route::post('offer-check', function (Request $request) {
    $offer_detail = Offer::where('offer', $request->id)->first();
    return response()->json($offer_detail, 200);
});

Route::get('get_balance', [ApiTestController::class, 'get_balance']);
Route::post('check_operator', [ApiTestController::class, 'check_operator']);
Route::post('get_products', [ApiTestController::class, 'get_products']);

Route::get('epay-transaction-list', [ApiTestController::class, 'epay_transaction_list']);
Route::get('dtone-transaction', [DtOneController::class, 'all_transaction']);
Route::get('international-transaction-details/{transaction_id}', [DtOneController::class, 'transaction_details']);
Route::get('ean_profit', [ApiTestController::class, 'ean_profit']);
Route::post('bd_query_recharge', [BangladeshRechargeController::class, 'query_recharge']);
Route::get('test', [ApiTestController::class, 'test']);
Route::get('bangladeshi_balance', [BangladeshRechargeController::class, 'check_balance']);
Route::get('epay_transaction_cancel', [ApiTestController::class, 'epay_transaction_cancel']);
