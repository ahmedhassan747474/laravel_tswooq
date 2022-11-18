<?php
header('Content-Type: text/html; charset=utf-8');
use Illuminate\Http\Request;

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

Route::get('/getupdates', function(){
    // $updates = DB::table('updates')->get();
    return response()->json(['num'=>1, "ios"=>"sss", 'android'=>"sss"]);
});

Route::middleware('auth:api', 'cors')->get('/user', function (Request $request) {
    return $request->user();
});




/*
	|--------------------------------------------------------------------------
	| App Controller Routes
	|--------------------------------------------------------------------------
	|
	| This section contains all Routes of application
	|
	|
*/

Route::group(['namespace' => 'App','middleware'=>'cors'], function () {

	//Route::post('/uploadimage', 'AppSettingController@uploadimage');

	Route::post('/getcategories', 'CategoriesController@getcategories');

	//registration url
	Route::post('/registerdevices', 'CustomersController@registerdevices');

	//processregistration url
	Route::post('/processregistration', 'CustomersController@processregistration');

	//update customer info url
	Route::post('/updatecustomerinfo', 'CustomersController@updatecustomerinfo');
	Route::get('/updatepassword', 'CustomersController@updatepassword');

	// login url
	Route::post('/processlogin', 'CustomersController@processlogin');

	//social login
	Route::post('/facebookregistration', 'CustomersController@facebookregistration');
	Route::post('/googleregistration', 'CustomersController@googleregistration');

	//push notification setting
	Route::post('/notify_me', 'CustomersController@notify_me');

	// forgot password url
	Route::post('/processforgotpassword', 'CustomersController@processforgotpassword');

	/*
	|--------------------------------------------------------------------------
	| Location Controller Routes
	|--------------------------------------------------------------------------
	|
	| This section contains countries shipping detail
	| This section contains links of affiliated to address
	|
	*/

	//get country url
	Route::post('/getcountries', 'LocationController@getcountries');

	//get zone url
	Route::post('/getzones', 'LocationController@getzones');

	//get all address url
	Route::post('/getalladdress', 'LocationController@getalladdress');

	//address url
	Route::post('/addshippingaddress', 'LocationController@addshippingaddress');

	//update address url
	Route::post('/updateshippingaddress', 'LocationController@updateshippingaddress');

	//update default address url
	Route::post('/updatedefaultaddress', 'LocationController@updatedefaultaddress');

	//delete address url
	Route::post('/deleteshippingaddress', 'LocationController@deleteshippingaddress');

	/*
	|--------------------------------------------------------------------------
	| Product Controller Routes
	|--------------------------------------------------------------------------
	|
	| This section contains product data
	| Such as:
	| top seller, Deals, Liked, categroy wise or category individually and detail of every product.
	*/


	//get categories
	// Route::post('/allcategories', 'MyProductController@allcategories');

	//getAllProducts
	// Route::post('/getallproducts', 'MyProductController@getallproducts');

	//like products
	// Route::post('/likeproduct', 'MyProductController@likeproduct');

	//unlike products
	// Route::post('/unlikeproduct', 'MyProductController@unlikeproduct');

	//get filters
	// Route::post('/getfilters', 'MyProductController@getfilters');

	//get getFilterproducts
	Route::post('/getfilterproducts', 'MyProductController@getfilterproducts');

	Route::post('/getsearchdata', 'MyProductController@getsearchdata');

	//getquantity
	Route::post('/getquantity', 'MyProductController@getquantity');


	/*
	|--------------------------------------------------------------------------
	| News Controller Routes
	|--------------------------------------------------------------------------
	|
	| This section contains news data
	| Such as:
	| top news or category individually and detail of every news.
	*/


	//get categories
	Route::post('/allnewscategories', 'NewsController@allnewscategories');

	//getAllProducts
	Route::post('/getallnews', 'NewsController@getallnews');

	/*
	|--------------------------------------------------------------------------
	| Cart Controller Routes
	|--------------------------------------------------------------------------
	|
	| This section contains customer orders
	|
	*/

	//hyperpaytoken
	Route::post('/hyperpaytoken', 'OrderController@hyperpaytoken');

	//hyperpaytoken
	Route::get('/hyperpaypaymentstatus', 'OrderController@hyperpaypaymentstatus');

	//paymentsuccess
	Route::get('/paymentsuccess', 'OrderController@paymentsuccess');

	//paymenterror
	Route::post('/paymenterror', 'OrderController@paymenterror');

	//generateBraintreeToken
	Route::get('/generatebraintreetoken', 'OrderController@generatebraintreetoken');

	//generateBraintreeToken
	Route::get('/instamojotoken', 'OrderController@instamojotoken');

	//add To order
	// Route::post('/addtoorder', 'OrderController@addtoorder');

	//updatestatus
	// Route::post('/updatestatus/', 'OrderController@updatestatus');

	//get all orders
	// Route::post('/getorders', 'OrderController@getorders');

	//get default payment method
	Route::post('/getpaymentmethods', 'OrderController@getpaymentmethods');

	//get shipping / tax Rate
	Route::post('/getrate', 'OrderController@getrate');

	//get Coupon
	Route::post('/getcoupon', 'OrderController@getcoupon');

	//paytm hash key
	Route::get('/generatpaytmhashes', 'OrderController@generatpaytmhashes');

	/*
	|--------------------------------------------------------------------------
	| Banner Controller Routes
	|--------------------------------------------------------------------------
	|
	| This section contains banners, banner history
	|
	*/

	//get banners
	Route::get('/getbanners', 'BannersController@getbanners');

	//banners history
	Route::post('/bannerhistory', 'BannersController@bannerhistory');

	/*
	|--------------------------------------------------------------------------
	| App setting Controller Routes
	|--------------------------------------------------------------------------
	|
	| This section contains app  languages
	|
	*/
	Route::get('/sitesetting', 'AppSettingController@sitesetting');


	//old app label
	Route::post('/applabels', 'AppSettingController@applabels');

	//new app label
	Route::get('/applabels3', 'AppSettingController@applabels3');
	Route::post('/contactus', 'AppSettingController@contactus');
	Route::get('/getlanguages', 'AppSettingController@getlanguages');


	/*
	|--------------------------------------------------------------------------
	| Page Controller Routes
	|--------------------------------------------------------------------------
	|
	| This section contains news data
	| Such as:
	| top Page individually and detail of every Page.
	*/

	//getAllPages
	Route::post('/getallpages', 'PagesController@getallpages');


  /*
	|--------------------------------------------------------------------------
	| reviews Controller Routes
	|--------------------------------------------------------------------------
 */

   Route::post('/givereview', 'ReviewsController@givereview');
   Route::post('/updatereview', 'ReviewsController@updatereview');
   Route::get('/getreviews', 'ReviewsController@getreviews');

  /*
  |--------------------------------------------------------------------------
  | current location Controller Routes
  |--------------------------------------------------------------------------
  */

  Route::get('/getlocation', 'AppSettingController@getlocation');

  /*
  |--------------------------------------------------------------------------
  | currency location Controller Routes
  |--------------------------------------------------------------------------
  */

  Route::get('/getcurrencies', 'AppSettingController@getcurrencies');

});

