<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\V1\BaseController;
use App\Transformers\Api\V1\UserTransformer;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Password;
use Illuminate\Mail\Message;
use App\User;
use JWTAuth;
use Hash;
use Auth;
use Mail;
Use Image;
use Str;
use App;
use App\Cart;
use App\Http\Controllers\App\AlertController;
use App\Http\Resources\CartCollection;
use App\Http\Resources\CartLikeCollection;
use App\Http\Resources\OrderCollection;
use App\Http\Resources\ProductCollection;
use App\ImageCategories;
use App\Models\AppModels\Orders;
use Illuminate\Support\Facades\File;
use App\Models\AppModels\Product;
use App\Models\Web\Order as WebOrder;
use App\Order;
use App\Product as AppProduct;
use App\ProductStock;
use App\Traits\ProductTrait;
use Carbon;
use DB;
use Essam\TapPayment\Payment;
use Tap\TapPayment\Facade\TapPayment;

class OrderController extends BaseController
{
    use ProductTrait;
    function __construct(UserTransformer $user_transformer)
    {
        config(['auth.defaults.guard' => 'api']);
        $this->user_transformer = $user_transformer;
        App::setLocale(request()->header('Accept-Language'));
    }

    public function getcart(Request $request){
        $user = $this->getAuthenticatedUser();
          
        if($user)
        {
            $carts = Cart::where('user_id',$user->id)->with('products')->get();
            if(count($carts)==0 || count($carts[0]->products) == 0){
                return response()->json(['data'=>[],'status'=>200],200);
            }
            return new CartCollection($carts);
            
        }
        else
        {
            return response()->json(['message' => trans('common.user_not_exist'), 'status_code' => 401], 401);
        }
    }

    public function getcartlike(Request $request){
        $user = $this->getAuthenticatedUser();
          
        if($user)
        {
            $carts = Cart::where('user_id',$user->id)->first();
            if($carts){
                $products=DB::table('cart_product')
                        ->where('cart_id', '=', $carts->cart_id)
                        ->where('type', '=', 2)->get();
                if(count($products)>0){
                    return new CartLikeCollection($products);
                }
                else{
                    return response()->json(['message'=>'cart is empty','data'=>[]],200);
                }
            }else{
                return response()->json(['message'=>'cart is empty','data'=>[]],200);
            }
            
            
        }
        else
        {
            return response()->json(['message' => trans('common.user_not_exist'), 'status_code' => 401], 401);
        }
    }

    public function addtocart(Request $request){
        $user = $this->getAuthenticatedUser();

        if($user)
        {
            $validator = Validator::make($request->all(), [
                'product_id'            => 'required',
                'quantity'              => 'required',
            ]);

            if($validator->fails())
            {
                return $this->getErrorMessage($validator);
            }

            $getCart = DB::table('carts')->where('user_id', $user->id)->first();
            if($request->type ==2){
                if($getCart){

                
                    $insertCart = $getCart->cart_id;
    
                    $product =DB::table('cart_product')->where('cart_id', '=', $getCart->cart_id)
                                                        ->where('product_id', '=', $request->product_id)
                                                        ->where('type', '=', $request->type)->first();
    
                    $productUpdate =DB::table('cart_product')->where('cart_id', '=', $getCart->cart_id)
                                                        ->where('product_id', '=', $request->product_id)
                                                        ->where('type', '=', $request->type);
    
                    //if product already exist
                    if($product){
                        $productUpdate->update([
                            'quantity'      => $product->quantity + 1,
                            'updated_at'    => now()
                        ]);
                    }else
                    {
                        $insertCartProduct = DB::table('cart_product')->insert([
                            'cart_id'       => $insertCart,
                            'product_id'    => $request->product_id,
                            'product_name'      => $request->product_name,
                            'product_image'      => $request->product_image,
                            'price'      => $request->price,
                            'quantity'      => $request->quantity,
                            'type'      => $request->type,
                            'created_at'    => now(),
                            'updated_at'    => now()
                        ]);
                    }
                }
                else
                {
                    $insertCart = DB::table('carts')->insertGetId([
                        'user_id'       => $user->id,
                        'created_at'    => now(),
                        'updated_at'    => now()
                    ]);
                    $insertCartProduct = DB::table('cart_product')->insert([
                        'cart_id'       => $insertCart,
                        'product_id'    => $request->product_id,
                        'product_name'      => $request->product_name,
                        'product_image'      => $request->product_image,
                        'price'      => $request->price,
                        'quantity'      => $request->quantity,
                        'type'      => $request->type,
                        'created_at'    => now(),
                        'updated_at'    => now()
                    ]);
                }
    
    
                return response()->json(['message' => trans('common.success_insert'), 'status_code' => 200], 200);
            }
            // dd($getCart);
            if($getCart){

                
                $insertCart = $getCart->cart_id;

                $product =DB::table('cart_product')->where('cart_id', '=', $getCart->cart_id)
                                                    ->where('product_id', '=', $request->product_id)
                                                    ->where('stock_id', '=', $request->stock_id)->first();

                $productUpdate =DB::table('cart_product')->where('cart_id', '=', $getCart->cart_id)
                                                    ->where('product_id', '=', $request->product_id)
                                                    ->where('stock_id', '=', $request->stock_id);

                //if product already exist
                if($product){
                    $productUpdate->update([
                        'quantity'      => $product->quantity + 1,
                        'updated_at'    => now()
                    ]);
                }else
                {
                    $insertCartProduct = DB::table('cart_product')->insert([
                        'cart_id'       => $insertCart,
                        'product_id'    => $request->product_id,
                        'stock_id'      => $request->stock_id,
                        'quantity'      => $request->quantity,
                        'created_at'    => now(),
                        'updated_at'    => now()
                    ]);
                }
            }
            else
            {
                $insertCart = DB::table('carts')->insertGetId([
                    'user_id'       => $user->id,
                    'created_at'    => now(),
                    'updated_at'    => now()
                ]);
                $insertCartProduct = DB::table('cart_product')->insert([
                    'cart_id'       => $insertCart,
                    'product_id'    => $request->product_id,
                    'stock_id'      => $request->stock_id,
                    'quantity'      => $request->quantity,
                    'created_at'    => now(),
                    'updated_at'    => now()
                ]);
            }


            return response()->json(['message' => trans('common.success_insert'), 'status_code' => 200], 200);
        }
        else
        {
            return response()->json(['message' => trans('common.user_not_exist'), 'status_code' => 401], 401);
        }
    }

    public function editcart(Request $request){
        $user = $this->getAuthenticatedUser();

        if($user)
        {
            $validator = Validator::make($request->all(), [
                'product_id'            => 'required',
                'quantity'              => 'required',
            ]);

            if($validator->fails())
            {
                return $this->getErrorMessage($validator);
            }

            $getCart = DB::table('carts')->where('user_id', $user->id)->first();

            if($getCart){
                $editCart = DB::table('cart_product')->where('cart_id', '=', $getCart->cart_id)->where('product_id', '=', $request->product_id)->update([
                    'quantity'      => $request->quantity,
                    'updated_at'    => now()
                ]);
                return response()->json(['message' => trans('common.success_update'), 'status_code' => 200], 200);
            } else {
                return response()->json(['message' => trans('common.user_not_exist'), 'status_code' => 400], 400);
            }

        }
        else
        {
            return response()->json(['message' => trans('common.user_not_exist'), 'status_code' => 401], 401);
        }
    }

    public function deletecart(Request $request){
        $user = $this->getAuthenticatedUser();

        if($user)
        {
            $getCart = DB::table('carts')->where('user_id', $user->id)->first();

            if($getCart){
                $deleteCart = DB::table('cart_product')->where('cart_id', '=', $getCart->cart_id)->where('product_id', '=', $request->product_id)->delete();
            }

            return response()->json(['message' => trans('common.success_update'), 'status_code' => 200], 200);
        }
        else
        {
            return response()->json(['message' => trans('common.user_not_exist'), 'status_code' => 401], 401);
        }
    }

