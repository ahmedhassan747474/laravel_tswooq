<?php

namespace App\Http\Controllers\AdminControllers;

use App\Http\Controllers\AdminControllers\SiteSettingController;
use App\Http\Controllers\Controller;
use App\Models\Core\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use App\Models\Core\Order;
// require __DIR__.'/../vendor/autoload.php';
use Mike42\Escpos\PrintConnectors\FilePrintConnector;
use Mike42\Escpos\Printer;
class OrdersController extends Controller
{
    //
    public function __construct( Setting $setting, Order $order )
    {
        $this->myVarsetting = new SiteSettingController($setting);
        $this->Setting = $setting;
        $this->Order = $order;
    }

    //add listingOrders
    public function display()
    {
     
        $title = array('pageTitle' => Lang::get("labels.ListingOrders"));

        $message = array();
        $errorMessage = array();

        $ordersData['orders'] = $this->Order->paginator();
        //dd($ordersData['orders']);

        $ordersData['message'] = $message;
        $ordersData['errorMessage'] = $errorMessage;
        $ordersData['currency'] = $this->myVarsetting->getSetting();
        $result['commonContent'] = $this->Setting->commonContent();
        return view("admin.Orders.index", $title)->with('listingOrders', $ordersData)->with('result', $result);
    }

    //view order detail
    public function vieworder(Request $request)
    {

        $title = array('pageTitle' => Lang::get("labels.ViewOrder"));
        $message = array();
        $errorMessage = array();

        //orders data
        $ordersData = $this->Order->detail($request);

        // dd($ordersData);
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

        //dd($ordersData['orders_data'][0]);

        return view("admin.Orders.vieworder", $title)->with('data', $ordersData)->with('result', $result);
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

        if($request->paied){
            DB::table('orders')
            ->where('orders_id', $orders_id)
            ->update([
                    'paied'=>DB::raw('paied + '.$request->paied)
                ]);
            return redirect()->back()->with('message', Lang::get("labels.OrderStatusChangedMessage"));
        }

        if ($old_orders_status == $orders_status && $request->paied==0) {
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
        $language_id = 2;
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


                // dd($orders_products);
            $i = 0;
            $total_price = 0;
            $total_tax = 0;
            $product = array();
            $subtotal = 0;
            // dd($order);
            // dd($orders_products_new);
            foreach ($orders_products as $orders_products_data) {

                //categories
                $categories = DB::table('products_to_categories')
                    ->leftjoin('categories', 'categories.categories_id', 'products_to_categories.categories_id')
                    ->leftjoin('categories_description', 'categories_description.categories_id', 'products_to_categories.categories_id')
                    ->select('categories.categories_id', 'categories_description.categories_name', 'categories.categories_image', 'categories.categories_icon', 'categories.parent_id')
                    ->where('products_id', '=', $orders_products_data->orders_products_id)
                    ->where('categories_description.language_id', '=', $language_id)->get();

                    $products_name=DB::table('products_description')
                                    ->where('products_id', '=', $orders_products_data->products_id)
                                    ->where('language_id', '=', $language_id)
                                    ->select('products_name')->first();
                                    // dd($products_name);
                $orders_products_data->products_name = $products_name->products_name;
                $orders_products_data->categories = $categories;

                $product_attribute = DB::table('orders_products_attributes')
                    ->where([
                        ['orders_products_id', '=', $orders_products_data->orders_products_id],
                        ['orders_id', '=', $orders_products_data->orders_id],
                    ])
                    ->get();

                $orders_products_data->attribute = $product_attribute;
                // dd($orders_products_data);
                $product[$i] = $orders_products_data;
                $total_price = $total_price + $orders_products[$i]->final_price;

                $subtotal += $orders_products[$i]->final_price;

                $i++;
            }


            $orders_products_new = DB::table('orders_products')
                ->select('orders_products.*')
                ->where('orders_products.orders_id', '=', $orders_id)
                ->where('orders_products.products_id', '=', 0)
                ->get();

            foreach ($orders_products_new as $index=>$orders_products_data) {

                $orders_products_data->attribute = [];
                $product[$i] = $orders_products_data;
                $total_price = $total_price + $orders_products_new[$index]->final_price;

                $subtotal += $orders_products_new[$index]->final_price;

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

            // dd($orders_data);
        $ordersData['message'] = $message;
        $ordersData['errorMessage'] = $errorMessage;
        $ordersData['orders_data'] = $orders_data;
        $ordersData['total_price'] = $total_price;
        $ordersData['orders_status'] = $orders_status;
        $ordersData['orders_status_history'] = $orders_status_history;
        $ordersData['subtotal'] = $subtotal;

        // dd($ordersData['orders_data']);
        //get function from other controller

        // $ordersData['currency'] = $this->myVarsetting->getSetting();
        // $result['commonContent'] = $this->Setting->commonContent();

        if($orders_data[0]->invoice_type ==2 ){
            return view("admin.Orders.invoiceprint2", $title)->with('data', $ordersData);
       }
       
       return view("admin.Orders.invoiceprint", $title)->with('data', $ordersData);
    
    }

    public function cutPaper()
    {
        $connector = new FilePrintConnector("php://stdout");
        $printer = new Printer($connector);

        $printer -> text("POS!\n");
        $printer -> cut();

        $printer -> close();
        echo 'Paper printed and cut successfully';
        // return redirect()->back();
    }
    public function assignOrders(Request $request)
    {
        $orders = $this->Order->assignOrders($request);
        return redirect()->back()->with('message', Lang::get("labels.Orders successfully assigned to the delivery boy"));
    }

}
