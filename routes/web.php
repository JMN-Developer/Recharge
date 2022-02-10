<?php

use App\Http\Controllers\ApiSettingsController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\RechargeController;
use App\Http\Controllers\ReloadlyController;
use App\Http\Controllers\PpnController;
use App\Http\Controllers\PinController;
use App\Http\Controllers\SimController;
use App\Http\Controllers\OperatorController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\OfferController;
use App\Http\Controllers\CargoController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PhoneController;
use App\Http\Controllers\RetailerController;
use App\Http\Controllers\BalanceController;
use App\Http\Controllers\PricingController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\ApiTestController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DingConnectController;
use App\Http\Controllers\DtOneController;
use App\Http\Controllers\FrontController;
use App\Http\Controllers\InternationalApiController;
use App\Http\Controllers\WalletController;
use App\Http\Controllers\FlightController;
use App\Models\ApiList;
use App\Models\SimOperator;
use App\Models\sim;
use App\Models\User;
use App\Models\Offer;
use App\Models\Slider;
use App\Models\Phone;
use App\Models\Order;
use App\Models\DomesticProduct;
use App\Models\DomesticProfit;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\TransactionController;

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
//Route::view('registration','front.registration-form');
Route::get('check_email',[Usercontroller::class,'check_email']);
Route::get('/',[AuthController::class,'index'])->name('/');
// Route::get('test-notification',[PpnController::class,'send_pin']);

// Route::get('test',[RechargeController::class,'data_test']);

Route::get('error-page', function () {

    return view('error.index');
});

Route::group(['prefix' => 'setting','middleware'=>['auth']], function()
{
    Route::get('/',[SettingsController::class,'index'])->name('setting');
});

Route::get('/sign-up',[UserController::class,'index']);

Route::post('/create',[UserController::class,'create'])->name('create');

Route::middleware(['auth:sanctum', 'verified'])->get('/l', function () {

})->name('/a');
Route::get('/add-reseller', function () {
    return view('front.add-reseller');
});

Route::get('/reseller/edit/{id}',[UserController::class,'edit']);

Route::post('/reseller/delete',[UserController::class,'destroy'])->name('delete-reseller');

Route::post('/reseller/update/{id}',[UserController::class,'update']);

Route::get('/resellers', function () {
    if(Auth::user()->role == 'admin')
    $show = User::where('role','user')->get();
    else{
        $show = User::where('role','user')->where('created_by',Auth::user()->id)->get();
    }
    return view('front.reseller',compact('show'));
});

Route::get('/recharge', function () {
    return view('front.recharge');
});

// Route::get('/sim', function () {
//     return view('front.sim');
// });

Route::post('offer-check', function(Request $request){
    $offer_detail = Offer::where('offer',$request->id)->first();
    return response()->json($offer_detail, 200);
})->name('offer-check');

Route::get('offer-edit/{id}', function($id){
    $operator = SimOperator::all();
    $offer_detail = Offer::where('id',$id)->first();
    return view('front.offer-edit',compact('offer_detail','operator'));
});

Route::post('check-products', function(Request $request){

    $offer_detail = DomesticProduct::where('product', 'like', '%'.$request->id.'%')->where('type','recharge')->get();
    return response()->json($offer_detail, 200);
});
Route::post('check-pins', function(Request $request){

    if($request->id == 'EA')
    {
        $offer_detail = DB::table('domestic_pins')->where('operator', $request->id)->where('type','pin')->get();
    }else {
        $offer_detail = DB::table('domestic_pins')->where('operator', 'like', '%'.$request->id.'%')->where('type','pin')->get();
    }
    return response()->json($offer_detail, 200);
});

Route::get('/operator', [OperatorController::class,'index']);

Route::post('/operator', [OperatorController::class,'store']);

Route::get('/delete-operator/{id}', [OperatorController::class,'destroy']);


Route::get('/offer', [OfferController::class,'index']);

Route::post('/offer', [OfferController::class,'store']);

Route::post('/offer-update/{id}', [OfferController::class,'update']);

Route::get('/delete-offer/{id}', [OfferController::class,'destroy']);




Route::get('/sim-orders', [SimController::class,'orders']);




Route::post('/sim-order/update', [SimController::class,'sim_order_update']);

//  CARGO NEW ORDER


    //  CREATE NEW ORDER



//  PHONE START



Route::get('/phone/add-phone-view', [PhoneController::class,'AddPhoneView'])->name('add-phone-view');