    //addtoorder
    public function addtoorder(Request $request){
        $user = $this->getAuthenticatedUser();

        if($user)
        {
            $getCart = DB::table('carts')->where('user_id', $user->id)->first();

            $results=array();
            $data=array();
            $likeItems=null;
            // dd($getCart);
            $products_cart=array();
            if($getCart){

                $likeItems = DB::table('cart_product')->where('cart_id', '=', $getCart->cart_id)
                                                        ->where('type', '=', 2)->get();

                $productItem = DB::table('cart_product')->where('cart_id', '=', $getCart->cart_id)
                                                        ->where('type', '=', 1)->get();
            
                foreach($productItem as $key=>$cart){

                    // dd($products_cart);
                    // $product = AppProduct::where('products_id',$cart->product_id)
                    //                     ->with(['descriptions','images'])
                    //                     // ->whereHas('descriptions',function($q){
                    //                     //     return $q->where('language_id', '=', 1);
                    //                     // })
                    //                     ->where('products_status', '=', '1')->first();


                    $product = AppProduct::where('products_id',$cart->product_id)
                                        ->where('products_status', '=', '1')->with(['descriptions','images'])->first();

                   
                    $results[$key]['product_data']=$product;


                     
                
                     
                    $stocks = ProductStock::where('id',$cart->stock_id)->where('product_id',$product->products_id)->get();
                    if(isset($stocks) && !empty($stocks)){
                        
                        foreach($stocks as $index=>$stock){
                            
                            $results[$key]['attributes'][$index]['stock_id']=$stock->id;
                            foreach(explode('-', $stock->variant) as $i=>$variant){
                                if($product->colors && $i==0){
                                    $results[$key]['attributes'][$index]['color']=$variant;
                                    
                                }else
                                {
                                    $choice_options = json_decode($product->choice_options);
                                    
                                        $attribute_name = DB::table('products_options_descriptions')->where('products_options_id',$choice_options[$i-1]->attribute_id)->where('language_id',$request->language_id ?? 1)->first()->options_name;
                                        $results[$key]['attributes'][$index][$attribute_name]=$variant;
                                      
                                }

                            }

                            $results[$key]['attributes'][$index]['price']=$stock->price;
                            if($stock->image){
                                $imagepath= ImageCategories::where('image_id',$stock->image)
                                            ->where('image_type', '=', 'THUMBNAIL')
                                            ->where('image_type', '!=', 'THUMBNAIL')
                                            ->orWhere('image_type', '=', 'ACTUAL')->first()->path ?? '';
                
                                $results[$key]['attributes'][$index]['image']=asset($imagepath);
                            }

                            $results[$key]['attributes'][$index]['quantity']=$stock->qty;
                            $results[$key]['attributes'][$index]['SKU']=$stock->sku;
                                
                        }
                    }
                    $product->quantity_ordered = $cart->quantity;
                    // dd($product);
                    $data[] = $product;

                }
            }

            $guest_status   = 0;//$request->guest_status;

            if($guest_status == 1){
                $check = DB::table('users')->where('role_id',2)->where('email',$request->email)->first();
                if($check == null){
                    $customers_id = DB::table('users')->insertGetId([
                        'role_id' => 2,
                        'email' => $request->email,
                        'password' => Hash::make('123456dfdfdf'),
                        'first_name' => $request->delivery_firstname,
                        'last_name' => $request->delivery_lastname,
                        'phone' => $request->customers_telephone
                    ]);
                } else {
                    $customers_id = $check->id;
                }
            }
            else{
                $customers_id            				=   $user->id;//$request->customers_id;
            }

            $date_added								=	date('Y-m-d h:i:s');
            $customers_telephone            		=   $request->customers_telephone;
            $email            						=   $request->email;
            $delivery_firstname  	          		=   $request->delivery_firstname;
            $delivery_lastname            			=   $request->delivery_lastname;
            $delivery_street_address            	=   $request->delivery_street_address;
            $delivery_suburb            			=   $request->delivery_suburb;
            $delivery_city            				=   $request->delivery_city;
            $delivery_postcode            			=   $request->delivery_postcode;


            $delivery = DB::table('zones')->where('zone_name', '=', $request->delivery_zone)->first();

            if($delivery){
                $delivery_state            				=   $delivery->zone_code;
            }else{
                $delivery_state            				=   'other';
            }

            $delivery_country            			=   $request->delivery_country;
            $billing_firstname            			=   $request->billing_firstname;
            $billing_lastname            			=   $request->billing_lastname;
            $billing_street_address            		=   $request->billing_street_address;
            $billing_suburb	            			=   $request->billing_suburb;
            $billing_city            				=   $request->billing_city;
            $billing_postcode            			=   $request->billing_postcode;

            $billing = DB::table('zones')->where('zone_name', '=', $request->billing_zone)->first();

            if($billing){
                $billing_state                  =   $billing->zone_code;
            }else{
                $billing_state                  =   'other';
            }

            $billing_country                    =   $request->billing_country;
            $payment_method                     =   $request->payment_method;
            $order_information                  =	array();

            $cc_type            				=   $request->cc_type;
            $cc_owner            				=   $request->cc_owner;
            $cc_number            				=   $request->cc_number;
            $cc_expires            				=   $request->cc_expires;
            $last_modified            			=   date('Y-m-d H:i:s');
            $date_purchased            			=   date('Y-m-d H:i:s');
            $order_price                        =   $request->totalPrice;     $currency_code                      =   'SAR';
            $order_price                        =   Orders::converttodefaultprice($request->totalPrice, $currency_code);
            $shipping_cost            			=   $request->shipping_cost;
            $shipping_cost                      =   Orders::converttodefaultprice($request->shipping_cost, $currency_code);
            $shipping_method            		=   $request->shipping_method;
            $orders_status            			=   '1';
            $orders_date_finished            	=   $request->orders_date_finished;
            $comments            				=   $request->comments;

            //additional fields
            $delivery_phone						=	$request->delivery_phone;
            $billing_phone						=	$request->billing_phone;

            $delivery_latitude                  = $request->latitude;
            $delivery_longitude                 = $request->longitude;

            $settings = DB::table('settings')->get();
            $currency_value            			=   $request->currency_value;

            //tax info
            $total_tax							=	$request->total_tax;
            $total_tax                          = Orders::converttodefaultprice($request->total_tax, $currency_code);

            $products_tax 						= 	1;
            //coupon info
            $is_coupon_applied            		=   $request->is_coupon_applied;

            if($is_coupon_applied==1){

                $code = array();
                $coupon_amount = 0;
                $exclude_product_ids = array();
                $product_categories = array();
                $excluded_product_categories = array();
                $exclude_product_ids = array();

                $coupon_amount    =		$request->coupon_amount;

                //convert price to default currency price
                $coupon_amount = Orders::converttodefaultprice($coupon_amount, $currency_code);

                foreach($request->coupons as $coupons_data){

                    //update coupans
                    $coupon_id = DB::statement("UPDATE `coupons` SET `used_by`= CONCAT(used_by,',$customers_id') WHERE `code` = '".$coupons_data['code']."'");

                }
                $code = json_encode($request->coupons);

            }else{
                $code            					=   '';
                $coupon_amount            			=   0;
            }

            //payment methods
            $payments_setting = Orders::payments_setting_for_brain_tree($request);

            if($payment_method == 'braintree_card' or $payment_method == 'braintree_paypal'){
                if($payment_method == 'braintree_card'){
                    $fieldName = 'sub_name_1';
                    $paymentMethodName = 'Braintree Card';
                } else {
                    $fieldName = 'sub_name_2';
                    $paymentMethodName = 'Braintree Paypal';
                }

                // $paymentMethodName = $payments_setting->$fieldName;



                //braintree transaction with nonce
                $is_transaction  = '1'; 			# For payment through braintree
                $nonce    		 =   $request->nonce;

                if($payments_setting['merchant_id']->environment=='0'){
                    $braintree_enviroment = 'Test';
                }else{
                    $braintree_enviroment = 'Live';
                }

                $braintree_merchant_id = $payments_setting['merchant_id']->value;
                $braintree_public_key  = $payments_setting['public_key']->value;
                $braintree_private_key = $payments_setting['private_key']->value;

                //brain tree credential
                require_once app_path('braintree/index.php');

                if ($result->success) {
                    if($result->transaction->id) {
                        $order_information = array(
                            'braintree_id'=>$result->transaction->id,
                            'status'=>$result->transaction->status,
                            'type'=>$result->transaction->type,
                            'currencyIsoCode'=>$result->transaction->currencyIsoCode,
                            'amount'=>$result->transaction->amount,
                            'merchantAccountId'=>$result->transaction->merchantAccountId,
                            'subMerchantAccountId'=>$result->transaction->subMerchantAccountId,
                            'masterMerchantAccountId'=>$result->transaction->masterMerchantAccountId,
                            // 'orderId'=>$result->transaction->orderId,
                            'createdAt'=>time(),
                            // 'updatedAt'=>$result->transaction->updatedAt->date,
                            'token'=>$result->transaction->creditCard['token'],
                            'bin'=>$result->transaction->creditCard['bin'],
                            'last4'=>$result->transaction->creditCard['last4'],
                            'cardType'=>$result->transaction->creditCard['cardType'],
                            'expirationMonth'=>$result->transaction->creditCard['expirationMonth'],
                            'expirationYear'=>$result->transaction->creditCard['expirationYear'],
                            'customerLocation'=>$result->transaction->creditCard['customerLocation'],
                            'cardholderName'=>$result->transaction->creditCard['cardholderName']
                        );
                        $payment_status = "success";
                    }
                } else {
                    $payment_status = "failed";
                }

            }else if($payment_method == 'stripe'){				#### stipe payment

                $payments_setting = Orders::payments_setting_for_stripe($request);
                $paymentMethodName = $payments_setting['publishable_key']->name;

                //require file
                require_once app_path('stripe/config.php');

                //get token from app
                $token  = $request->nonce;

                $customer = \Stripe\Customer::create(array(
                    'email' => $email,
                    'source'  => $token
                ));

                $charge = \Stripe\Charge::create(array(
                    'customer' => $customer->id,
                    'amount'   => 100*$order_price,
                    'currency' => 'usd'
                ));

                if($charge->paid == true){
                    $order_information = array(
                            'paid'=>'true',
                            'transaction_id'=>$charge->id,
                            'type'=>$charge->outcome->type,
                            'balance_transaction'=>$charge->balance_transaction,
                            'status'=>$charge->status,
                            'currency'=>$charge->currency,
                            'amount'=>$charge->amount,
                            'created'=>date('d M,Y', $charge->created),
                            'dispute'=>$charge->dispute,
                            'customer'=>$charge->customer,
                            'address_zip'=>$charge->source->address_zip,
                            'seller_message'=>$charge->outcome->seller_message,
                            'network_status'=>$charge->outcome->network_status,
                            'expirationMonth'=>$charge->outcome->type
                        );

                        $payment_status = "success";

                }else{
                        $payment_status = "failed";
                }

            } else if($payment_method == 'apple_pay' || $payment_method == 'google_pay' || $payment_method == 'apple_pay_or_google_pay_and_wallet'|| $payment_method == 'wallet') {
                $order_information = array(
                    'paid'=>'true',
                    'balance_transaction'=>$order_price,
                    'currency'=>"SAR",
                    'amount'=>$order_price,
                );
                $payment_status = "success";
                $paymentMethodName = $payment_method;
            
            }
            
            else if($payment_method == 'cash_on_delivery'){

                $payments_setting = Orders::payments_setting_for_cod($request);
                $paymentMethodName =  $payments_setting->name;
                $payment_method = 'Cash on Delivery';
                $payment_status='success';

            } else if($payment_method == 'instamojo'){

                $payments_setting = Orders::payments_setting_for_instamojo($request);
                $paymentMethodName = $payments_setting['auth_token']->name;
                $payment_method = 'Instamojo';
                $payment_status='success';
                $order_information = array('payment_id'=>$request->nonce, 'transaction_id'=>$request->transaction_id);

            }else if($payment_method == 'hyperpay'){
                $payments_setting = Orders::payments_setting_for_hyperpay($request);
                $paymentMethodName = $payments_setting['userid']->name;
                $payment_method = 'Hyperpay';
                $payment_status='success';
            }else if($payment_method == 'razorpay'){
                $payments_setting = Orders::payments_setting_for_razorpay($request);
                $paymentMethodName = $payments_setting['RAZORPAY_KEY']->name;
                $payment_method = 'razorpay';
                $payment_status='success';
            }else if($payment_method == 'paytm'){
                $payments_setting = Orders::payments_setting_for_paytm($request);
                $paymentMethodName = $payments_setting['paytm_mid']->name;
                $payment_method = 'Paytm';
                $payment_status='success';
            }else if($payment_method == 'directbank'){
                $payments_setting = Orders::payments_setting_for_directbank($request);
                $paymentMethodName = $payments_setting['account_name']->name;
                $payment_method = 'directbank';
                $payment_status='success';
                $order_information = array(
                    'account_name' => $payments_setting['account_name']->value,
                    'account_number' => $payments_setting['account_number']->value,
                    'payment_method' => $payments_setting['account_name']->payment_method,
                    'bank_name' => $payments_setting['bank_name']->value,
                    'short_code' => $payments_setting['short_code']->value,
                    'iban' => $payments_setting['iban']->value,
                    'swift' => $payments_setting['swift']->value,
                );
            }else if($payment_method == 'paystack'){
                $payments_setting = Orders::payments_setting_for_paystack($request);
                $paymentMethodName = $payments_setting['secret_key']->name;
                $payment_method = 'paystack';
                $payment_status='success';
                $order_information = '';

            }else if ($payment_method == 'tap') {
                $paymentMethodName = 'tap';
                $or = new WebOrder();
                $payments_setting = $or->payments_setting_for_tap();
                // dd(session()->all());
                // dd($request->all());
                // $arrayToSend = [
                //     "amount"            => $order_price,
                //     "currency"          => "SAR",
                //     "threeDSecure"      => true,
                //     "save_card"         => false,
                //     // "description"       => "Test Description",
                //     // "statement_descriptor"=> "Sample",
                //     "customer" => [
                //         "first_name"    => $billing_firstname,
                //         "last_name"     => $billing_lastname,
                //         "email"         => $email,
                //         "phone" => [
                //             "country_code"  => "966",
                //             "number"        => $billing_phone
                //         ]
                //     ],
                //     "source" => [
                //         "id" => $request->token_id
                //     ],
                //     "post" => [
                //         "url" => route('place_order')
                //     ],
                //     "redirect" => [
                //         "url" => route('checkout_tap_payment')
                //     ]
                // ];

                // $json = json_encode($arrayToSend);

                // $curl = curl_init();

                // curl_setopt_array($curl, array(
                //   CURLOPT_URL => "https://api.tap.company/v2/charges",
                //   CURLOPT_RETURNTRANSFER => true,
                //   CURLOPT_ENCODING => "",
                //   CURLOPT_MAXREDIRS => 10,
                //   CURLOPT_TIMEOUT => 30,
                //   CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                //   CURLOPT_CUSTOMREQUEST => "POST",
                //   CURLOPT_POSTFIELDS => $json,
                //   CURLOPT_HTTPHEADER => array(
                //     "authorization: Bearer sk_test_AZ4bmEMR1rqGLzoTShvkwFNK",
                //     "content-type: application/json"
                //   ),
                // ));

                // $response = curl_exec($curl);
                // $err = curl_error($curl);

                // curl_close($curl);

                // if ($err) {
                //     // echo "cURL Error #:" . $err;
                //     $payment_status = 'failed';
                // } else {
                //     // echo $response;
                //     $resultsResponse = json_decode($response);
                //     // dd($resultsResponse->transaction->url);
                //     $payment_status = 'success';
                //     $transaction_id = $resultsResponse->id;
                //     $order_information = $resultsResponse;
                //     // dd($order_information->transaction->url);
                // }




                // get token from app
                // $token = $request->token;

                // $customer = \Stripe\Customer::create(array(
                //     'email' => $email,
                //     'source' => $token,
                // ));

                // $charge = \Stripe\Charge::create(array(
                //     'customer' => $customer->id,
                //     'amount' => 100 * $order_price,
                //     'currency' => 'usd',
                // ));

                // $fullnameBilling = $billing_firstname . ' ' . $billing_lastname;
                // $amountPay = 100 * $order_price;
                // $secret_api_Key = "pk_test_fILmzM42k3xrQT1UdEVWjK0X";
                // $TapPay = new Payment(['secret_api_Key'=> $secret_api_Key]);//pk_test_fILmzM42k3xrQT1UdEVWjK0X

                // $TapPay->card([
                //     'number' => '5123450000000008',
                //     'exp_month' => 05,
                //     'exp_year' => 21,
                //     'cvc' => 100,
                // ]);

                // dd($TapPay);

                try {
                $TapPay = new Payment(['secret_api_Key'=> 'sk_test_XKokBfNWv6FIYuTMg5sLPjhJ']);

                $redirect = false; // return response as json , you can use it form mobile web view application

                $re= $TapPay->charge([
                        'amount' => $request->price,
                        'currency' => 'SAR',
                        'threeDSecure' => 'true',
                        'description' => 'test description',
                        'statement_descriptor' => 'sample',
                        'customer' => [
                        'first_name' => $user->first_name,
                        'email' => $user->email,
                        ],
                        'source' => [
                        'id' => 'src_card'
                        ],
                        'post' => [
                        'url' => null
                        ],
                        'redirect' => [
                        'url' => route('payment_success',$req->id)
                        ]
                ],$redirect);
                

                    // $order_information = array(
                    //     'paid' => 'true',
                    //     'transaction_id' => $TapPay->getId(),
                    //     'type' => $TapPay->outcome->type,
                    //     'balance_transaction' => $TapPay->balance_transaction,
                    //     'status' => $TapPay->status,
                    //     'currency' => $TapPay->currency,
                    //     'amount' => $TapPay->amount,
                    //     'created' => date('d M,Y', $TapPay->created),
                    //     'dispute' => $TapPay->dispute,
                    //     'customer' => $TapPay->customer,
                    //     'address_zip' => $TapPay->source->address_zip,
                    //     'seller_message' => $TapPay->outcome->seller_message,
                    //     'network_status' => $TapPay->outcome->network_status,
                    //     'expirationMonth' => $TapPay->outcome->type,
                    // );

                    dd($re);
                    $payment_status = "success";
                } catch( \Exception $exception ) {
                    $payment_status = "failed";
                }

                // $payment = TapPayment::createCharge();
                //     $payment->setCustomerName($fullnameBilling);
                //     $payment->setCustomerPhone("20", $billing_phone);
                //     $payment->setDescription("Some description");
                //     $payment->setAmount($amountPay);
                //     $payment->setCurrency("SAR");
                //     $payment->setSource("src_all");
                //     $payment->setRedirectUrl("https://example.com");
                //     // $payment->setPostUrl("https://example.com"); // if you are using post request to handle payment updates
                //     // $payment->setMetaData(['package' => json_encode($package)]); // if you want to send metadata
                //     $invoice = $payment->pay();

                //     // dd($invoice);

                // try {


                //     if($payment->isSuccess()) {
                //         $order_information = array(
                //             'paid' => 'true',
                //             'transaction_id' => $invoice->getId(),
                //             'type' => $invoice->outcome->type,
                //             'balance_transaction' => $charge->balance_transaction,
                //             'status' => $invoice->status,
                //             'currency' => $invoice->currency,
                //             'amount' => $invoice->amount,
                //             'created' => date('d M,Y', $invoice->created),
                //             'dispute' => $invoice->dispute,
                //             'customer' => $invoice->customer,
                //             'address_zip' => $invoice->source->address_zip,
                //             'seller_message' => $invoice->outcome->seller_message,
                //             'network_status' => $invoice->outcome->network_status,
                //             'expirationMonth' => $invoice->outcome->type,
                //         );

                //         $payment_status = "success";
                //     } else {
                //         $payment_status = "failed";
                //     }

                // } catch( \Exception $exception ) {
                //     $payment_status = "failed";
                // }
            } else if ($payment_method == 'bank_account') {
                $paymentMethodName = 'Bank Account';
                $payments_setting = $this->payments_setting_for_bank_account();

                $bank_account_image='';
                if($request->bank_account_image){
                    $file = $request->bank_account_image;
                    $ext = $file->getClientOriginalExtension();
                    $imageName = time() . uniqid() . '.' . $ext;
                    $file->move(public_path('images/bank_account'), $imageName);
                    $bank_account_image = $imageName;
                }

                $bank_account_iban = $request->bank_account_iban;
                $payment_status = 'success';
            }
            else if($payment_method == 'new_tap'){
                $paymentMethodName='tap';
                $payment_status='success';
            }

            //check if order is verified
            if($payment_status=='success'){
                if( $payment_method == 'hyperpay'){
                    $cyb_orders = DB::table('orders')->where('transaction_id','=',$request->transaction_id)->get();
                    $orders_id = $cyb_orders[0]->orders_id;

                    //update database
                    DB::table('orders')->where('transaction_id','=',$request->transaction_id)->update(
                        [
                            'customers_id' => $customers_id,
                            'customers_name'  => $delivery_firstname.' '.$delivery_lastname,
                            'customers_street_address' => $delivery_street_address,
                            'customers_suburb'  =>  $delivery_suburb,
                            'customers_city' => $delivery_city,
                            'customers_postcode'  => $delivery_postcode,
                            'customers_state' => $delivery_state,
                            'customers_country'  =>  $delivery_country,
                            'customers_telephone' => $customers_telephone,
                            'email'  => $email,

                            'delivery_name'  =>  $delivery_firstname.' '.$delivery_lastname,
                            'delivery_street_address' => $delivery_street_address,
                            'delivery_suburb'  => $delivery_suburb,
                            'delivery_city' => $delivery_city,
                            'delivery_postcode'  =>  $delivery_postcode,
                            'delivery_state' => $delivery_state,
                            'delivery_country'  => $delivery_country,
                            'billing_name'  => $billing_firstname.' '.$billing_lastname,
                            'billing_street_address' => $billing_street_address,
                            'billing_suburb'  =>  $billing_suburb,
                            'billing_city' => $billing_city,
                            'billing_postcode'  => $billing_postcode,
                            'billing_state' => $billing_state,
                            'billing_country'  =>  $billing_country,

                            'payment_method'  =>  $paymentMethodName,
                            'cc_type' => $cc_type,
                            'cc_owner'  => $cc_owner,
                            'cc_number' =>$cc_number,
                            'cc_expires'  =>  $cc_expires,
                            'last_modified' => $last_modified,
                            'date_purchased'  => $date_purchased,
                            'order_price'  => $order_price,
                            'shipping_cost' =>$shipping_cost,
                            'shipping_method'  =>  $shipping_method,
                            'currency'  =>  $currency_code,
                            'currency_value' => $last_modified,
                            'coupon_code'		 =>		$code,
                            'coupon_amount' 	 =>		$coupon_amount,
                            'total_tax'		 =>		$total_tax,
                            'ordered_source' 	 => 	'2',
                            'delivery_phone'	 =>		$delivery_phone,
                            'billing_phone'	 =>		$billing_phone
                        ]);

                }else if ($payment_method == 'bank_account'){
                    // dd($bank_account_image);
                        //insert order
                        $orders_id = DB::table('orders')->insertGetId(
                        [	 'customers_id' => $customers_id,
                            'customers_name'  => $delivery_firstname.' '.$delivery_lastname,
                            'customers_street_address' => $delivery_street_address,
                            'customers_suburb'  =>  $delivery_suburb,
                            'customers_city' => $delivery_city,
                            'customers_postcode'  => $delivery_postcode,
                            'customers_state' => $delivery_state,
                            'customers_country'  =>  $delivery_country,
                            'customers_telephone' => $customers_telephone,
                            'email'  => $email,

                            'delivery_name'  =>  $delivery_firstname.' '.$delivery_lastname,
                            'delivery_street_address' => $delivery_street_address,
                            'delivery_suburb'  => $delivery_suburb,
                            'delivery_city' => $delivery_city,
                            'delivery_postcode'  =>  $delivery_postcode,
                            'delivery_state' => $delivery_state,
                            'delivery_country'  => $delivery_country,

                            'billing_name'  => $billing_firstname.' '.$billing_lastname,
                            'billing_street_address' => $billing_street_address,
                            'billing_suburb'  =>  $billing_suburb,
                            'billing_city' => $billing_city,
                            'billing_postcode'  => $billing_postcode,
                            'billing_state' => $billing_state,
                            'billing_country'  =>  $billing_country,

                            'payment_method'  =>  $paymentMethodName,
                            'cc_type' => $cc_type,
                            'cc_owner'  => $cc_owner,
                            'cc_number' =>$cc_number,
                            'cc_expires'  =>  $cc_expires,
                            'last_modified' => $last_modified,
                            'date_purchased'  => $date_purchased,
                            'order_price'  => $order_price,
                            'shipping_cost' =>$shipping_cost,
                            'shipping_method'  =>  $shipping_method,
                            'currency'  =>  $currency_code,
                            'currency_value' => $last_modified,
                            'order_information' => json_encode($order_information),
                            'coupon_code'		 =>		$code,
                            'coupon_amount' 	 =>		$coupon_amount,
                            'total_tax'		 =>		$total_tax,
                            'ordered_source' 	 => 	'2',
                            'delivery_phone'	 =>		$delivery_phone,
                            'billing_phone'	 =>		$billing_phone,
                            'delivery_latitude' => $delivery_latitude,
                            'delivery_longitude' => $delivery_longitude,
                            // 'transaction_id'    => $transaction_id,
                            'bank_account_image' => $bank_account_image,
                            'bank_account_iban' => $bank_account_iban
                            
                        ]);

                    }
                else{

                    //insert order
                    $orders_id = DB::table('orders')->insertGetId(
                    [	 'customers_id' => $customers_id,
                        'customers_name'  => $delivery_firstname.' '.$delivery_lastname,
                        'customers_street_address' => $delivery_street_address,
                        'customers_suburb'  =>  $delivery_suburb,
                        'customers_city' => $delivery_city,
                        'customers_postcode'  => $delivery_postcode,
                        'customers_state' => $delivery_state,
                        'customers_country'  =>  $delivery_country,
                        'customers_telephone' => $customers_telephone,
                        'email'  => $email,

                        'delivery_name'  =>  $delivery_firstname.' '.$delivery_lastname,
                        'delivery_street_address' => $delivery_street_address,
                        'delivery_suburb'  => $delivery_suburb,
                        'delivery_city' => $delivery_city,
                        'delivery_postcode'  =>  $delivery_postcode,
                        'delivery_state' => $delivery_state,
                        'delivery_country'  => $delivery_country,

                        'billing_name'  => $billing_firstname.' '.$billing_lastname,
                        'billing_street_address' => $billing_street_address,
                        'billing_suburb'  =>  $billing_suburb,
                        'billing_city' => $billing_city,
                        'billing_postcode'  => $billing_postcode,
                        'billing_state' => $billing_state,
                        'billing_country'  =>  $billing_country,

                        'payment_method'  =>  $paymentMethodName,
                        'cc_type' => $cc_type,
                        'cc_owner'  => $cc_owner,
                        'cc_number' =>$cc_number,
                        'cc_expires'  =>  $cc_expires,
                        'last_modified' => $last_modified,
                        'date_purchased'  => $date_purchased,
                        'order_price'  => $order_price,
                        'shipping_cost' =>$shipping_cost,
                        'shipping_method'  =>  $shipping_method,
                        'currency'  =>  $currency_code,
                        'currency_value' => $last_modified,
                        'order_information' => json_encode($order_information),
                        'coupon_code'		 =>		$code,
                        'coupon_amount' 	 =>		$coupon_amount,
                        'total_tax'		 =>		$total_tax,
                        'ordered_source' 	 => 	'2',
                        'delivery_phone'	 =>		$delivery_phone,
                        'billing_phone'	 =>		$billing_phone,
                        'delivery_latitude' => $delivery_latitude,
                        'delivery_longitude' => $delivery_longitude,
                        'paied' => $request->paied??0,
                        // 'transaction_id'    => $transaction_id,
                        // 'bank_account_image' => $bank_account_image,
                        // 'bank_account_iban' => $bank_account_iban
                    ]);

                }

                //orders status history
                $orders_history_id = DB::table('orders_status_history')->insertGetId(
                    [	'orders_id'  => $orders_id,
                        'orders_status_id' => $orders_status,
                        'date_added'  => $date_added,
                        'customer_notified' =>'1',
                        'comments'  =>  $comments
                    ]);

                    // dd($data);
                foreach($data as $ii=>$products){
                    //dd($products->products_price, $currency_code);
                    $c_price = str_replace(',','',$results[$ii]['attributes']->price ?? $products->products_price);
                    $c_final_price = str_replace(',','',$results[$ii]['attributes']->price ?? $products->products_price);
                    $price = Orders::converttodefaultprice($c_price, $currency_code);
                    $final_price = $c_final_price*$products->quantity_ordered;
                    $final_price = Orders::converttodefaultprice($final_price, $currency_code);

                    $current_stock_in = DB::table('inventory')->where('products_id', $products->products_id)->Where('stock_type', '=', 'in')->sum('stock');
                    $current_stock_out = DB::table('inventory')->where('products_id', $products->products_id)->Where('stock_type', '=', 'out')->sum('stock');
                    $current_stock = $current_stock_in - $current_stock_out;

                    // if($products->quantity_ordered>$current_stock){
                    //     $responseData = array('status' => 2, 'message' => "Sorry, ".$products->descriptions[0]->products_name." is out of stock");
                    //     return response()->json($responseData);
                    // }
                    // dd($products->descriptions[0]->products_name);
                    $orders_products_id = DB::table('orders_products')->insertGetId(
                    [
                        'orders_id' 		 => 	$orders_id,
                        'products_id' 	 	 =>		$products->products_id,
                        'products_name'	     => 	$products->descriptions[0]->products_name ??$products->products_slug,
                        'products_price'	 =>  	$price,
                        'final_price' 		 =>  	$final_price,
                        'products_tax' 	     =>  	$products_tax,
                        'products_quantity' =>  	$products->quantity_ordered,
                    ]);
                    
                    $inventory_ref_id = DB::table('inventory')->insertGetId([
                        'products_id'   		=>   $products->products_id,
                        'reference_code'  		=>   '',
                        'stock'  				=>   $products->quantity_ordered,
                        'admin_id'  			=>   0,
                        'added_date'	  		=>   time(),
                        'purchase_price'  		=>   0,
                        'stock_type'  			=>   'out',
                    ]);

                    // dd($getCart);

                 
                    if(!empty($results[$ii]['attributes']))
                    {
                        // dd($results['attributes']);

                        foreach($results[$ii]['attributes'] as $attribute){
                            // dd($attribute->stock_id);
                            DB::table('orders_products_attributes')->insert(
                            [
                                'orders_id' => $orders_id,
                                'products_id'  => $products->products_id,
                                'stock_id'  => $attribute['stock_id'],
                                // 'products_options' =>$attribute['option']['name'],
                                // 'products_options_values'  =>  $value['value'],
                                'options_values_price'  =>  $attribute['price'],
                                // 'price_prefix'  => $value['price_prefix']
                            ]);

                            // $products_attributes = DB::table('products_attributes')->where([
                            //     ['options_id', '=', $attribute['option']['id']],
                            //     ['options_values_id', '=', $value['id']],
                            // ])->get();

                            // DB::table('inventory_detail')->insert([
                            //     'inventory_ref_id'  =>   $inventory_ref_id,
                            //     'products_id'  		=>   $products->products_id,
                            //     'attribute_id'		=>   $products_attributes[0]->products_attributes_id,
                            // ]);

                        }
                    }
                    

                }
                if($likeItems){
                    foreach($likeItems as $products){
                        $orders_products_id = DB::table('orders_products')->insertGetId(
                            [
                                'orders_id' 		 => 	$orders_id,
                                'products_id' 	 	 =>		$products->product_id,
                                'products_image' 	 	 =>		$products->product_image,
                                'products_name'	     => 	$products->product_name ??'no Name',
                                'products_price'	 =>  	$products->price,
                                'final_price' 		 =>  	$products->price * $products->quantity,
                                'products_tax' 	     =>  	0,
                                'type' 	     =>  	2, // like 
                                'products_quantity' =>  	$products->quantity,
                            ]);
                    }
                }

                $responseData = array('success'=>'1', 'data'=>array(), 'customer_id' => $customers_id,'message'=>"Order has been placed successfully.");

                //send order email to user
                $order = DB::table('orders')
                    ->LeftJoin('orders_status_history', 'orders_status_history.orders_id', '=', 'orders.orders_id')
                    ->LeftJoin('orders_status', 'orders_status.orders_status_id', '=' ,'orders_status_history.orders_status_id')
                    ->where('orders.orders_id', '=', $orders_id)->orderby('orders_status_history.date_added', 'DESC')->get();

                //foreach
                foreach($order as $data){
                    $orders_id	 = $data->orders_id;

                    $orders_products = DB::table('orders_products')
                        ->join('products', 'products.products_id','=', 'orders_products.products_id')
                        ->select('orders_products.*', 'products.products_image as image')
                        ->where('orders_products.orders_id', '=', $orders_id)->get();
                        $i = 0;
                        $total_price  = 0;
                        $product = array();
                        $subtotal = 0;
                        foreach($orders_products as $orders_products_data){
                            $product_attribute = DB::table('orders_products_attributes')
                                ->where([
                                    ['orders_products_id', '=', $orders_products_data->orders_products_id],
                                    ['orders_id', '=', $orders_products_data->orders_id],
                                ])
                                ->get();

                            $orders_products_data->attribute = $product_attribute;
                            $product[$i] = $orders_products_data;
                            //$total_tax	 = $total_tax+$orders_products_data->products_tax;
                            $total_price = $total_price+$orders_products[$i]->final_price;

                            $subtotal += $orders_products[$i]->final_price;

                            $i++;
                        }

                    $data->data = $product;
                    $orders_data[] = $data;
                }

                $orders_status_history = DB::table('orders_status_history')
                    ->LeftJoin('orders_status', 'orders_status.orders_status_id', '=' ,'orders_status_history.orders_status_id')
                    ->orderBy('orders_status_history.date_added', 'desc')
                    ->where('orders_id', '=', $orders_id)->get();

                $orders_status = DB::table('orders_status')->get();

                $ordersData['orders_data']		 	 	=	$orders_data;
                $ordersData['total_price']  			=	$total_price;
                $ordersData['orders_status']			=	$orders_status;
                $ordersData['orders_status_history']    =	$orders_status_history;
                $ordersData['subtotal']    				=	$subtotal;


                //notification/email
                $myVar = new AlertController();
                $alertSetting = $myVar->orderAlert($ordersData);

            }else if($payment_status == "failed"){
                if(!empty($error_cybersource)){
                    $return_error = $error_cybersource;
                }else{
                    $return_error = 'Error while placing order.';
                }
                $responseData = array('success'=>'0', 'data'=>array(), 'message'=>$error_cybersource);
            }

            // $orderResponse = json_encode($responseData);

            // return $orderResponse;

            

            if($payment_method == 'new_tap'){
                $TapPay = new Payment(['secret_api_Key'=> 'sk_test_XKokBfNWv6FIYuTMg5sLPjhJ']);

                $redirect = false; // return response as json , you can use it form mobile web view application

                $re= $TapPay->charge([
                        'amount' => $order_price,
                        'currency' => 'SAR',
                        'threeDSecure' => 'true',
                        'description' => 'test description',
                        'statement_descriptor' => 'sample',
                        'customer' => [
                        'first_name' => $user->first_name,
                        'email' => $user->email,
                        ],
                        'source' => [
                        'id' => 'src_card'
                        ],
                        'post' => [
                        'url' => null
                        ],
                        'redirect' => [
                        'url' => route('order_payment_success',['id'=>$orders_id,'cart_id'=>$getCart->cart_id])
                        ]
                ],$redirect);

                return response()->json($re);

            }
            if($getCart){
                $deleteCart = DB::table('cart_product')->where('cart_id', '=', $getCart->cart_id)->delete();
            }
            return response()->json($responseData);
        } else {
            return response()->json(['message' => trans('common.user_not_exist'), 'status_code' => 401], 401);
        }

    }

