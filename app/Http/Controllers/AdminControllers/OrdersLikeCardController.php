<?php

namespace App\Http\Controllers\AdminControllers;

use App\Http\Controllers\AdminControllers\SiteSettingController;
use App\Http\Controllers\Controller;
use App\Models\Core\Setting;
use App\Models\Core\OrderProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use App\Models\Core\OrderLikeCard;
use Illuminate\Support\Facades\Session;

class OrdersLikeCardController extends Controller
{
    function decryptSerial($encrypted_txt){    
      $secret_key = 't-3zafRa';    
      $secret_iv = 'St@cE4eZ';
      $encrypt_method = 'AES-256-CBC';                
      $key = hash('sha256', $secret_key);        
    
      //iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning          
      $iv = substr(hash('sha256', $secret_iv), 0, 16);        
    
      return openssl_decrypt(base64_decode($encrypted_txt), $encrypt_method, $key, 0, $iv);        
    }

    //
    public function __construct( Setting $setting, OrderLikeCard $order )
    {
        $this->myVarsetting = new SiteSettingController($setting);
        $this->Setting = $setting;
        $this->Order = $order;
    }

    //add listingOrders
    public function balance()
    {
        $title = array('pageTitle' => Lang::get("labels.ListingOrders"));        

        $language_id = Session::get('language_id') ? Session::get('language_id') : '1';

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://taxes.like4app.com/online/check_balance",
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
        $balance = json_decode($response);

        $message = array();
        $errorMessage = array();        
        
        $ordersData['message'] = $message;
        $ordersData['errorMessage'] = $errorMessage;
        $ordersData['currency'] = $this->myVarsetting->getSetting(); 
        $result['commonContent'] = $this->Setting->commonContent();
        return view("admin.Orders_likecard.balance", $title)->with('listingOrders', $ordersData)->with('result', $result)->with('balance', $balance);
    }

    public function display()
    {
   
     
        $title = array('pageTitle' => Lang::get("labels.ListingOrders"));        

        $message = array();
        $errorMessage = array();        
        
        if(auth()->user()->role_id == 1){
             $ordersData['orders'] =OrderLikeCard::orderby('id','DESC')->paginate(20); 
        }
        else{
            
        $ordersData['orders'] =OrderLikeCard::where('customers_id',auth()->user()->id)->orderby('id','DESC')->paginate(20);
        }

        $ordersData['message'] = $message;
        $ordersData['errorMessage'] = $errorMessage;
        $ordersData['currency'] = $this->myVarsetting->getSetting(); 
        $result['commonContent'] = $this->Setting->commonContent();
        return view("admin.Orders_likecard.index", $title)->with('listingOrders', $ordersData)->with('result', $result);
    }

    //view order detail
    public function vieworder($id,Request $request)
    { 
        $order=OrderProduct::find($id);
     
        if($order->orderCard!=null){
            
            
            if($order->orderCard->order_like_card_id !=null){
                $order_details=deatailsOrder($order->orderCard->order_like_card_id);
            }
            else{
                $order_details='';
            }
        }
        else{
          $order_details='';
  
        }
    
        $title = array('pageTitle' => Lang::get("labels.ViewOrder"));
        $message = array();
        $errorMessage = array();

        //orders data
        $ordersData = $this->Order->detail($request);        

        // current order status
        $orders_status_history = $this->Order->currentOrderStatus($request);  
        

        //all statuses 
        $orders_status = $this->Order->orderStatuses(); 
           
        
        $ordersData['message'] = $message;
        $ordersData['errorMessage'] = $errorMessage;
        $ordersData['orders_status'] = $orders_status;
        $ordersData['orders_status_history'] = $orders_status_history;

        //get function from other controller
        $ordersData['currency'] = $this->myVarsetting->getSetting();
        $result['commonContent'] = $this->Setting->commonContent();

        //dd($ordersData);
        $data=array();
        // dd($order);
        $order_details=deatailsOrder($order->orderCard->order_like_card_id);
        $orderNumber = $order_details->orderNumber??0000;
        
        $serials = $order_details->serials[0]??0000;
        
        $serialNumber = $serials->serialNumber??0000;
        
        $serialCode = $this->decryptSerial($serials->serialCode)??0000;
        
        // dd($serialCode);
        
        $data['orderNumber'] = $order_details->orderNumber??0000;
        
        $data['serialNumber'] = $serials->serialNumber??0000;
        
        $data['serialCode'] = $this->decryptSerial($serials->serialCode)??0000;

         return view("admin.Orders_likecard.vieworder",compact('data','order_details','title','ordersData','result','order'));
  
    }

    //update order
    public function updateOrder(Request $request)
    {

        $orders_status = $request->orders_status;
        $old_orders_status = $request->old_orders_status;

        $comments = $request->comments;
        $orders_id = $request->orders_id;

        //get function from other controller
        $setting = $this->myVarsetting->getSetting();       

        if ($old_orders_status == $orders_status) {
            return redirect()->back()->with('error', Lang::get("labels.StatusChangeError"));
        } else {
            //update order
            $orders_status = $this->Order->updateRecord($request);  
            return redirect()->back()->with('message', Lang::get("labels.OrderStatusChangedMessage"));
        }

    }

    //deleteorders
    public function deleteOrder(Request $request)
    {       
        //reverse stock
        $this->Order->reverseStock($request);     
        $this->Order->deleteRecord($request);
        
        return redirect()->back()->withErrors([Lang::get("labels.OrderDeletedMessage")]);
    }