Route::get('/phone/edit/{id}', [PhoneController::class,'phoneedit'])->name('phone-edit');

Route::get('/phone/delete/{id}', [PhoneController::class,'phonedelete'])->name('phone-delete');

Route::post('/phone/add-phone', [PhoneController::class,'AddPhone'])->name('add-phone');

Route::post('/phone/update-phone/{id}', [PhoneController::class,'UpdatePhone'])->name('update-phone');

Route::get('/add-slider-view', [UserController::class,'AddsliderView'])->name('add-sldier-view');

Route::get('/slider-view', [UserController::class,'sliderView'])->name('sldier-view');

Route::get('/slider-edit/{id}', [UserController::class,'slideredit'])->name('slider-edit');

Route::get('/slider-delete/{id}', [UserController::class,'sliderdelete'])->name('slider-delete');

Route::post('/add-slider', [UserController::class,'slider'])->name('add-slider');

Route::post('/edit-slider', [UserController::class,'updateslider'])->name('edit-slider');


Route::post('/phone/update', [PhoneController::class,'updateorder'])->name('update-order');


//  PHONE END

//  RECHARGES START

Route::get('domestic_product', function () {
    if(Auth::check()){
    if(Auth::user()->role == 'admin'){
        return view('front.add-domestic');
    }else{
        return back();
    }
    }else{
        return redirect('login');
    }
});

Route::post('/domestic_product', function (Request $request) {
    $add = new DomesticProfit;
    $add->ean = $request->ean;
    $add->commission = $request->commission;
    $add->save();

    return back();

});

// Route::get('test-event', function () {
//     event(new App\Events\DueRequest('Hello World'));
//     return "Event has been sent!";
// });

Route::get('contact-info', function () {
    return view('front.contact-info');
})->name('contact-info');
Route::get('wallet-request',[WalletController::class,'index'])->name('wallet-request');

Route::get('get-wallet-data',[WalletController::class,'get_wallet_data'])->name('get-wallet-data');
Route::post('amount_request',[WalletController::class,'wallet_request']);
Route::post('approved_amount',[WalletController::class,'approved_amount']);
Route::get('get_requested_amount',[WalletController::class,'get_requested_amount']);

Route::get('wallet_notification_count',[WalletController::class,'wallet_notification_count']);

Route::get('sim_notification_count',[SimController::class,'sim_notification_count']);


Route::group(['prefix' => 'recharge','middleware'=>['auth','user']], function()
{

    Route::get('dingconnect',[DingConnectController::class,'index']);
   Route::get('international', [InternationalApiController::class,'index'])->name('international');


    Route::get('recharge-int', [RechargeController::class,'RechargeInt'])->name('recharge-int');
    // Route::get('international2', [ReloadlyController::class,'index'])->name('recharge-reloadly');
    // Route::get('international3', [PpnController::class,'index'])->name('recharge-ppn');


    Route::post('get_all_invoice',[RechargeController::class,'get_all_invoice'])->name('get_all_invoice');
    Route::get('all-invoice', [RechargeController::class,'invoices'])->name('recharge-invoice');
    Route::get('recharge-italy', [RechargeController::class,'RechargeDom'])->name('recharge-italy');
    Route::get('pin', [PinController::class,'index'])->name('pin');
    Route::get('recharge-gift-card', [RechargeController::class,'RechargeGiftCard'])->name('recharge-gift-card');
    Route::get('recharge-calling-card', [RechargeController::class,'RechargeCallingCard'])->name('recharge-calling-card');
    Route::get('print-all-invoice', [RechargeController::class,'PrintInvoice'])->name('print-all-invoice');
    Route::get('pin/all-invoice', [PinController::class,'invoices'])->name('pin-invoice');
    Route::post('filebytype',[RechargeController::class,'filebytype'])->name('filebytype');
    Route::get('filebydate/{start}/{end}',[RechargeController::class,'filebydate']);
    Route::get('pinfilebydate/{start}/{end}',[RechargeController::class,'pinfilebydate']);
    Route::post('check-operator',[RechargeController::class,'check_operator'])->name('check-operator');
    Route::get('change-operator/{numbers}/{rg}',[RechargeController::class,'change_operator']);
    Route::post('get-price',[RechargeController::class,'get_price'])->name('get-price');
    Route::post('check-product',[RechargeController::class,'get_product'])->name('check-product');
    Route::post('estimate',[RechargeController::class,'estimate'])->name('estimate');
    Route::post('check-changed-product',[RechargeController::class,'get_changed_product'])->name('check-changed-product');
    Route::post('international_recharge',[RechargeController::class,'recharge'])->name('international_recharge');
    Route::post('estimated',[RechargeController::class,'estimate'])->name('estimated');
    Route::post('domestic_recharge',[RechargeController::class,'domestic_recharge'])->name('domestic_recharge');
    Route::get('load_recent_domestic_recharge',[RechargeController::class,'load_recent_domestice_recharge'])->name('load_recent_domestic_recharge');
    Route::get('recharge_invoice/{id}',[RechargeController::class,'invoice']);
    Route::post('domestic_pin',[PinController::class,'store'])->name('domestic-pin');
    Route::get('pin_invoice/{id}',[PinController::class,'invoice']);
    Route::post('reloadly_operator_details',[ReloadlyController::class,'mobile_number_details'])->name('reloadly_operator_details');
    Route::post('ppn_operator_details',[PpnController::class,'mobile_number_details'])->name('ppn_operator_details');
    Route::post('dtone_operator_details',[DtOneController::class,'mobile_number_details'])->name('dtone_operator_details');
    Route::post('reloadly_recharge',[ReloadlyController::class,'reloadly_recharge'])->name('reloadly_recharge');
    Route::post('ppn_recharge',[PpnController::class,'recharge'])->name('ppn_recharge');
    Route::post('dtone_recharge',[DtOneController::class,'recharge'])->name('dtone_recharge');
    Route::post('ding_recharge',[RechargeController::class,'recharge'])->name('ding_recharge');
    Route::post('ppn_pin',[PpnController::class,'pin'])->name('ppn_pin');
    Route::get('calling-card',[PpnController::class,'calling_card_index'])->name('calling-card');
    Route::get('get_white_calling_table',[PpnController::class,'get_white_calling_table'])->name('get_white_calling_table');
    Route::post('send_pin_to_email',[PpnController::class,'send_pin']);
    Route::get('check_daily_duplicate',[RechargeController::class,'check_daily_duplicate']);

});