    //get default payment method
    public function getPaymentMethods()
    {
        /**   BRAIN TREE **/
        //////////////////////
        $result = array();
        $payments_setting = $this->payments_setting_for_brain_tree();
        if ($payments_setting['merchant_id']->environment == '0') {
            $braintree_enviroment = 'Test';
        } else {
            $braintree_enviroment = 'Live';
        }

        $braintree = array(
            'environment' => $braintree_enviroment,
            'name' => $payments_setting['merchant_id']->name,
            'public_key' => $payments_setting['public_key']->value,
            'active' => $payments_setting['merchant_id']->status,
            'payment_method' => $payments_setting['merchant_id']->payment_method,
            'payment_currency' => 'SAR',
        );
        /**  END BRAIN TREE **/
        //////////////////////

        /**   STRIPE**/
        //////////////////////

        $payments_setting = $this->payments_setting_for_stripe();
        if ($payments_setting['publishable_key']->environment == '0') {
            $stripe_enviroment = 'Test';
        } else {
            $stripe_enviroment = 'Live';
        }

        $stripe = array(
            'environment' => $stripe_enviroment,
            'name' => $payments_setting['publishable_key']->name,
            'public_key' => $payments_setting['publishable_key']->value,
            'active' => $payments_setting['publishable_key']->status,
            'payment_currency' => 'SAR',
            'payment_method' => $payments_setting['publishable_key']->payment_method,
        );

        /**   END STRIPE**/
        //////////////////////

        /**   CASH ON DELIVERY**/
        //////////////////////

        $payments_setting = $this->payments_setting_for_cod();

        $cod = array(
            'environment' => 'Live',
            'name' => $payments_setting->name,
            'public_key' => '',
            'active' => $payments_setting->status,
            'payment_currency' => 'SAR',
            'payment_method' => $payments_setting->payment_method,
        );

        /**   END CASH ON DELIVERY**/
        /*************************/


        /**   INSTAMOJO**/
        /*************************/
        $payments_setting = $this->payments_setting_for_instamojo();
        if ($payments_setting['auth_token']->environment == '0') {
            $instamojo_enviroment = 'Test';
        } else {
            $instamojo_enviroment = 'Live';
        }

        $instamojo = array(
            'environment' => $instamojo_enviroment,
            'name' => $payments_setting['auth_token']->name,
            'public_key' => $payments_setting['api_key']->value,
            'active' => $payments_setting['api_key']->status,
            'payment_currency' => 'SAR',
            'payment_method' => $payments_setting['api_key']->payment_method,
        );

        /**   END INSTAMOJO**/
        /*************************/

        /**   END HYPERPAY**/
        /*************************/
        $payments_setting = $this->payments_setting_for_hyperpay();
        //dd($payments_setting);
        if ($payments_setting['userid']->environment == '0') {
            $hyperpay_enviroment = 'Test';
        } else {
            $hyperpay_enviroment = 'Live';
        }

        $hyperpay = array(
            'environment' => $hyperpay_enviroment,
            'name' => $payments_setting['userid']->name,
            'public_key' => $payments_setting['userid']->value,
            'active' => $payments_setting['userid']->status,
            'payment_currency' => 'SAR',
            'payment_method' => $payments_setting['userid']->payment_method,
        );
        /**   END HYPERPAY**/
        /*************************/

        $payments_setting = $this->payments_setting_for_razorpay();

        if ($payments_setting['RAZORPAY_SECRET']->environment == '0') {
            $razorpay_enviroment = 'Test';
        } else {
            $razorpay_enviroment = 'Live';
        }

        $razorpay = array(
            'environment' => $razorpay_enviroment,
            'public_key' => $payments_setting['RAZORPAY_KEY']->value,
            'name' => $payments_setting['RAZORPAY_KEY']->name,
            'RAZORPAY_KEY' => $payments_setting['RAZORPAY_KEY']->value,
            'RAZORPAY_SECRET' => $payments_setting['RAZORPAY_SECRET']->value,
            'active' => $payments_setting['RAZORPAY_SECRET']->status,
            'payment_currency' => 'SAR',
            'payment_method' => $payments_setting['RAZORPAY_SECRET']->payment_method,
        );

        $payments_setting = $this->payments_setting_for_paytm();


        if ($payments_setting['paytm_mid']->environment == '0') {
            $paytm_enviroment = 'Test';
        } else {
            $paytm_enviroment = 'Live';
        }

        $paytm = array(
            'environment' => $paytm_enviroment,
            'payment_currency' => 'SAR',
            'public_key' => '',
            'name' => $payments_setting['paytm_mid']->name,
            'active' => $payments_setting['paytm_mid']->status,
            'payment_method' => $payments_setting['paytm_mid']->payment_method,
        );

        /**   TAP   **/
        //////////////////////

        $payments_setting = $this->payments_setting_for_tap();
        if ($payments_setting['api_key']->environment == '0') {
            $tap_enviroment = 'Test';
        } else {
            $tap_enviroment = 'Live';
        }

        $tap = array(
            'environment' => $tap_enviroment,
            'name' => $payments_setting['api_key']->name,
            'public_key' => $payments_setting['api_key']->value,
            'active' => $payments_setting['api_key']->status,
            'payment_currency' => 'SAR',
            'payment_method' => $payments_setting['api_key']->payment_method,
        );

        /**   END TAP   **/
        //////////////////////

        /**   Bank Account   **/
        //////////////////////

        $payments_setting = $this->payments_setting_for_bank_account();
        if ($payments_setting['iban']->environment == '0') {
            $tap_enviroment = 'Test';
        } else {
            $tap_enviroment = 'Live';
        }

        $bank = array(
            'environment' => $tap_enviroment,
            'name' => $payments_setting['iban']->name,
            'public_key' => $payments_setting['iban']->value,
            'active' => $payments_setting['iban']->status,
            'payment_currency' => 'SAR',
            'payment_method' => $payments_setting['iban']->payment_method,
        );

        /**   END TAP   **/
        //////////////////////

        array_push($result,$braintree);
        array_push($result,$stripe);
        array_push($result,$cod);
        array_push($result,$instamojo);
        array_push($result,$hyperpay);
        array_push($result,$razorpay);
        array_push($result,$paytm);
        array_push($result,$tap);
        array_push($result,$bank);
        // $result[0] = $braintree;
        // $result[1] = $stripe;
        // $result[2] = $cod;
        // $result[4] = $instamojo;
        // $result[5] = $hyperpay;
        // $result[6] = $razorpay;
        // $result[7] = $paytm;
        // $result[8] = $tap;
        // $result[9] = $bank;

        // dd($result);
        if (count($result) > 0) {
            $responseData = array('success' => '1', 'data' => $result, 'message' => "Sliders are returned successfull.");
        } else {
            $result = array();
            $responseData = array('success' => '0', 'data' => $result, 'message' => "Sliders are empty.");
        }

        return response()->json($responseData);

        // return $result;
    }

