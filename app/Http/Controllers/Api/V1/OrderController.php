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
use Illuminate\Support\Facades\File;
use App\Models\AppModels\Product;
use Carbon;
use DB;

class OrderController extends BaseController
{
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
            $getCart = DB::table('carts')->where('user_id', $user->id)->first();

            if($getCart){
                $getCart->products = DB::table('cart_product')->where('cart_id', '=', $getCart->cart_id)->get();
            }

            return response()->json($getCart);
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

            $insertCart = DB::table('carts')->insertGetId([
                'user_id'       => $user->id,
                'created_at'    => now(),
                'updated_at'    => now()
            ]);

            $insertCartProduct = DB::table('cart_product')->insert([
                'cart_id'       => $insertCart,
                'product_id'    => $request->product_id,
                'quantity'      => $request->quantity,
                'created_at'    => now(),
                'updated_at'    => now()
            ]);

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
            foreach($request->products as $products){
                $req = array();
                $req['products_id'] = $products['products_id'];
                $attr = array();
                
                // if (isset($products['attributes'])) {
                //     foreach ($products['attributes'] as $key => $value) {
                //         $attr[$key] = $value['products_options_id'];
                //     }
                //     $req['attributes'] = $attr;
                // }
                
                $check = Product::getquantity($req);
                $check = json_decode($check);

                if($products['customers_basket_quantity'] > $check->stock){
                    $responseData = array('success'=>'1', 'data'=>array(),'products_id' => $products['products_id'], 'message'=>"Some Products are out of Stock.");
                    // $orderResponse = json_encode($responseData);
                    // return $orderResponse;
                    return response()->json($responseData);
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
            $order_price                        =   $request->totalPrice;
            $currency_code                      =   $request->currency_code;
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

            }else if($payment_method == 'cod'){

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
            }else if($payment_method == 'paytm'){
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
            }

            //check if order is verified
            if($payment_status=='success'){
                if( $payment_method == 'hyperpay'){
                    $cyb_orders = DB::table('orders')->where('transaction_id','=',$request->transaction_id)->get();
                    $orders_id = $cyb_orders[0]->orders_id;

                    //update database
                    DB::table('orders')->where('transaction_id','=',$request->transaction_id)->update(
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
                            'coupon_code'		 =>		$code,
                            'coupon_amount' 	 =>		$coupon_amount,
                            'total_tax'		 =>		$total_tax,
                            'ordered_source' 	 => 	'2',
                            'delivery_phone'	 =>		$delivery_phone,
                            'billing_phone'	 =>		$billing_phone
                        ]);

                }else{

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
                        'delivery_longitude' => $delivery_longitude
                    ]);

                }
                
                //orders status history
                $orders_history_id = DB::table('orders_status_history')->insertGetId(
                    [	 'orders_id'  => $orders_id,
                        'orders_status_id' => $orders_status,
                        'date_added'  => $date_added,
                        'customer_notified' =>'1',
                        'comments'  =>  $comments
                    ]);

                foreach($request->products as $products){
                    //dd($products['price'], $currency_code);
                    $c_price = str_replace(',','',$products['price']);
                    $c_final_price = str_replace(',','',$products['final_price']);
                    $price = Orders::converttodefaultprice($c_price, $currency_code);
                    $final_price = $c_final_price*$products['customers_basket_quantity'];
                    $final_price = Orders::converttodefaultprice($final_price, $currency_code);

                    $orders_products_id = DB::table('orders_products')->insertGetId(
                    [
                        'orders_id' 		 => 	$orders_id,
                        'products_id' 	 	 =>		$products['products_id'],
                        'products_name'	 => 	$products['products_name'],
                        'products_price'	 =>  	$price,
                        'final_price' 		 =>  	$final_price,
                        'products_tax' 	 =>  	$products_tax,
                        'products_quantity' =>  	$products['customers_basket_quantity'],
                    ]);

                    $inventory_ref_id = DB::table('inventory')->insertGetId([
                        'products_id'   		=>   $products['products_id'],
                        'reference_code'  		=>   '',
                        'stock'  				=>   $products['customers_basket_quantity'],
                        'admin_id'  			=>   0,
                        'added_date'	  		=>   time(),
                        'purchase_price'  		=>   0,
                        'stock_type'  			=>   'out',
                    ]);


                    if(!empty($products['attributes'])){
                        foreach($products['attributes'] as $attribute){
                            DB::table('orders_products_attributes')->insert(
                            [
                                'orders_id' => $orders_id,
                                'products_id'  => $products['products_id'],
                                'orders_products_id'  => $orders_products_id,
                                'products_options' =>$attribute['products_options'],
                                'products_options_values'  =>  $attribute['products_options_values'],
                                'options_values_price'  =>  $attribute['options_values_price'],
                                'price_prefix'  =>  $attribute['price_prefix']
                            ]);

                            $products_attributes = DB::table('products_attributes')->where([
                                ['options_id', '=', $attribute['products_options_id']],
                                ['options_values_id', '=', $attribute['products_options_values_id']],
                            ])->get();

                            DB::table('inventory_detail')->insert([
                                'inventory_ref_id'  =>   $inventory_ref_id,
                                'products_id'  		=>   $products['products_id'],
                                'attribute_id'		=>   $products_attributes[0]->products_attributes_id,
                            ]);

                        }
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

            return response()->json($responseData);
        } else {
            return response()->json(['message' => trans('common.user_not_exist'), 'status_code' => 401], 401);
        }
        
    }

    //getorders
    public function getorders(Request $request){
        $user = $this->getAuthenticatedUser();

        if($user)
        {
            $customers_id                   =  $user->id;
            $language_id                    =  $request->language_id;
            $requested_currency             =  'SAR';//$request->currency_code;
            $order = DB::table('orders')->orderBy('customers_id', 'desc')
                ->where([
                ['customers_id', '=', $customers_id],
                ])->get();


            if(count($order) > 0){
            //foreach
            $index = '0';
            foreach($order as $data){

                // deliveryboy 
                $current_boy = DB::table('orders_to_delivery_boy')
                        ->leftjoin('users', 'users.id', '=', 'orders_to_delivery_boy.deliveryboy_id')
                        ->LeftJoin('deliveryboy_info', 'deliveryboy_info.users_id', '=', 'users.id')
                        ->select('orders_to_delivery_boy.*',
                        'users.*',
                        'deliveryboy_info.*',
                        'deliveryboy_info.users_id as deliveryboy_id'                 
                        )
                        ->where('orders_to_delivery_boy.orders_id', '=', $data->orders_id)
                        ->where('orders_to_delivery_boy.is_current', '=', '1')
                        ->orderby('orders_to_delivery_boy.created_at', 'DESC')
                        ->get();
                
                if(count($current_boy)>0){
                $data->deliveryboy_info = $current_boy; 
                }else{
                $data->deliveryboy_info = array(); 
                }
                
                $data->total_tax     =  Orders::convertprice($data->total_tax, $requested_currency);
                $data->order_price   =  Orders::convertprice($data->order_price, $requested_currency);
                $data->shipping_cost =  Orders::convertprice($data->shipping_cost, $requested_currency);
                $data->coupon_amount =  Orders::convertprice($data->coupon_amount, $requested_currency);

                if(!empty($data->product_discount_percentage)){
                $product_ids = explode(',', $coupons[0]->product_ids);
                $data->product_ids =  $product_ids;
                }
                else{
                $data->product_ids = array();
                }

                if(!empty($data->discount_type)){
                $exclude_product_ids = explode(',', $data->discount_type);
                $data->discount_type =  $exclude_product_ids;
                }else{
                $data->discount_type =  array();
                }

                if(!empty($data->amount)){
                $product_categories = explode(',', $data[0]->amount);
                $data->amount =  $product_categories;
                }else{
                $data->amount =  array();
                }

                if(!empty($data->product_ids)){
                $excluded_product_categories = explode(',', $data->product_ids);
                $data->product_ids =  $excluded_product_categories;
                }else{
                $data->product_ids = array();
                }

                if(!empty($data->exclude_product_ids)){
                $email_restrictions = explode(',', $data->exclude_product_ids);
                $data->exclude_product_ids =  $email_restrictions;
                }else{
                $data->exclude_product_ids =  array();
                }

                if(!empty($data->usage_limit)){
                $used_by = explode(',', $data->usage_limit);
                $data->usage_limit =  $used_by;
                }else{
                $data->usage_limit =  array();
                }

                if(!empty($data->product_categories)){
                $used_by = explode(',', $data->product_categories);
                $data->product_categories =  $used_by;
                }else{
                $data->product_categories =  array();
                }

                if(!empty($data->excluded_product_categories)){
                $used_by = explode(',', $data->excluded_product_categories);
                $data->excluded_product_categories =  $used_by;
                }else{
                $data->excluded_product_categories =  array();
                }

                if(!empty($data->coupon_code)){

                $coupon_code =  $data->coupon_code;

                $coupon_datas = array();
                $index_c = 0;
                foreach(json_decode($coupon_code) as $coupon_codes){

                    if(!empty($coupon_codes->code)){
                    $code = explode(',', $coupon_codes->code);
                    $coupon_datas[$index_c]['code'] =  $code[0];
                    }else{
                    $coupon_datas[$index_c]['code'] =  '';
                    }

                    if(!empty($coupon_codes->amount)){
                    $amount = explode(',', $coupon_codes->amount);
                    $amount =  Orders::convertprice($amount[0], $requested_currency);
                    $coupon_datas[$index_c]['amount'] =  $amount;
                    }else{
                    $coupon_datas[$index_c]['amount'] =  '';
                    }


                    if(!empty($coupon_codes->discount_type)){
                    $discount_type = explode(',', $coupon_codes->discount_type);
                    $coupon_datas[$index_c]['discount_type'] =  $discount_type[0];
                    }else{
                    $coupon_datas[$index_c]['discount_type'] =  '';
                    }

                    $index_c++;
                }
                $order[$index]->coupons = $coupon_datas;
                }
                else{
                $coupon_code =  array();
                $order[$index]->coupons = $coupon_code;
                }

                unset($data->coupon_code);

                $orders_id	 = $data->orders_id;

                $orders_status_history = DB::table('orders_status_history')
                    ->LeftJoin('orders_status', 'orders_status.orders_status_id', '=', 'orders_status_history.orders_status_id')
                    ->LeftJoin('orders_status_description', 'orders_status_description.orders_status_id', '=', 'orders_status_history.orders_status_id')
                    ->select('orders_status_description.orders_status_name', 'orders_status.orders_status_id', 'orders_status_history.comments')
                    ->where('orders_id', '=', $orders_id)
                    ->where('orders_status.role_id','=',2)->orderby('orders_status_history.orders_status_history_id', 'ASC')->get();

                $order[$index]->orders_status_id = $orders_status_history[0]->orders_status_id;
                $order[$index]->orders_status = $orders_status_history[0]->orders_status_name;
                $order[$index]->customer_comments = $orders_status_history[0]->comments;

                $total_comments = count($orders_status_history);
                $i = 1;

                foreach($orders_status_history as $orders_status_history_data){

                if($total_comments == $i && $i != 1){
                    $order[$index]->orders_status_id = $orders_status_history_data->orders_status_id;
                    $order[$index]->orders_status = $orders_status_history_data->orders_status_name;
                    $order[$index]->admin_comments = $orders_status_history_data->comments;
                }else{
                    $order[$index]->admin_comments = '';
                }

                $i++;
                }

                $orders_products = DB::table('orders_products')
                ->join('products', 'products.products_id','=', 'orders_products.products_id')
                ->LeftJoin('image_categories', function ($join) {
                $join->on('image_categories.image_id', '=', 'products.products_image')
                    ->where(function ($query) {
                        $query->where('image_categories.image_type', '=', 'THUMBNAIL')
                            ->where('image_categories.image_type', '!=', 'THUMBNAIL')
                            ->orWhere('image_categories.image_type', '=', 'ACTUAL');
                    });
                })
                ->select('orders_products.*', 'image_categories.path as image')
                ->where('orders_products.orders_id', '=', $orders_id)->get();
                $k = 0;
                $product = array();
                foreach($orders_products as $orders_products_data){
                $orders_products_data->products_price =  Orders::convertprice($orders_products_data->products_price, $requested_currency);
                $orders_products_data->final_price =  Orders::convertprice($orders_products_data->final_price, $requested_currency);
                //categories
                $categories = DB::table('products_to_categories')
                        ->leftjoin('categories','categories.categories_id','products_to_categories.categories_id')
                        ->leftjoin('categories_description','categories_description.categories_id','products_to_categories.categories_id')
                        ->select('categories.categories_id','categories_description.categories_name',
                        'categories.categories_image','categories.categories_icon', 'categories.parent_id')
                        ->where('products_id','=', $orders_products_data->products_id)
                        ->where('categories_description.language_id','=',$language_id)->get();

                $orders_products_data->categories =  $categories;

                $product_attribute = DB::table('orders_products_attributes')
                    ->where([
                    ['orders_products_id', '=', $orders_products_data->orders_products_id],
                    ['orders_id', '=', $orders_products_data->orders_id],
                    ])
                    ->get();

                $orders_products_data->attributes = $product_attribute;
                $product[$k] = $orders_products_data;
                $k++;
                }
                $data->data = $product;
                $orders_data[] = $data;
            $index++;
            }
                $responseData = array('success'=>'1', 'data'=>$orders_data, 'message'=>"Returned all orders.");
            }else{
                $orders_data = array();
                $responseData = array('success'=>'0', 'data'=>$orders_data, 'message'=>"Order is not placed yet.");
            }
            return response()->json($responseData);
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
}