Route::get('report',[ReportController::class,'index'])->name('report');
Route::post('get_report_data',[ReportController::class,'get_report_data'])->name('get-report-data');
Route::post('get_report_data_separate',[ReportController::class,'get_report_data_separate'])->name('get-report-data-separate');


Route::group(['prefix' => 'sim','middleware'=>['auth','user']], function()
{
    Route::get('sim_edit',[SimController::class,'sim_edit']);
    Route::get('sim-activation', [SimController::class,'index'])->name('sim-activation');
    Route::get('sim-selling', [SimController::class,'orders'])->name('sim-selling');
    Route::get('wi-fi', [SimController::class,'WiFi'])->name('wi-fi');
    Route::get('buy-sim/{id}', [SimController::class,'show']);
    Route::post('buy-sim', [SimController::class, 'buy'])->name('buy-sim');
    Route::get('sim-invoice/{id}', [SimController::class,'invoice'])->name('sim-invoice');
    Route::get('sim-download/{id}', [SimController::class,'download']);
    Route::post('add-sim', [SimController::class,'store']);
    Route::post('update-sim', [SimController::class,'update_sim']);


});


Route::group(['prefix' => 'cargo','middleware'=>['auth','user']], function()
{

    Route::GET('price-edit/{id}', [PricingController::class,'EditPricing']);
    Route::POST('edit-new-pricing-for-real/{id}', [PricingController::class,'EditPricingForReal'])->name('edit-new-pricing-for-real');
    Route::GET('price-delete/{id}', [PricingController::class,'DeletePricing'])->name('price-delete');
    Route::post('order-label/update', [CargoController::class,'Orderlabel']);
    Route::get('order-label/{id}', function($id){
    $get  = Order::where('id',$id)->first();

    return response()->download(public_path('/storage'.'/'.$get->label));
    });

    Route::get('order-tracking', [CargoController::class,'OrderTracking'])->name('order-tracking');
    Route::get('order-invoice/{id}', [CargoController::class,'OrderInvoice'])->name('order-invoice-view');
    Route::get('caorder/cancel/{id}', [CargoController::class,'OrderCancel']);

    Route::get('create-new-order', [CargoController::class,'CreateNewOrder'])->name('create-new-order');
    Route::get('search', [CargoController::class,'Search'])->name('search');
    Route::get('track', [CargoController::class,'OrderTracking'])->name('track');
    Route::GET('add-new-pricing', [PricingController::class,'Pricing'])->name('add-new-pricing');
    Route::POST('add-new-pricing-for-real', [PricingController::class,'AddPricing'])->name('add-new-pricing-for-real');
    Route::GET('pricing-list', [PricingController::class,'PricingTab'])->name('pricing-list');
    Route::get('order/view/{id}', [CargoController::class,'OrderView']);
    Route::get('send-pricing', [PricingController::class,'SendPricing'])->name('send-pricing');
    Route::get('send-pricing-for-docs', [PricingController::class,'SendPricingForDocs'])->name('send-pricing-for-docs');
    Route::get('get-country-by-type', [PricingController::class,'GetCountryByType'])->name('get-country-by-type');
    Route::get('new-order', [CargoController::class,'NewOrderView'])->name('cargo-new-order');
    Route::get('order-list', [CargoController::class,'OrderList'])->name('order-list');
    Route::get('order-tracking-view', [CargoController::class,'OrderTrackingView'])->name('order-tracking-view');
    Route::POST('add-new-order', [OrderController::class,'AddOrder'])->name('add-new-order');
    Route::post('cargo_update',[OrderController::class,'update_status']);

});