    //getorders
    public function getorders(Request $request){
        $user = $this->getAuthenticatedUser();

        if($user)
        {
            $orders=Order::where('customers_id',$user->id)->with('products')->orderBy('orders_id', 'desc')->get();
            return new OrderCollection($orders);
            
        }
        else
        {
            return response()->json(['message' => trans('common.user_not_exist'), 'status_code' => 401], 401);
        }
    }

    //updatestatus
    public function updatestatus(Request $request){
        $user = $this->getAuthenticatedUser();

        if($user)
        {
            $orders_id          = $request->orders_id;
            $date_added			= date('Y-m-d h:i:s');
  			$comments			= $request->comment;
  			$orders_history_id = DB::table('orders_status_history')->insertGetId([
                'orders_id'  => $orders_id,
                'orders_status_id' => '3',
                'date_added'  => $date_added,
                'customer_notified' =>'1',
                'comments'  =>  $comments
            ]);

  			$responseData = array('success'=>'1', 'data'=>array(), 'message'=>"Status has been changed succefully.");
            return response()->json($responseData);
        }
        else
        {
            return response()->json(['message' => trans('common.user_not_exist'), 'status_code' => 401], 401);
        }
    }

    public function payments_setting_for_brain_tree()
    {
        $payments_setting = DB::table('payment_methods_detail')
            ->leftjoin('payment_description', 'payment_description.payment_methods_id', '=', 'payment_methods_detail.payment_methods_id')
            ->leftjoin('payment_methods', 'payment_methods.payment_methods_id', '=', 'payment_methods_detail.payment_methods_id')
            ->select('payment_methods_detail.*', 'payment_description.name', 'payment_methods.environment', 'payment_methods.status', 'payment_methods.payment_method')
            ->where('language_id', session('language_id'))
            ->where('payment_description.payment_methods_id', 1)
            ->orwhere('language_id', 1)
            ->where('payment_description.payment_methods_id', 1)
            ->get()->keyBy('key');
        return $payments_setting;
    }