    //view order detail
    public function invoiceprint(Request $request)
    {

        $title = array('pageTitle' => Lang::get("labels.ViewOrder"));
        $language_id = '1';
        $orders_id = $request->id;

        $message = array();
        $errorMessage = array();

        DB::table('orders')->where('orders_id', '=', $orders_id)
            ->where('customers_id', '!=', '')->update(['is_seen' => 1]);

        $order = DB::table('orders')
            ->LeftJoin('orders_status_history', 'orders_status_history.orders_id', '=', 'orders.orders_id')
            ->LeftJoin('orders_status', 'orders_status.orders_status_id', '=', 'orders_status_history.orders_status_id')
            ->LeftJoin('orders_status_description', 'orders_status_description.orders_status_id', '=', 'orders_status.orders_status_id')
            ->where('orders_status_description.language_id', '=', $language_id)->where('orders_status.role_id', '<=', 2)
            ->where('orders.orders_id', '=', $orders_id)->orderby('orders_status_history.date_added', 'DESC')->get();

        foreach ($order as $data) {
            $orders_id = $data->orders_id;

            $orders_products = DB::table('orders_products')
                ->join('products', 'products.products_id', '=', 'orders_products.products_id')
                ->select('orders_products.*', 'products.products_image as image')
                ->where('orders_products.orders_id', '=', $orders_id)->get();
            $i = 0;
            $total_price = 0;
            $total_tax = 0;
            $product = array();
            $subtotal = 0;
            foreach ($orders_products as $orders_products_data) {

                //categories
                $categories = DB::table('products_to_categories')
                    ->leftjoin('categories', 'categories.categories_id', 'products_to_categories.categories_id')
                    ->leftjoin('categories_description', 'categories_description.categories_id', 'products_to_categories.categories_id')
                    ->select('categories.categories_id', 'categories_description.categories_name', 'categories.categories_image', 'categories.categories_icon', 'categories.parent_id')
                    ->where('products_id', '=', $orders_products_data->orders_products_id)
                    ->where('categories_description.language_id', '=', $language_id)->get();

                $orders_products_data->categories = $categories;

                $product_attribute = DB::table('orders_products_attributes')
                    ->where([
                        ['orders_products_id', '=', $orders_products_data->orders_products_id],
                        ['orders_id', '=', $orders_products_data->orders_id],
                    ])
                    ->get();

                $orders_products_data->attribute = $product_attribute;
                $product[$i] = $orders_products_data;
                $total_price = $total_price + $orders_products[$i]->final_price;

                $subtotal += $orders_products[$i]->final_price;

                $i++;
            }
            $data->data = $product;
            $orders_data[] = $data;
        }

        $orders_status_history = DB::table('orders_status_history')
            ->LeftJoin('orders_status', 'orders_status.orders_status_id', '=', 'orders_status_history.orders_status_id')
            ->LeftJoin('orders_status_description', 'orders_status_description.orders_status_id', '=', 'orders_status.orders_status_id')
            ->where('orders_status_description.language_id', '=', $language_id)->where('orders_status.role_id', '<=', 2)
            ->orderBy('orders_status_history.date_added', 'desc')
            ->where('orders_id', '=', $orders_id)->get();

        $orders_status = DB::table('orders_status')->LeftJoin('orders_status_description', 'orders_status_description.orders_status_id', '=', 'orders_status.orders_status_id')
            ->where('orders_status_description.language_id', '=', $language_id)->where('orders_status.role_id', '<=', 2)->get();

        $ordersData['message'] = $message;
        $ordersData['errorMessage'] = $errorMessage;
        $ordersData['orders_data'] = $orders_data;
        $ordersData['total_price'] = $total_price;
        $ordersData['orders_status'] = $orders_status;
        $ordersData['orders_status_history'] = $orders_status_history;
        $ordersData['subtotal'] = $subtotal;

        //get function from other controller

        $ordersData['currency'] = $this->myVarsetting->getSetting();
        $result['commonContent'] = $this->Setting->commonContent();

        return view("admin.Orders_likecard.invoiceprint", $title)->with('data', $ordersData)->with('result', $result);

    }
    
    public function invoiceprintnew(Request $request)
    {
        // $data = \App\Models\Core\OrderLikeCard::whereId($request->id)->first()->detail($request);
        $data = OrderProduct::find($request->id)->orderCard->detail($request);
        // dd($data);
        $data['orders_data'][0]['orders_id'] = $request->id;
        
        $data['orders_data'][0]['shipping_method'] = '';

        $result['commonContent'] = $this->Setting->commonContent();

        $order=OrderProduct::find($request->id);
        $order_details=deatailsOrder($order->orderCard->order_like_card_id);
        $result['order_details'] = $order_details;
        
        // dd($order_details);
        $orderNumber = $order_details->orderNumber??0000;
        
        $serials = $order_details->serials[0]??0000;
        
        $serialNumber = $serials->serialNumber??0000;
        
        $serialCode = $this->decryptSerial($serials->serialCode)??0000;
        
        $data['orderNumber'] = $order_details->orderNumber??0000;
        
        $data['serialNumber'] = $serials->serialNumber??0000;
        
        $data['serialCode'] = $this->decryptSerial($serials->serialCode)??0000;


        return view("admin.Orders_likecard.invoiceprint")->with('data', $data)->with('result', $result);
    }

    public function assignOrders(Request $request)
    {
        $orders = $this->Order->assignOrders($request);
        return redirect()->back()->with('message', Lang::get("labels.Orders successfully assigned to the delivery boy"));
    }

}