Route::group(['namespace' => 'Api\V1', 'prefix' => 'v1','middleware'=>'cors'], function(){

		Route::get('getAllShops','shopController@getShops')->name('getShops');
		Route::get('become_merchant_with_us','shopController@Become_merchant_with_us')->name('Become_a_merchant_with_us');
		Route::post('checkout','shopController@checkout')->middleware('user');
	

	// //database
    // Route::get('backup', 'UserController@backup');
    // Route::get('drop', 'UserController@dropDB');

    //Auth
    Route::post('sign_in', 'UserController@login');
    Route::post('sign_up', 'UserController@registration');
    Route::post('forget_password', 'UserController@forgetPassword');
    Route::post('change_password', 'UserController@changePassword');
	Route::post('sign_with_social_media', 'UserController@RegisterAndLoginSocailMedia');
    Route::post('sign_with_social', 'UserController@loginWithSocial');
    Route::post('active_phone_number', 'UserController@verifyPhoneNumber');
    Route::post('delete_account', 'UserController@delete_account');

	//vendors 
    Route::get('get_vendors', 'UserController@get_vendors');

	//Product
	Route::post('/get_categories', 'ProductController@allcategories');
	Route::post('/get_brands', 'ProductController@allbrands');
	Route::post('/get_brands_by_category', 'ProductController@allbrandsbycategory');
	Route::post('/getallproducts', 'ProductController@getallproducts');
	Route::get('/get_all_group_products', 'ProductController@get_all_group_products');
	Route::get('/get_groups_by_vendor', 'ProductController@get_groups_by_vendor');
	Route::get('/get_category_by_vendor', 'ProductController@get_category_by_vendor');
	Route::get('/get_all_groups', 'ProductController@get_all_groups');
	Route::get('/get_all_groups_new', 'ProductController@get_all_groups_new');
	Route::post('/getproductsbycategory', 'ProductController@getproductsbycategory');
	Route::post('/getproductsbycategoryAndVendore', 'ProductController@getproductsbycategoryAndVendore');
	Route::post('/getfilters', 'ProductController@getfilters');
	Route::post('/getfilterproducts', 'ProductController@getfilterproducts');
    Route::post('/getproductsbybrand', 'ProductController@getproductsbybrand');
    Route::post('/getproductbyid', 'ProductController@getproductbyid');
	Route::post('/getsliders', 'SliderController@getsliders');


	Route::post('/getpaymentmethods', 'OrderController@getPaymentMethods');

    //like card
	Route::post('/get_like_card_categories', 'LikeCardController@categories');
	Route::post('/search', 'LikeCardController@search');
	Route::post('/products', 'LikeCardController@products');

    Route::group(['middleware' => 'user'], function(){
        
		//Auth
        Route::post('update_profile', 'UserController@updateProfile');
        Route::post('logout', 'UserController@logout');
        Route::get('get_profile', 'UserController@getProfile');
        Route::post('send_active_email', 'UserController@sendActiveEmail');

		//Product
		Route::post('/likeproduct', 'ProductController@likeproduct');
		Route::post('/unlikeproduct', 'ProductController@unlikeproduct');
        Route::post('/getfavourites', 'ProductController@getproductsyfavourite');

		//Cart
		Route::post('/get_cart_like', 'OrderController@getcartlike');
		Route::post('/get_cart', 'OrderController@getcart');
		Route::post('/add_to_cart/', 'OrderController@addtocart');
		Route::post('/edit_cart', 'OrderController@editcart');
		Route::post('/delete_cart', 'OrderController@deletecart');

		//Order
		Route::post('/addtoorder', 'OrderController@addtoorder');
		Route::post('/addtopos', 'OrderController@addtopos');
		Route::post('/cancelorder', 'OrderController@updatestatus');
		Route::post('/getorders', 'OrderController@getorders');

		// Wallet User
		Route::post('/get_information_wallet', 'WalletController@get_information_wallet');
		Route::post('/add_to_wallet', 'WalletController@add_to_wallet');
		Route::post('/pull_from_wallet', 'WalletController@pull_from_wallet');


    });
});