    public function payments_setting_for_stripe()
    {
        $payments_setting = DB::table('payment_methods_detail')
            ->leftjoin('payment_description', 'payment_description.payment_methods_id', '=', 'payment_methods_detail.payment_methods_id')
            ->leftjoin('payment_methods', 'payment_methods.payment_methods_id', '=', 'payment_methods_detail.payment_methods_id')
            ->select('payment_methods_detail.*', 'payment_description.name', 'payment_methods.environment', 'payment_methods.status', 'payment_methods.payment_method')
            ->where('language_id', session('language_id'))
            ->where('payment_description.payment_methods_id', 2)
            ->orwhere('language_id', 1)
            ->where('payment_description.payment_methods_id', 2)
            ->get()->keyBy('key');
        return $payments_setting;
    }

    public function payments_setting_for_cod()
    {
        $payments_setting = DB::table('payment_description')
            ->leftjoin('payment_methods', 'payment_methods.payment_methods_id', '=', 'payment_description.payment_methods_id')
            ->select('payment_description.name', 'payment_methods.environment', 'payment_methods.status', 'payment_methods.payment_method')
            ->where('language_id', session('language_id'))
            ->where('payment_description.payment_methods_id', 4)
            ->orwhere('language_id', 1)
            ->where('payment_description.payment_methods_id', 4)
            ->first();
        return $payments_setting;
    }

