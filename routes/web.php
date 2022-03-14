<?php

use App\Models\Web\package;
use App\Models\Web\PackgeRequest;
use Illuminate\Support\Facades\DB;

header('Content-Type: text/html; charset=utf-8');
if(file_exists(storage_path('installed'))){
	$check = DB::table('settings')->where('id', 94)->first();
	if($check->value == 'Maintenance'){
		$middleware = ['installer','env'];
	}
	else{
		$middleware = ['installer'];
	}
}
else{
	$middleware = ['installer'];
}
Route::get('/maintance','Web\IndexController@maintance');

//reset link request routes...
// Route::get('password/email', 'Auth\ForgotPasswordController@showLinkRequestForm')->name('password.email');
// Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail');

// // Password reset routes...
// Route::get('password/reset/{token}', 'Auth\ResetPasswordController@showResetForm')->name('password.request');
// Route::post('password/reset', 'Auth\ResetPasswordController@postReset')->name('password.reset');

Route::group(['namespace' => 'Web','middleware' => ['installer']], function () {
Route::get('/login', 'CustomersController@login');
Route::post('/process-login', 'CustomersController@processLogin');
Route::get('/logout', 'CustomersController@logout')->middleware('Customer');
});
Route::get('/','AdminControllers\AdminController@dashboard')->name('set_home')->middleware('auth:web');

	Route::get('/test', 'Web\IndexController@test1');
	
	Auth::routes();

	Route::get('/password/success', function(){
    $user = Auth::user();

    if($user){
        Auth::logout();
        
        return view('auth.success');
    }

    return view('auth.success');
});

Route::get('/payment/success/{id}', function($id){
    PackgeRequest::find($id)->update(['payment_status'=>1]);
    return view('web.success');
})->name('payment_success');

Route::get('order/payment/success/{id}/{cart_id}', function($id,$cart_id){
    DB::table('orders')->where('orders_id',$id)->update(['payment_status'=>1]);
	$deleteCart = DB::table('cart_product')->where('cart_id', '=', $cart_id)->delete();
    return view('web.success');
})->name('order_payment_success');