Route::get('transaction-history',[TransactionController::class,'index'])->name('transaction-history');

Route::group(['prefix' => 'phone','middleware'=>['auth']], function()
{

    Route::get('phone-order', [PhoneController::class,'PhoneOrder'])->name('phone-order');
    Route::get('selling-list', [PhoneController::class,'SellingList'])->name('selling-list');
    Route::post('phone-order', [PhoneController::class,'order'])->name('add-order');


});

Route::group(['prefix' => 'flights'], function()
{
    Route::get('{any}', function () {
        return view('front.add-flight');
    })->where('any', '.*');

   Route::get('/', [FlightController::class,'add_flight'])->name('add-flight');


});

Route::group(['prefix' => 'retailer','middleware'=>['auth']], function()
{


    Route::get('retailer-details', [RetailerController::class,'RetailerDetail'])->name('retailer-details');

    Route::get('retailer-sign-up', [RetailerController::class,'RetailerSignUp'])->name('retailer-sign-up');

    Route::get('changeStatus', [RetailerController::class,'changeStatus']);

    Route::get('retailer-action', [RetailerController::class,'RetailerAction'])->name('retailer-action');

    Route::get('changeSim', [RetailerController::class,'changeSim']);

    Route::get('changeCargo', [RetailerController::class,'changeCargo']);

    Route::get('changePhone', [RetailerController::class,'changePhone']);

    Route::get('changeReseller', [RetailerController::class,'changeReseller']);

    Route::get('changePin', [RetailerController::class,'changePin']);

    Route::post('add_com',[RetailerController::class,'AddCom'])->name('AddCom');


    Route::get("retailer-details-admin",[RetailerController::class,'retailer_details'])->name('retailer-details-admin')->middleware('admin');


});

Route::group(['prefix' => 'ApiControl','middleware'=>['auth','admin']], function()
{

    Route::get('change_status',[ApiSettingsController::class,'change_status'])->name('change_status');
    Route::get('get_data',[ApiSettingsController::class,'get_data'])->name('get_data');
    Route::get('api-activation', [ApiSettingsController::class,'ApiActivation'])->name('api-activation');



});










//  RECHARGES END

//  SIMS START


//  SIMS END

//  RETAILER START






//  RETAILER END

Route::get('/logout', function(){
    If(Auth::check()){
        Auth::logout();
    }else{

    }
    return redirect('/');
});




// edit by shuvo
Route::get('/fcm', [RechargeController::class,'fcmSend']);

Route::middleware(['auth:sanctum', 'verified'])->get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');



Route::group(['middleware'=>['auth','admin']], function()
{
    Route::post('/add_balance',[BalanceController::class,'AddBalance'])->name('AddBalance');

    Route::post('/add_cargo_due',[BalanceController::class,'AddDue']);

    Route::post('/edit_cargo_due',[BalanceController::class,'EditDue']);

    Route::post('/edit_sim_due',[BalanceController::class,'SimDue']);



    Route::post('/edit_limit',[BalanceController::class,'EditLimit'])->name('EditLimit');
    Route::post('/edit_limit_domestic',[BalanceController::class,'EditLimitDomestic'])->name('EditLimitDomestic');

    Route::get('/change-phone-price', [BalanceController::class,'PriceDiscount']);

    Route::post('/edit_wallet',[BalanceController::class,'edit_wallet']);



});