    public function payments_setting_for_instamojo()
    {
        $payments_setting = DB::table('payment_methods_detail')
            ->leftjoin('payment_description', 'payment_description.payment_methods_id', '=', 'payment_methods_detail.payment_methods_id')
            ->leftjoin('payment_methods', 'payment_methods.payment_methods_id', '=', 'payment_methods_detail.payment_methods_id')
            ->select('payment_methods_detail.*', 'payment_description.name', 'payment_methods.environment', 'payment_methods.status', 'payment_methods.payment_method')
            ->where('language_id', session('language_id'))
            ->where('payment_description.payment_methods_id', 5)
            ->orwhere('language_id', 1)
            ->where('payment_description.payment_methods_id', 5)
            ->get()->keyBy('key');
        return $payments_setting;
    }

    public function payments_setting_for_hyperpay()
    {
        $payments_setting = DB::table('payment_methods_detail')
            ->leftjoin('payment_description', 'payment_description.payment_methods_id', '=', 'payment_methods_detail.payment_methods_id')
            ->leftjoin('payment_methods', 'payment_methods.payment_methods_id', '=', 'payment_methods_detail.payment_methods_id')
            ->select('payment_methods_detail.*', 'payment_description.name', 'payment_methods.environment', 'payment_methods.status', 'payment_methods.payment_method')
            ->where('language_id', session('language_id'))
            ->where('payment_description.payment_methods_id', 6)
            ->orwhere('language_id', 1)
            ->where('payment_description.payment_methods_id', 6)
            ->get()->keyBy('key');
        return $payments_setting;
    }

