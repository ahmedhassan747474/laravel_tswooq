<?php

namespace App\Http\Controllers\AdminControllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Exception;
use Kyslik\ColumnSortable\Sortable;
use App\Models\Core\Images;
use App\Models\Core\Setting;
use App\Models\Core\Languages;
use App\Models\Core\Suppliers;
use App\Models\Core\Categories;
use App\Models\Core\User;
use Auth;
use Session;

class POSCardController extends Controller
{
    public function __construct(Suppliers $suppliers, Setting $setting)
    {
        $this->Suppliers = $suppliers;
        $this->myVarsetting = new SiteSettingController($setting);
        $this->Setting = $setting;
    }

    public function display()
    {
        $title = array('pageTitle' => Lang::get("labels.ListingCustomers"));
        $language_id = '1';
        $result = array();
        $result['commonContent'] = $this->Setting->commonContent();

        $language_id = Session::get('language_id') ? Session::get('language_id') : '1';

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://taxes.like4app.com/online/categories",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => array(
                'deviceId' => '5b3e7f9bfb09c2a60d835794282f589d2fc4bfa89cc093c574ee76126dbc0b86',
                'email' => 'Jaber2800@hotmail.com',
                'password' => 'c0bf116b36be1ec7d90bf6a520c1c350',
                'securityCode' => '9a328e9f300dfd45f54e48c12df75363',
                'langId' => $language_id,
            ),
            CURLOPT_HTTPHEADER => array(
                // "Content-Type: application/x-www-form-urlencoded"
            ),
        ));

        do{             
            $response = curl_exec($curl);         
            
        }while(!$response);        
        curl_close($curl);
        $categories = json_decode($response);

        $customers = User::all();
        $countries = DB::table('countries')->select('countries_id as id', 'countries_name as name')->get();
        $results = array();
        $results['categories'] = $categories;
        $results['customers'] = $customers;
        $results['countries'] = $countries;
        return view("admin.pos_card.index", compact('results', 'result'));
    }

    public function search(Request $request)
    {
        $language_id = Session::get('language_id') ? Session::get('language_id') : '1';

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://taxes.like4app.com/online/categories",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => array(
                'deviceId' => '5b3e7f9bfb09c2a60d835794282f589d2fc4bfa89cc093c574ee76126dbc0b86',
                'email' => 'Jaber2800@hotmail.com',
                'password' => 'c0bf116b36be1ec7d90bf6a520c1c350',
                'securityCode' => '9a328e9f300dfd45f54e48c12df75363',
                'langId' => $language_id,
            ),
            CURLOPT_HTTPHEADER => array(
                // "Content-Type: application/x-www-form-urlencoded"
            ),
        ));

        do{             
            $response = curl_exec($curl);         
            
        }while(!$response);        curl_close($curl);
        $categories = json_decode($response);

        if (!empty($request->poscategory) and $request->poscategory != 'all' and empty($request->possubcategory)and $request->possubcategory == 'all') {
            $category_id = $request->poscategory;
        } elseif (!empty($request->possubcategory)and $request->possubcategory != 'all') {
            $category_id = $request->possubcategory;
        }elseif (!empty($request->possubofsubcategory)and $request->possubofsubcategory != 'all') {
                $category_id = $request->possubofsubcategory;
        } else {
            $category_id = $categories->data[0]->childs[0]->id;
        }

        // if (!empty($request->possubofsubcategory)and $request->possubofsubcategory != 'all') {
        //         $category_id = $request->possubofsubcategory;
        // }
        // dd($request->all(), $category_id);

        $curl2 = curl_init();

        curl_setopt_array($curl2, array(
            CURLOPT_URL => "https://taxes.like4app.com/online/products",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => array(
                'deviceId' => '5b3e7f9bfb09c2a60d835794282f589d2fc4bfa89cc093c574ee76126dbc0b86',
                'email' => 'Jaber2800@hotmail.com',
                'password' => 'c0bf116b36be1ec7d90bf6a520c1c350',
                'securityCode' => '9a328e9f300dfd45f54e48c12df75363',
                'langId' => $language_id,
				'categoryId' => $category_id ? $category_id : $categories->data[0]->childs[0]->id,
            ),
            CURLOPT_HTTPHEADER => array(
                // "Content-Type: application/x-www-form-urlencoded"
            ),
        ));

        $response2 = curl_exec($curl2);

        curl_close($curl2);

        $response2 = json_decode($response2);

        return response()->json($response2);
    }

    public function products($data)
    {
        $language_id = Session::get('language_id') ? Session::get('language_id') : '1';

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://taxes.like4app.com/online/categories",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => array(
                'deviceId' => '5b3e7f9bfb09c2a60d835794282f589d2fc4bfa89cc093c574ee76126dbc0b86',
                'email' => 'Jaber2800@hotmail.com',
                'password' => 'c0bf116b36be1ec7d90bf6a520c1c350',
                'securityCode' => '9a328e9f300dfd45f54e48c12df75363',
                'langId' => $language_id,
            ),
            CURLOPT_HTTPHEADER => array(
                // "Content-Type: application/x-www-form-urlencoded"
            ),
        ));

        do{             
            $response = curl_exec($curl);         
            
        }while(!$response);        curl_close($curl);
        $categories = json_decode($response);

        if (!empty($request->poscategory) and $request->poscategory != 'all' and empty($request->possubcategory)and $request->possubcategory == 'all') {
            $category_id = $request->poscategory;
        } elseif (!empty($request->poscategory) and $request->poscategory != 'all' and !empty($request->possubcategory)and $request->possubcategory != 'all') {
            $category_id = $request->possubcategory;
        } else {
            $category_id = $categories->data[0]->childs[0]->id;
        }

        dd($category_id);

        $curl2 = curl_init();

        curl_setopt_array($curl2, array(
            CURLOPT_URL => "https://taxes.like4app.com/online/products",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => array(
                'deviceId' => '5b3e7f9bfb09c2a60d835794282f589d2fc4bfa89cc093c574ee76126dbc0b86',
                'email' => 'Jaber2800@hotmail.com',
                'password' => 'c0bf116b36be1ec7d90bf6a520c1c350',
                'securityCode' => '9a328e9f300dfd45f54e48c12df75363',
                'langId' => $language_id,
				'categoryId' => $category_id ? $category_id : $categories->data[0]->childs[0]->id,
            ),
            CURLOPT_HTTPHEADER => array(
                // "Content-Type: application/x-www-form-urlencoded"
            ),
        ));

        $response2 = curl_exec($curl2);

        curl_close($curl2);

        $response2 = json_decode($response2);

        $responseData = array(
            'success' => '1', 
            'product_data' => $response2, 
            'message' => Lang::get('website.Returned all products'), 
            // 'total_record' => count($total_record),
            // 'paginate' => $paginate
        );

        return ($responseData);
    }

    public function getVarinats(Request $request){
        return 0;
    }

    public function addToCart(Request $request)
    {
        // dd($request->all());

        $subtotal=0;
        if(Session::has('posCardCart')){
            foreach (Session::get('posCardCart') as $key => $cartItem){
                $subtotal += $cartItem['price']*$cartItem['quantity'];
    
            }
            $subtotal +=$request->product_price*$request->quantity;
    
            if($subtotal > auth()->user()->like_limit){
                return 'Please check your limit value';
                // return redirect()->back()->with('message','Please check your limit value');
            }
        }
        
        
        $data = array();
        
        $tax = 0;
        $data['variant'] = null;

        $tax = 0;
        $data['id'] = $request->product_id;
        $data['name'] = $request->product_name;
        $data['price'] = $request->product_price;
        $data['image'] = $request->product_image;
        $data['currency'] = $request->product_currency;
        $data['quantity'] = $request->quantity;
        $data['tax'] = $tax;

        if($request->session()->has('posCardCart')){
            $foundInCart = false;
            $cart = collect();

            foreach ($request->session()->get('posCardCart') as $key => $cartItem){
                $cart->push($cartItem);
            }

            if (!$foundInCart) {
                $cart->push($data);
            }
            $request->session()->put('posCardCart', $cart);
        }
        else{
            $cart = collect([$data]);
            $request->session()->put('posCardCart', $cart);
        }

        return view('admin.pos_card.cart');
    }

    //updated the quantity for a cart item
    public function updateQuantity(Request $request)
    {
        $subtotal=0;
        if(Session::has('posCardCart')){
            foreach (Session::get('posCardCart') as $key => $cartItem){
                $subtotal += $cartItem['price']*$cartItem['quantity'];
    
            }
            $subtotal +=$request->product_price*$request->quantity;
    
            if($subtotal > auth()->user()->like_limit){
                return 'Please check your limit value';
                // return redirect()->back()->with('message','Please check your limit value');
            }
        }
        
        
        $cart = $request->session()->get('posCardCart', collect([]));
        $cart = $cart->map(function ($object, $key) use ($request) {
            if($key == $request->key){
                $object['quantity'] = $request->quantity;
            }
            return $object;
        });
        $request->session()->put('posCardCart', $cart);

        return view('admin.pos_card.cart');
    }

    //removes from Cart
    public function removeFromCart(Request $request)
    {
        if(Session::has('posCardCart')){
            $cart = Session::get('posCardCart', collect([]));
            $cart->forget($request->key);
            Session::put('posCardCart', $cart);
        }

        return view('admin.pos_card.cart');
    }

    //Shipping Address for admin
    public function getShippingAddress(Request $request){
        return view('admin.pos.guest_shipping_address');
    }

    //Shipping Address for seller
    public function getShippingAddressForSeller(Request $request){
        $user_id = $request->id;
        if($user_id == ''){
            return view('pos.frontend.seller.pos.guest_shipping_address');
        }
        else{
            return view('pos.frontend.seller.pos.shipping_address', compact('user_id'));
        }
    }

    //order place
    public function order_store(Request $request)
    {
        // dd($request->all());
        if(Session::has('posCardCart') && count(Session::get('posCardCart')) > 0){
            // $order = new Order;
            $first_name = '';
            $last_name = '';
            $email = '';
            $address = '';
            $country = '';
            $city = '';
            $postal_code = '';
            $phone = '';

            if ($request->user_id == null) {

                $email = $request->email;
                if($email != null) {
                    $check = DB::table('users')->where('role_id', 2)->where('email', $email)->first();
                    if ($check == null) {
                        $customers_id = DB::table('users')
                            ->insertGetId([
                                'role_id' => 2,
                                'email' => $request->email,
                                'password' => Hash::make('123456789'),
                                'first_name' => $request->first_name,
                                'last_name' => $request->last_name,
                                'phone' => $request->phone,
                            ]);
                        session(['customers_id' => $customers_id]);
                    } else {
                        $customers_id = $check->id;
                        $email = $check->email;
                        session(['customers_id' => $customers_id]);
                    }
                } else {
                    $customers_id = auth()->user()->id;
                    $email = auth()->user()->email;
                    session(['customers_id' => $customers_id]);
                }

                // $order->guest_id    = mt_rand(100000, 999999);
                $first_name         = $request->first_name;
                $last_name          = $request->last_name;
                // $email              = $request->email;
                $shipping_address   = $request->shipping_address;
                $address            = $request->address;
                $country            = $request->country;
                $city               = $request->city;
                $postal_code        = $request->postal_code;
                $phone              = $request->phone;

                if ($request->customer_id) {
                    $get_detail_customer = DB::table('users')->where('id', $request->customer_id)->first();
                    if ($get_detail_customer) {
                        $first_name         = $get_detail_customer->first_name;
                        $last_name          = $get_detail_customer->last_name;
                        $email              = $get_detail_customer->email;
                        $phone              = $get_detail_customer->phone;
                    }
                }
            }
            else {
                $order->user_id = $request->user_id;
                $user           = User::findOrFail($request->user_id);
                $name   = $user->name;
                $email  = $user->email;

                if($request->shipping_address != null){
                    $address_data   = Address::findOrFail($request->shipping_address);
                    $address        = $address_data->address;
                    $country        = $address_data->country;
                    $city           = $address_data->city;
                    $postal_code    = $address_data->postal_code;
                    $phone          = $address_data->phone;
                }
            }

            $delivery_firstname = $first_name;
            $delivery_lastname = $last_name;
            $delivery_street_address = $shipping_address;
            $delivery_suburb = '';
            $delivery_city = $city;
            $delivery_postcode = $postal_code;
            $delivery_phone = $phone;
            $delivery_state = 'other';
            $delivery_country = $country;

            $billing_firstname = $first_name;
            $billing_lastname = $last_name;
            $billing_street_address = $shipping_address;
            $billing_suburb = '';
            $billing_city = $city;
            $billing_postcode = $postal_code;
            $billing_phone = $phone;
            $billing_state = 'other';
            $billing_country = $country;

            $cc_type = '';
            $cc_owner = '';
            $cc_number = '';
            $cc_expires = '';

            $last_modified = date('Y-m-d H:i:s');
            $date_purchased = date('Y-m-d H:i:s');

            $payment_method = 'cash_on_delivery';
            $shipping_method = "";//smsaexpress
            $order_information = array();

            $taxes_rate = 0;
            foreach (Session::get('posCardCart') as $key => $cartItem){
                $taxes_rate += $cartItem['tax']*$cartItem['quantity'];
            }
            $tax_rate = $taxes_rate;
            $order_price = $request->total_price;

            $orders_status = '1';
            $code = '';
            $coupon_amount = '';
            $comments = $request->comment;
            $date_added = date('Y-m-d h:i:s');

            $web_setting = DB::table('settings')->get();
            $currency = $request->currency;//$web_setting[19]->value;
            // $total_tax = number_format((float) session('tax_rate'), 2, '.', '');
            $products_tax = 1;

            if($request->payment_type == 'cash') {
                $payment_method_name = 'Cash';
            } else {
                $payment_method_name = 'Visa';
            }

            if($request->first_name && $request->first_name){
                $customers_name = $delivery_firstname . ' ' . $delivery_lastname;
            } else {
                $customers_name = null;
            }
            
            $orders_id = DB::table('order_like_card')->insertGetId([
                'customers_id' => $customers_id,
                'customers_name' => $customers_name,
                'customers_street_address' => $delivery_street_address,
                'customers_suburb' => $delivery_suburb,
                'customers_city' => $delivery_city,
                'customers_postcode' => $delivery_postcode,
                'customers_state' => $delivery_state,
                'customers_country' => $delivery_country,
                //'customers_telephone' => $customers_telephone,
                'email' => $email,
                'delivery_name' => $customers_name,
                'delivery_street_address' => $delivery_street_address,
                'delivery_suburb' => $delivery_suburb,
                'delivery_city' => $delivery_city,
                'delivery_postcode' => $delivery_postcode,
                'delivery_state' => $delivery_state,
                'delivery_country' => $delivery_country,
                'billing_name' => $customers_name,
                'billing_street_address' => $billing_street_address,
                'billing_suburb' => $billing_suburb,
                'billing_city' => $billing_city,
                'billing_postcode' => $billing_postcode,
                'billing_state' => $billing_state,
                'billing_country' => $billing_country,
                'payment_method' => $payment_method_name,
                'last_modified' => $last_modified,
                'date_purchased' => $date_purchased,
                'order_price' => $order_price,
                'currency' => $currency,
                'total_tax' => $tax_rate,
                'delivery_phone' => $delivery_phone,
                'billing_phone' => $billing_phone,
                'admin_id'  => auth()->user()->id
            ]);

            if($orders_id) {
                $subtotal = 0;
                $tax = 0;
                $total_tax = 0;

                foreach (Session::get('posCardCart') as $key => $cartItem){
                    $subtotal += $cartItem['price']*$cartItem['quantity'];
                    $tax += $cartItem['tax']*$cartItem['quantity'];

                    $product_variation = $cartItem['variant'];

                    $total_tax += $cartItem['tax']*$cartItem['quantity'];

                    $orders_products_id = DB::table('order_like_card_product')->insertGetId([
                        'order_like_card_id' => $orders_id,
                        'product_id' => $cartItem['id'],
                        'product_name' => $cartItem['name'],
                        'product_price' => $cartItem['price'],
                        'product_currency' => $cartItem['currency'],
                        'product_image' => $cartItem['image'],
                        'final_price' => $subtotal + $total_tax,
                        'products_tax' => $products_tax,
                        'product_quantity' => $cartItem['quantity'],
                        'created_at' => $date_purchased
                    ]);
                }

                $request->session()->put('order_id', $orders_id);

                Session::forget('pos_shipping_info');
                Session::forget('posCardCart');
    
                $responseData = array(
                    'success' => '1', 
                    'data' => 1, 
                    'message' => "Order has been placed successfully.",
                    'order_id' => $orders_id,
                    // 'print_url' => route('invoiceprint')
                );
                return $responseData;
            }
            else {
                return 0;
            }
        }
        return 0;
    }
}
