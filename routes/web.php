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
Route::group(['namespace' => 'Web','middleware' => $middleware], function () {
	Route::get('general_error/{msg}', function($msg) {
		 return view('errors.general_error',['msg' => $msg]);
	});
	// route for to show payment form using get method
		Route::get('pay', 'RazorpayController@pay')->name('pay');
    	Route::post('/paytm-callback', 'PaytmController@paytmCallback');
		Route::get('/store_paytm', 'PaytmController@store');
		// route for make payment request using post method
		Route::post('dopayment', 'RazorpayController@dopayment')->name('dopayment');

		Route::get('/','IndexController@index')->name('set_home');
		Route::post('/change_language', 'WebSettingController@changeLanguage');
		Route::post('/change_currency', 'WebSettingController@changeCurrency');
		Route::post('/addToCart', 'CartController@addToCart');
		Route::post('/addToCartFixed', 'CartController@addToCartFixed');
		Route::post('/addToCartResponsive', 'CartController@addToCartResponsive');
		
		Route::post('/modal_show', 'ProductsController@ModalShow');
		Route::post('/reviews', 'ProductsController@reviews');
		Route::get('/deleteCart', 'CartController@deleteCart');
		Route::get('/viewcart', 'CartController@viewcart');
		Route::get('/editcart/{id}/{slug}', 'CartController@editcart');
		Route::post('/updateCart', 'CartController@updateCart');
		Route::post('/updatesinglecart', 'CartController@updatesinglecart');
		Route::get('/cartButton', 'CartController@cartButton');

		Route::get('/profile', 'CustomersController@profile')->middleware('Customer');
		Route::get('/change-password', 'CustomersController@changePassword')->middleware('Customer');
		
		Route::get('/wishlist', 'CustomersController@wishlist')->middleware('Customer');
		Route::post('/updateMyProfile', 'CustomersController@updateMyProfile')->middleware('Customer');
		Route::post('/updateMyPassword', 'CustomersController@updateMyPassword')->middleware('Customer');
		Route::get('UnlikeMyProduct/{id}', 'CustomersController@unlikeMyProduct')->middleware('Customer');
		Route::post('likeMyProduct', 'CustomersController@likeMyProduct');
		Route::post('addToCompare', 'CustomersController@addToCompare');
		Route::get('compare', 'CustomersController@Compare')->middleware('Customer');
		Route::get('deletecompare/{id}', 'CustomersController@DeleteCompare')->middleware('Customer');
		Route::get('/orders', 'OrdersController@orders')->middleware('Customer');
		Route::get('/view-order/{id}', 'OrdersController@viewOrder')->middleware('Customer');
		Route::post('/updatestatus/', 'OrdersController@updatestatus')->middleware('Customer');
		Route::get('/shipping-address', 'ShippingAddressController@shippingAddress')->middleware('Customer');
		Route::post('/addMyAddress', 'ShippingAddressController@addMyAddress')->middleware('Customer');
		Route::post('/myDefaultAddress', 'ShippingAddressController@myDefaultAddress')->middleware('Customer');
		Route::post('/update-address', 'ShippingAddressController@updateAddress')->middleware('Customer');
		Route::get('/delete-address/{id}', 'ShippingAddressController@deleteAddress')->middleware('Customer');
		Route::post('/ajaxZones', 'ShippingAddressController@ajaxZones');
		//news section
		Route::get('/news', 'NewsController@news');
		Route::get('/news-detail/{slug}', 'NewsController@newsDetail');
		Route::post('/loadMoreNews', 'NewsController@loadMoreNews');
		Route::get('/page', 'IndexController@page');
		Route::get('/shop', 'ProductsController@shop');
		Route::post('/shop', 'ProductsController@shop');
		Route::get('/product-detail/{slug}', 'ProductsController@productDetail');
		Route::post('/filterProducts', 'ProductsController@filterProducts');
		Route::post('/getquantity', 'ProductsController@getquantity');
		Route::post('/getProductDetailsById', 'ProductsController@getProductDetailsById');

		Route::get('/guest_checkout', 'OrdersController@guest_checkout');
		Route::get('/checkout', 'OrdersController@checkout')->middleware('Customer')->name('checkout');
		Route::post('/checkout_shipping_address', 'OrdersController@checkout_shipping_address')->middleware('Customer');
		Route::post('/checkout_billing_address', 'OrdersController@checkout_billing_address')->middleware('Customer');
		Route::post('/checkout_payment_method', 'OrdersController@checkout_payment_method')->middleware('Customer');
		Route::post('/paymentComponent', 'OrdersController@paymentComponent')->middleware('Customer');
		Route::get('/checkout_tap_payment', 'OrdersController@checkout_tap_payment')->middleware('Customer')->name('checkout_tap_payment');
		Route::post('/place_order', 'OrdersController@place_order')->middleware('Customer')->name('place_order');
		Route::get('/orders', 'OrdersController@orders')->middleware('Customer');
		Route::post('/updatestatus/', 'OrdersController@updatestatus')->middleware('Customer');
		Route::post('/myorders', 'OrdersController@myorders')->middleware('Customer');
		Route::get('/stripeForm', 'OrdersController@stripeForm')->middleware('Customer');
		Route::get('/view-order/{id}', 'OrdersController@viewOrder')->middleware('Customer');
		Route::post('/pay-instamojo', 'OrdersController@payIinstamojo')->middleware('Customer');
		Route::get('/thankyou', 'OrdersController@thankyou')->middleware('Customer');

		//paystack
		Route::get('/paystack/transaction', 'OrdersController@paystackTransaction')->middleware('Customer');
		Route::get('/paystack/verify/transaction', 'OrdersController@authorizepaystackTransaction')->middleware('Customer');
		
		//paystack
		Route::get('/midtrans/transaction', 'MidtransController@midtransTransaction')->middleware('Customer');
		// Route::get('/midtrans/verify/transaction', 'OrdersController@authorize<idtransTransaction')->middleware('Customer');
		
		Route::get('/checkout/hyperpay', 'OrdersController@hyperpay')->middleware('Customer');
		Route::get('/checkout/hyperpay/checkpayment', 'OrdersController@checkpayment')->middleware('Customer');
		Route::post('/checkout/payment/changeresponsestatus', 'OrdersController@changeresponsestatus')->middleware('Customer');
		Route::post('/apply_coupon', 'CartController@apply_coupon');
		Route::get('/removeCoupon/{id}', 'CartController@removeCoupon')->middleware('Customer');

		Route::get('/signup', 'CustomersController@signup');
		Route::get('/logoutt', 'CustomersController@logout')->middleware('Customer');
		Route::post('/signupProcess', 'CustomersController@signupProcess');
		Route::get('/forgotPassword', 'CustomersController@forgotPassword');
		Route::get('/recoverPassword', 'CustomersController@recoverPassword');
		Route::post('/processPassword', 'CustomersController@processPassword');


		Route::get('login/{social}', 'CustomersController@socialLogin');
		Route::get('login/{social}/callback', 'CustomersController@handleSocialLoginCallback');
		Route::post('/commentsOrder', 'OrdersController@commentsOrder');
		Route::post('/subscribeNotification/', 'CustomersController@subscribeNotification');
		Route::get('/contact', 'IndexController@contactus');
		Route::post('/processContactUs', 'IndexController@processContactUs');
		
		Route::get('/setcookie', 'IndexController@setcookie');
		Route::get('/newsletter', 'IndexController@newsletter');

		Route::get('/subscribeMail', 'IndexController@subscribeMail');

		Route::post('/get_brands', 'ProductsController@getBrands')->name('get_brands');

		//Like Card
		Route::get('/like_card_index', 'LikeCardController@index')->name('like_card');
		Route::get('/like_card_test', 'LikeCardController@index_one');
		Route::get('/like_card_two', 'LikeCardController@index_two');
		Route::get('/like_card_balance', 'LikeCardController@index_balance');
		Route::get('getAllShops','shopController@getShops')->name('getShops');
		Route::get('shop/{id}/products','shopController@shopGetProducts')->name('shop.products');
		Route::get('shop/{id}/groups','shopController@shopGetGroups')->name('shop.groups');
		Route::get('Become_merchant_with_us','shopController@Become_merchant_with_us')->name('Become_a_merchant_with_us');
		Route::get('packge/{type}','shopController@getPackge')->name('getPackge')->middleware('Customer');
		Route::get('checkout/{id}/{month}','shopController@checkout')->name('packg.checkout')->middleware('Customer');
		Route::post('charge','shopController@charge')->name('packge.charge');

	});

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