    public function payments_setting_for_razorpay()
    {
        $payments_setting = DB::table('payment_methods_detail')
            ->leftjoin('payment_description', 'payment_description.payment_methods_id', '=', 'payment_methods_detail.payment_methods_id')
            ->leftjoin('payment_methods', 'payment_methods.payment_methods_id', '=', 'payment_methods_detail.payment_methods_id')
            ->select('payment_methods_detail.*', 'payment_description.name', 'payment_methods.environment', 'payment_methods.status', 'payment_methods.payment_method')
            ->where('language_id', session('language_id'))
            ->where('payment_description.payment_methods_id', 7)
            ->orwhere('language_id', 1)
            ->where('payment_description.payment_methods_id', 7)
            ->get()->keyBy('key');
        return $payments_setting;
    }

    public function payments_setting_for_paytm()
    {
        $payments_setting = DB::table('payment_methods_detail')
            ->leftjoin('payment_description', 'payment_description.payment_methods_id', '=', 'payment_methods_detail.payment_methods_id')
            ->leftjoin('payment_methods', 'payment_methods.payment_methods_id', '=', 'payment_methods_detail.payment_methods_id')
            ->select('payment_methods_detail.*', 'payment_description.name', 'payment_methods.environment', 'payment_methods.status', 'payment_methods.payment_method')
            ->where('language_id', session('language_id'))
            ->where('payment_description.payment_methods_id', 8)
            ->orwhere('language_id', 1)
            ->where('payment_description.payment_methods_id', 8)
            ->get()->keyBy('key');
        return $payments_setting;
    }

    public function payments_setting_for_directbank()
    {
        $payments_setting = DB::table('payment_methods_detail')
            ->leftjoin('payment_description', 'payment_description.payment_methods_id', '=', 'payment_methods_detail.payment_methods_id')
            ->leftjoin('payment_methods', 'payment_methods.payment_methods_id', '=', 'payment_methods_detail.payment_methods_id')
            ->select('payment_methods_detail.*', 'payment_description.name', 'payment_methods.environment', 'payment_methods.status',
            'payment_methods.payment_method', 'payment_description.sub_name_1')
            ->where('language_id', session('language_id'))
            ->where('payment_description.payment_methods_id', 9)
            ->orwhere('language_id', 1)
            ->where('payment_description.payment_methods_id', 9)
            ->get()->keyBy('key');
        return $payments_setting;
    }

    public function payments_setting_for_paystack()
    {
        $payments_setting = DB::table('payment_methods_detail')
            ->leftjoin('payment_description', 'payment_description.payment_methods_id', '=', 'payment_methods_detail.payment_methods_id')
            ->leftjoin('payment_methods', 'payment_methods.payment_methods_id', '=', 'payment_methods_detail.payment_methods_id')
            ->select('payment_methods_detail.*', 'payment_description.name', 'payment_methods.environment', 'payment_methods.status',
            'payment_methods.payment_method')
            ->where('language_id', session('language_id'))
            ->where('payment_description.payment_methods_id', 10)
            ->orwhere('language_id', 1)
            ->where('payment_description.payment_methods_id', 10)
            ->get()->keyBy('key');
        return $payments_setting;
    }

    public function payments_setting_for_midtrans()
    {
        $payments_setting = DB::table('payment_methods_detail')
            ->leftjoin('payment_description', 'payment_description.payment_methods_id', '=', 'payment_methods_detail.payment_methods_id')
            ->leftjoin('payment_methods', 'payment_methods.payment_methods_id', '=', 'payment_methods_detail.payment_methods_id')
            ->select('payment_methods_detail.*', 'payment_description.name', 'payment_methods.environment', 'payment_methods.status',
            'payment_methods.payment_method')
            ->where('language_id', session('language_id'))
            ->where('payment_description.payment_methods_id', 11)
            ->orwhere('language_id', 1)
            ->where('payment_description.payment_methods_id', 11)
            ->get()->keyBy('key');
        return $payments_setting;
    }

    public function payments_setting_for_tap()
    {
        $payments_setting = DB::table('payment_methods_detail')
            ->leftjoin('payment_description', 'payment_description.payment_methods_id', '=', 'payment_methods_detail.payment_methods_id')
            ->leftjoin('payment_methods', 'payment_methods.payment_methods_id', '=', 'payment_methods_detail.payment_methods_id')
            ->select('payment_methods_detail.*', 'payment_description.name', 'payment_methods.environment', 'payment_methods.status', 'payment_methods.payment_method')
            ->where('language_id', session('language_id'))
            ->where('payment_description.payment_methods_id', 9)
            ->orwhere('language_id', 1)
            ->where('payment_description.payment_methods_id', 9)
            ->get()->keyBy('key');
        return $payments_setting;
    }

    public function payments_setting_for_bank_account()
    {
        $payments_setting = DB::table('payment_methods_detail')
            ->leftjoin('payment_description', 'payment_description.payment_methods_id', '=', 'payment_methods_detail.payment_methods_id')
            ->leftjoin('payment_methods', 'payment_methods.payment_methods_id', '=', 'payment_methods_detail.payment_methods_id')
            ->select('payment_methods_detail.*', 'payment_description.name', 'payment_methods.environment', 'payment_methods.status', 'payment_methods.payment_method')
            ->where('language_id', session('language_id'))
            ->where('payment_description.payment_methods_id', 10)
            ->orwhere('language_id', 1)
            ->where('payment_description.payment_methods_id', 10)
            ->get()->keyBy('key');
        return $payments_setting;
    }

    public function addtopos(Request $request){
        $user = $this->getAuthenticatedUser();

        if($user)
        {

            $getCart = DB::table('carts')->where('user_id', $user->id)->first();

            $products_cart=array();
            if($getCart){

                $productItem = DB::table('cart_product')->where('cart_id', '=', $getCart->cart_id)->get();
                
            }


            foreach($productItem as $cart){
                DB::table('pos_standby')->insert([
                    'customer_id'       => $user->id,
                    'product_id'    => $cart->product_id,
                    'quantity'      => $cart->quantity,
                    'created_at'    => now(),
                    'updated_at'    => now()
                ]);
            }
            if($getCart){
                $deleteCart = DB::table('cart_product')->where('cart_id', '=', $getCart->cart_id)->delete();
            }
        }

        return response()->json(['message'=>'Order placed to POS Successfully']);
    }

}
