<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Kyslik\ColumnSortable\Sortable;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;

class Reports extends Model
{
    use Sortable;
    public $sortable = ['reviews_id', 'products_id', 'customers_id', 'customers_name', 'reviews_rating', 'reviews_status', 'reviews_read', 'created_at', 'updated_at'];
    public function customersReport($request)
    {

        $language_id = '1';
        $report = DB::table('orders');

        if (isset($request->orderid)) {
            $report->where('orders_id', $request->orderid);
        }

        if (isset($request->customers_id)) {
            $report->where('customers_id', $request->customers_id);
        }

        if (isset($request->dateRange)) {
            $range = explode('-', $request->dateRange);

            $startdate = trim($range[0]);
            $enddate = trim($range[1]);

            $dateFrom = date('Y-m-d ' . '00:00:00', strtotime($startdate));
            $dateTo = date('Y-m-d ' . '23:59:59', strtotime($enddate));
            $report->whereBetween('date_purchased', [$dateFrom, $dateTo]);
        }

        //deliveryboy
        if (isset($request->deliveryboys_id)) {

            $report->join('orders_to_delivery_boy', 'orders_to_delivery_boy.orders_id', '=', 'orders.orders_id')
                ->where('orders_to_delivery_boy.deliveryboy_id', $request->deliveryboys_id)
                ->where('is_current', 1);

        }

        if (isset($request->orders_status_id)) {

            $orders_status_id = $request->orders_status_id;
            $report->LeftJoin('orders_status_history', function ($join) use ($orders_status_id) {
                $join->on('orders_status_history.orders_id', '=', 'orders.orders_id')
                    ->orderby('orders_status_history.date_added', 'DESC')->limit(1);
            });

        }

        $report->select('orders.*')->where('customers_id', '!=', '')->orderby('orders.orders_id', 'ASC')->groupby('orders.orders_id');

        if(auth()->user()->role_id != 1) {
            $report->where('orders.admin_id', '=', auth()->user()->id);
        } elseif(auth()->user()->role_id == 12) {
            $report->where('orders.admin_id', '=', auth()->user()->parent_admin_id);
        }

        if ($request->page and $request->page == 'invioce') {
            $orders = $report->get();
        } else {
            $orders = $report->paginate(50);
        }

        $total_orders_price = $report->sum('order_price');
        // dd($total_orders_price);
        $index = 0;
        $total_price = 0;

        foreach ($orders as $orders_data) {
            $orders_products = DB::table('orders_products')->where('orders_id', '=', $orders_data->orders_id)->sum('final_price');

            $orders[$index]->total_price = $orders_products;
            $total_price += $orders_products;

            $orders_status = DB::table('orders_status_history')
                ->LeftJoin('orders_status', 'orders_status.orders_status_id', '=', 'orders_status_history.orders_status_id')
                ->LeftJoin('orders_status_description', 'orders_status_description.orders_status_id', '=', 'orders_status.orders_status_id')
                ->select('orders_status_description.orders_status_name', 'orders_status_description.orders_status_id')
                ->where('orders_status_description.language_id', '=', $language_id)
                ->where('orders_id', '=', $orders_data->orders_id)
                ->where('orders_status.role_id', '<=', 2);
            if (isset($request->orders_status_id)) {
                $orders_status->where('orders_status_history.orders_status_id', $orders_status_id);
            }

            $orders_status_history = $orders_status->orderby('orders_status_history.date_added', 'DESC')->limit(1)->get();

            $current_boy = DB::table('users')
                ->leftjoin('deliveryboy_info', 'users.id', '=', 'deliveryboy_info.users_id')
                ->leftjoin('orders_to_delivery_boy', 'orders_to_delivery_boy.deliveryboy_id', '=', 'users.id')
            //->select('users.id', 'users.first_name', 'users.last_name', 'deliveryboy_info.availability_status')
                ->where('orders_to_delivery_boy.orders_id', '=', $orders_data->orders_id)
                ->where('users.role_id', 4)
                ->where('is_current', 1)
                ->first();

            if ($current_boy) {
                $orders[$index]->deliveryboy_name = $current_boy->first_name . ' ' . $current_boy->last_name;
            } else {
                $orders[$index]->deliveryboy_name = '';
            }

            $orders[$index]->orders_status_id = $orders_status_history[0]->orders_status_id;
            $orders[$index]->orders_status = $orders_status_history[0]->orders_status_name;
            $index++;

        }

        $result = array('orders' => $orders, 'total_price' => $total_orders_price);
        return $result;
    }

    public function couponReport($request)
    {
        $report = DB::table('orders');

        if (isset($request->couponcode)) {
            $report->where('coupon_code', $request->couponcode);
        }

        if (isset($request->dateRange)) {
            $range = explode('-', $request->dateRange);

            $startdate = trim($range[0]);
            $enddate = trim($range[1]);

            $dateFrom = date('Y-m-d ' . '00:00:00', strtotime($startdate));
            $dateTo = date('Y-m-d ' . '23:59:59', strtotime($enddate));
            $report->whereBetween('date_purchased', [$dateFrom, $dateTo]);
        }

        $report->select('orders.*')->where('customers_id', '!=', '')->where('coupon_code', '!=', '')->orderby('orders.orders_id', 'ASC')->groupby('orders.orders_id');

        if(auth()->user()->role_id != 1) {
            $report->where('orders.admin_id', '=', auth()->user()->id);
        } elseif(auth()->user()->role_id == 12) {
            $report->where('orders.admin_id', '=', auth()->user()->parent_admin_id);
        }

        if ($request->page and $request->page == 'invioce') {
            $orders = $report->get();
        } else {
            $orders = $report->paginate(50);
        }

        $total_orders_price = $report->sum('order_price');

        $index = 0;
        $total_price = 0;

        $result = array('orders' => $orders);
        return $result;
    }

    public function customersReportTotal($request)
    {
        $report = DB::table('orders');

        if (isset($request->orderid)) {
            $report->where('orders_id', $request->orderid);
        }

        if (isset($request->customers_id)) {
            $report->where('customers_id', $request->customers_id);
        }

        if (isset($request->dateRange)) {

            $range = explode('-', $request->dateRange);

            $startdate = trim($range[0]);
            $enddate = trim($range[1]);

            $dateFrom = date('Y-m-d ' . '00:00:00', strtotime($startdate));
            $dateTo = date('Y-m-d ' . '23:59:59', strtotime($enddate));
            $report->whereBetween('date_purchased', [$dateFrom, $dateTo]);
        }

        //deliveryboy
        if (isset($request->deliveryboys_id)) {

            $report->join('orders_to_delivery_boy', 'orders_to_delivery_boy.orders_id', '=', 'orders.orders_id')
                ->where('orders_to_delivery_boy.deliveryboy_id', $request->deliveryboys_id)
                ->where('is_current', 1);

        }

        if (isset($request->orders_status_id)) {

            $orders_status_id = $request->orders_status_id;
            $report->LeftJoin('orders_status_history', function ($join) use ($orders_status_id) {
                $join->on('orders_status_history.orders_id', '=', 'orders.orders_id')
                    ->orderby('orders_status_history.date_added', 'DESC')->limit(1);
            });

        }

        // $report->groupby('orders.orders_id');

        if(auth()->user()->role_id != 1) {
            $report->where('orders.admin_id', '=', auth()->user()->id);
        } elseif(auth()->user()->role_id == 12) {
            $report->where('orders.admin_id', '=', auth()->user()->parent_admin_id);
        }

        $prices = $report->sum('order_price');
        return ($prices);
    }

    public function allorderstatuses()
    {
        $statuses = DB::table('orders_status')
            ->LeftJoin('orders_status_description', 'orders_status_description.orders_status_id', '=', 'orders_status.orders_status_id')
            ->LeftJoin('languages', 'languages.languages_id', '=', 'orders_status_description.language_id')
            ->where('orders_status_description.language_id', '=', '1')
        // ->where('orders_status.role_id', '=', 2)
            ->orderby('role_id')
            ->get();

        return $statuses;
    }

    public function salesreport($request)
    {
        $report = DB::table('orders')
                    ->leftjoin('orders_products', 'orders_products.orders_id', '=', 'orders.orders_id')
                    // ->leftJoin('products', 'products.products_id', '=', 'orders_products.products_id')
                    ->selectRaw("payment_method,date_purchased, count('orders.orders_id') as total_orders,
                    count('orders_products.orders_id') as total_products, sum(order_price) as total_price, orders.orders_id");
                    if (isset($request->dateRange)) {
                        $range = explode('-', $request->dateRange);
                        $startdate = trim($range[0]);
                        $enddate = trim($range[1]);
                        $dateFrom = date('Y-m-d ' . '00:00:00', strtotime($startdate));
                        $dateTo = date('Y-m-d ' . '23:59:59', strtotime($enddate));
                        $report->whereBetween('date_purchased', [$dateFrom, $dateTo]);
                    }
               
        $report->whereRaw("date_purchased between (CURDATE() - INTERVAL (select count(orders_id) from orders) DAY)
            and (CURDATE() - INTERVAL 1 DAY) group by DATE(date_purchased)");
                //  dd($report)  ;  
        if(auth()->user()->role_id != 1) {
            $report->where('orders.admin_id', '=', auth()->user()->id);
        } elseif(auth()->user()->role_id == 12) {
            $report->where('orders.admin_id', '=', auth()->user()->parent_admin_id);
        }

        if ($request->page and $request->page == 'invioce') {
            $orders = $report->get();
            // $orderTotals = $orderTotal->get();
        } else {
            $orders = $report->paginate(50);
            // $orderTotals = $orderTotal->paginate(50);
        }
        // dd($orderTotals);
        $total_price_buy = 0;
        $total_price_win = 0;
        $total_price=0;
        // dd($orders)  ;
        foreach($orders as $order) {
            $price_buy = 0;
            $dateTime = date('Y-m-d', strtotime($order->date_purchased));
            $getOrdersByDatePrimary = DB::table('orders')
                ->leftjoin('orders_products', 'orders_products.orders_id', '=', 'orders.orders_id')
                ->leftJoin('products', 'products.products_id', '=', 'orders_products.products_id')
                ->whereDate('date_purchased', '=', $dateTime);
                if (isset($request->dateRange)) {
                    $range = explode('-', $request->dateRange);

                    $startdate = trim($range[0]);
                    $enddate = trim($range[1]);

                    $dateFrom = date('Y-m-d ' . '00:00:00', strtotime($startdate));
                    $dateTo = date('Y-m-d ' . '23:59:59', strtotime($enddate));
                    $getOrdersByDatePrimary->whereBetween('date_purchased', [$dateFrom, $dateTo]);
                }

                if(isset($request->admin_id)) {
                    $admin_id = $request->admin_id;
                    $getOrdersByDatePrimary->where('products.admin_id', '=', $admin_id);
                }
  
            $getOrdersByDate = $getOrdersByDatePrimary->get();
            foreach($getOrdersByDate as $getOrderByDate) {
                // dd($getOrderByDate);
                $orderProducts = DB::table('orders_products')
                    ->where('orders_id', '=', $getOrderByDate->orders_id)       
                    ->get();
                foreach($orderProducts as $orderProduct){
                    $product = DB::table('products')->where('products_id', '=', $orderProduct->products_id)->first();
                    if($product){
                        $price_buy += $product->price_buy * $orderProduct->products_quantity;
                    }
                }
            }

            $order->total_price_buy = $price_buy;
            $order->total_price_win = $order->total_price - $price_buy;
            $total_price_buy += $price_buy;
            $total_price_win += $order->total_price - $price_buy;
            $total_price+=$order->total_price;
        }

        // dd($orders);
        // $total_orders_price = DB::table('orders')
        //             ->sum('order_price');

        $result = array('orders' => $orders, 'total_price' => $total_price, 'total_price_buy' => $total_price_buy, 'total_price_win' => $total_price_win);
        // dd($result);
        return $result;
    }
    public function salesreportpos($request)
    {
        $report = DB::table('orders')
                    ->leftjoin('orders_products', 'orders_products.orders_id', '=', 'orders.orders_id')
                    // ->leftJoin('products', 'products.products_id', '=', 'orders_products.products_id')
                    ->selectRaw("payment_method,date_purchased, count('orders.orders_id') as total_orders,
                    count('orders_products.orders_id') as total_products, sum(order_price) as total_price, orders.orders_id");
          
                  
                    if (isset($request->dateRange)) {
                        $range = explode('-', $request->dateRange);
                        $startdate = trim($range[0]);
                        $enddate = trim($range[1]);
                        $dateFrom = date('Y-m-d ' . '00:00:00', strtotime($startdate));
                        $dateTo = date('Y-m-d ' . '23:59:59', strtotime($enddate));
                        $report->whereBetween('date_purchased', [$dateFrom, $dateTo]);
                    }
               
        $report->whereRaw("date_purchased between (CURDATE() - INTERVAL (select count(orders_id) from orders) DAY)
            and (CURDATE() - INTERVAL 1 DAY) group by DATE(date_purchased)");
                     $report->get()->whereIn('payment_method', ['Cash', 'Visa'])  ;  
    //    $report=    $report->where('payment_method', 'tap');
                //   dd($report)  ;  
        if(auth()->user()->role_id != 1) {
            $report->where('orders.admin_id', '=', auth()->user()->id)->get()->whereIn('payment_method', ['Cash', 'Visa']);
        } elseif(auth()->user()->role_id == 12) {
            $report->where('orders.admin_id', '=', auth()->user()->parent_admin_id)->get()->whereIn('payment_method', ['Cash', 'Visa']);
        }
        //  $report=    $report->where('payment_method', 'tap');

        if ($request->page and $request->page == 'invioce') {
            $orders = $report->get()->whereIn('payment_method', ['Cash', 'Visa']);
            // $orderTotals = $orderTotal->get();
        } else {
            $orders = $report->get()->whereIn('payment_method', ['Cash', 'Visa']);
            // $orderTotals = $orderTotal->paginate(50);
                       $totalGroup = count($orders);
    $perPage = 50;
    $page = Paginator::resolveCurrentPage('page');

    $orders = new LengthAwarePaginator($orders->forPage($page, $perPage), $totalGroup, $perPage, $page, [
        'path' => Paginator::resolveCurrentPath(),
        'pageName' => 'page',
    ]);
        }
        // dd($orderTotals);
        $total_price_buy = 0;
        $total_price_win = 0;
        $total_price=0;
        //  dd($orders)  ;
        foreach($orders as $order) {
            $price_buy = 0;
            $dateTime = date('Y-m-d', strtotime($order->date_purchased));
            $getOrdersByDatePrimary = DB::table('orders')
                ->leftjoin('orders_products', 'orders_products.orders_id', '=', 'orders.orders_id')
                ->leftJoin('products', 'products.products_id', '=', 'orders_products.products_id')
                ->whereDate('date_purchased', '=', $dateTime);
                if (isset($request->dateRange)) {
                    $range = explode('-', $request->dateRange);

                    $startdate = trim($range[0]);
                    $enddate = trim($range[1]);

                    $dateFrom = date('Y-m-d ' . '00:00:00', strtotime($startdate));
                    $dateTo = date('Y-m-d ' . '23:59:59', strtotime($enddate));
                    $getOrdersByDatePrimary->whereBetween('date_purchased', [$dateFrom, $dateTo]);
                }

                if(isset($request->admin_id)) {
                    $admin_id = $request->admin_id;
                    $getOrdersByDatePrimary->where('products.admin_id', '=', $admin_id);
                }
  
            $getOrdersByDate = $getOrdersByDatePrimary->get();
            foreach($getOrdersByDate as $getOrderByDate) {
                // dd($getOrderByDate);
                $orderProducts = DB::table('orders_products')
                    ->where('orders_id', '=', $getOrderByDate->orders_id)       
                    ->get();
                foreach($orderProducts as $orderProduct){
                    $product = DB::table('products')->where('products_id', '=', $orderProduct->products_id)->first();
                    if($product){
                        $price_buy += $product->price_buy * $orderProduct->products_quantity;
                    }
                }
            }

            $order->total_price_buy = $price_buy;
            $order->total_price_win = $order->total_price - $price_buy;
            $total_price_buy += $price_buy;
            $total_price_win += $order->total_price - $price_buy;
            $total_price+=$order->total_price;
        }

        //  dd($orders);
        // $total_orders_price = DB::table('orders')
        //             ->sum('order_price');

        $result = array('orders' => $orders, 'total_price' => $total_price, 'total_price_buy' => $total_price_buy, 'total_price_win' => $total_price_win);
        // dd($result);
        return $result;
    }
    
    public function salesreportmobile($request)
    {
        $report = DB::table('orders')
                    ->leftjoin('orders_products', 'orders_products.orders_id', '=', 'orders.orders_id')
                    // ->leftJoin('products', 'products.products_id', '=', 'orders_products.products_id')
                    ->selectRaw("payment_method,date_purchased, count('orders.orders_id') as total_orders,
                    count('orders_products.orders_id') as total_products, sum(order_price) as total_price, orders.orders_id");
          
                  
                    if (isset($request->dateRange)) {
                        $range = explode('-', $request->dateRange);
                        $startdate = trim($range[0]);
                        $enddate = trim($range[1]);
                        $dateFrom = date('Y-m-d ' . '00:00:00', strtotime($startdate));
                        $dateTo = date('Y-m-d ' . '23:59:59', strtotime($enddate));
                        $report->whereBetween('date_purchased', [$dateFrom, $dateTo]);
                    }
               
        $report->whereRaw("date_purchased between (CURDATE() - INTERVAL (select count(orders_id) from orders) DAY)
            and (CURDATE() - INTERVAL 1 DAY) group by DATE(date_purchased)");
                     $report->get()->whereNotIn('payment_method', ['Cash', 'Visa'])  ;  
    //    $report=    $report->where('payment_method', 'tap');
                //   dd($report)  ;  
        if(auth()->user()->role_id != 1) {
            $report->where('orders.admin_id', '=', auth()->user()->id)->get()->whereNotIn('payment_method', ['Cash', 'Visa']);
        } elseif(auth()->user()->role_id == 12) {
            $report->where('orders.admin_id', '=', auth()->user()->parent_admin_id)->get()->whereNotIn('payment_method', ['Cash', 'Visa']);
        }
        //  $report=    $report->where('payment_method', 'tap');

        if ($request->page and $request->page == 'invioce') {
            $orders = $report->get()->whereNotIn('payment_method', ['Cash', 'Visa']);
            // $orderTotals = $orderTotal->get();
        } else {
             $orders = $report->get()->whereNotIn('payment_method', ['Cash', 'Visa']);
            // $orders = $report->whereNotIn('payment_method', ['Cash', 'Visa'])->paginate(50);
            // $orders = $report->paginate(50);
            // $orderTotals = $orderTotal->paginate(50);
             $totalGroup = count($orders);
    $perPage = 50;
    $page = Paginator::resolveCurrentPage('page');

    $orders = new LengthAwarePaginator($orders->forPage($page, $perPage), $totalGroup, $perPage, $page, [
        'path' => Paginator::resolveCurrentPath(),
        'pageName' => 'page',
    ]);
        }
        // dd($orderTotals);
        $total_price_buy = 0;
        $total_price_win = 0;
        $total_price=0;
        //   dd($orders)  ;
        foreach($orders as $order) {
            $price_buy = 0;
            $dateTime = date('Y-m-d', strtotime($order->date_purchased));
            $getOrdersByDatePrimary = DB::table('orders')
                ->leftjoin('orders_products', 'orders_products.orders_id', '=', 'orders.orders_id')
                ->leftJoin('products', 'products.products_id', '=', 'orders_products.products_id')
                ->whereDate('date_purchased', '=', $dateTime);
                if (isset($request->dateRange)) {
                    $range = explode('-', $request->dateRange);

                    $startdate = trim($range[0]);
                    $enddate = trim($range[1]);

                    $dateFrom = date('Y-m-d ' . '00:00:00', strtotime($startdate));
                    $dateTo = date('Y-m-d ' . '23:59:59', strtotime($enddate));
                    $getOrdersByDatePrimary->whereBetween('date_purchased', [$dateFrom, $dateTo]);
                }

                if(isset($request->admin_id)) {
                    $admin_id = $request->admin_id;
                    $getOrdersByDatePrimary->where('products.admin_id', '=', $admin_id);
                }
  
            $getOrdersByDate = $getOrdersByDatePrimary->get();
            foreach($getOrdersByDate as $getOrderByDate) {
                // dd($getOrderByDate);
                $orderProducts = DB::table('orders_products')
                    ->where('orders_id', '=', $getOrderByDate->orders_id)       
                    ->get();
                foreach($orderProducts as $orderProduct){
                    $product = DB::table('products')->where('products_id', '=', $orderProduct->products_id)->first();
                    if($product){
                        $price_buy += $product->price_buy * $orderProduct->products_quantity;
                    }
                }
            }

            $order->total_price_buy = $price_buy;
            $order->total_price_win = $order->total_price - $price_buy;
            $total_price_buy += $price_buy;
            $total_price_win += $order->total_price - $price_buy;
            $total_price+=$order->total_price;
        }

        //  dd($orders);
        // $total_orders_price = DB::table('orders')
        //             ->sum('order_price');

        $result = array('orders' => $orders, 'total_price' => $total_price, 'total_price_buy' => $total_price_buy, 'total_price_win' => $total_price_win);
        // dd($result);
        return $result;
    }

    public function shopsalesreport($request)
    {
        $report = DB::table('orders')
                    // ->leftjoin('orders_products', 'orders_products.orders_id', '=', 'orders.orders_id')
                    // ->leftJoin('products', 'products.products_id', '=', 'orders_products.products_id')
                    ->selectRaw("date_purchased, count('orders.orders_id') as total_orders, sum(order_price) as total_price, orders.orders_id");
                    // dd($report->get());
                    if (isset($request->dateRange)) {
                        $range = explode('-', $request->dateRange);

                        $startdate = trim($range[0]);
                        $enddate = trim($range[1]);

                        $dateFrom = date('Y-m-d ' . '00:00:00', strtotime($startdate));
                        $dateTo = date('Y-m-d ' . '23:59:59', strtotime($enddate));
                        $report->whereBetween('date_purchased', [$dateFrom, $dateTo]);
                    }
                    if(isset($request->admin_id)) {
                        $admin_id = $request->admin_id;
                        $report->where('orders.admin_id', '=', $admin_id);
                    }

// dd($report->get());

        // $report->whereRaw("date_purchased between (CURDATE() - INTERVAL (select count(orders_id) from orders) DAY)
        //     and (CURDATE() - INTERVAL 1 DAY) group by DATE(date_purchased)");


        if ($request->page and $request->page == 'invioce') {
            $orders = $report->get();
        } else {
            $orders = $report->paginate(50);
        }
        $total_price_buy = 0;
        $total_price_win = 0;
        // dd($orders);
        foreach($orders as $order) {
            $price_buy = 0;
            $dateTime = date('Y-m-d', strtotime($order->date_purchased));

            $getOrdersByDatePrimary = DB::table('orders')
                ->leftjoin('orders_products', 'orders_products.orders_id', '=', 'orders.orders_id')
                ->leftJoin('products', 'products.products_id', '=', 'orders_products.products_id')
                ->whereDate('date_purchased', '=', $dateTime);
                if (isset($request->dateRange)) {
                    $range = explode('-', $request->dateRange);

                    $startdate = trim($range[0]);
                    $enddate = trim($range[1]);

                    $dateFrom = date('Y-m-d ' . '00:00:00', strtotime($startdate));
                    $dateTo = date('Y-m-d ' . '23:59:59', strtotime($enddate));
                    // dd($dateTo);
                    $getOrdersByDatePrimary->whereBetween('date_purchased', [$dateFrom, $dateTo]);
                }

                if(isset($request->admin_id)) {
                    $admin_id = $request->admin_id;
                    $getOrdersByDatePrimary->where('orders.admin_id', '=', $admin_id);
                }

                // ->where(DB::raw("DATE(date_purchased) = '".$dateTime."'"))
            $getOrdersByDate = $getOrdersByDatePrimary->get();
            foreach($getOrdersByDate as $getOrderByDate) {
                $orderProducts = DB::table('orders_products')
                    ->where('orders_id', '=', $getOrderByDate->orders_id)
                    // ->whereDate('date_purchased', $order->date_purchased)
                    ->get();
                foreach($orderProducts as $orderProduct){
                    $product = DB::table('products')->where('products_id', '=', $orderProduct->products_id)->first();
                    if($product){
                        $price_buy += $product->price_buy;
                    }
                }
            }

            $order->total_price_buy = $price_buy;
            $order->total_price_win = $order->total_price - $price_buy;
            $total_price_buy += $price_buy;
            $total_price_win += $order->total_price - $price_buy;
        }

        $total_orders_price = DB::table('orders')
                    ->sum('order_price');
        $result = array('orders' => $orders, 'total_price' => $total_orders_price, 'total_price_buy' => $total_price_buy, 'total_price_win' => $total_price_win);
        return $result;
    }

    public function shopemployereport($request)
    {
        $report = DB::table('orders')
                    ->leftjoin('orders_products', 'orders_products.orders_id', '=', 'orders.orders_id')
                    ->leftJoin('products', 'products.products_id', '=', 'orders_products.products_id')
                    ->selectRaw("date_purchased, count('orders.orders_id') as total_orders,
                        count('orders_products.orders_id') as total_products, sum(order_price) as total_price, orders.orders_id")
                    ->where('orders.customers_id', '=', 0);

                    if (isset($request->dateRange)) {
                        $range = explode('-', $request->dateRange);

                        $startdate = trim($range[0]);
                        $enddate = trim($range[1]);

                        $dateFrom = date('Y-m-d ' . '00:00:00', strtotime($startdate));
                        $dateTo = date('Y-m-d ' . '23:59:59', strtotime($enddate));
                        $report->whereBetween('date_purchased', [$dateFrom, $dateTo]);
                    }

                    if(isset($request->admin_id)) {
                        $employe_id = $request->employe_id;
                        $report->where('orders.admin_id', '=', $employe_id);
                    }

        $report->whereRaw("date_purchased between (CURDATE() - INTERVAL (select count(orders_id) from orders) DAY)
            and (CURDATE() - INTERVAL 1 DAY) group by DATE(date_purchased)");

        if ($request->page and $request->page == 'invioce') {
            $orders = $report->get();
        } else {
            $orders = $report->paginate(50);
        }
        $total_price_buy = 0;
        $total_price_win = 0;
        $total_price=0;
        foreach($orders as $order) {
            $price_buy = 0;
            $dateTime = date('Y-m-d', strtotime($order->date_purchased));

            $getOrdersByDatePrimary = DB::table('orders')
                ->leftjoin('orders_products', 'orders_products.orders_id', '=', 'orders.orders_id')
                ->leftJoin('products', 'products.products_id', '=', 'orders_products.products_id')
                ->whereDate('date_purchased', '=', $dateTime)
                ->where('orders.customers_id', '=', 0);
                if (isset($request->dateRange)) {
                    $range = explode('-', $request->dateRange);

                    $startdate = trim($range[0]);
                    $enddate = trim($range[1]);

                    $dateFrom = date('Y-m-d ' . '00:00:00', strtotime($startdate));
                    $dateTo = date('Y-m-d ' . '23:59:59', strtotime($enddate));
                    $getOrdersByDatePrimary->whereBetween('date_purchased', [$dateFrom, $dateTo]);
                }

                if(isset($request->admin_id)) {
                    $employe_id = $request->employe_id;
                    $getOrdersByDatePrimary->where('orders.admin_id', '=', $employe_id);
                }
                // ->where(DB::raw("DATE(date_purchased) = '".$dateTime."'"))
            $getOrdersByDate = $getOrdersByDatePrimary->get();
            // dd($getOrdersByDate);
            foreach($getOrdersByDate as $getOrderByDate) {
                $orderProducts = DB::table('orders_products')
                    ->where('orders_id', '=', $getOrderByDate->orders_id)
                    // ->whereDate('date_purchased', $order->date_purchased)
                    ->get();
                foreach($orderProducts as $orderProduct){
                    $product = DB::table('products')->where('products_id', '=', $orderProduct->products_id)->first();
                    if($product){
                        $price_buy += $product->price_buy;
                    }
                }
            }

            $order->total_price_buy = $price_buy;
            $order->total_price_win = $order->total_price - $price_buy;
            $total_price_buy += $price_buy;
            $total_price_win += $order->total_price - $price_buy;
            $total_price+=$order->total_price;
        }

        // $total_orders_price = DB::table('orders')
        //             ->sum('order_price');

        $result = array('orders' => $orders, 'total_price' => $total_price, 'total_price_buy' => $total_price_buy, 'total_price_win' => $total_price_win);
        return $result;
    }

    public function inventoryreport($request)
    {
        $report = DB::table('inventory')
            ->leftjoin('manage_min_max', 'manage_min_max.products_id', '=', 'manage_min_max.products_id')
            ->select('inventory.*', 'manage_min_max.min_level', 'manage_min_max.max_level');

        if (isset($request->products_id)) {
            $report->where('inventory.products_id', $request->products_id);

            if (isset($request->dateRange)) {
                $range = explode('-', $request->dateRange);

                $startdate = trim($range[0]);
                $enddate = trim($range[1]);

                $dateFrom = date('Y-m-d ' . '00:00:00', strtotime($startdate));
                $dateTo = date('Y-m-d ' . '23:59:59', strtotime($enddate));
                $report->whereBetween('created_at', [$dateFrom, $dateTo]);
            }

        } else {
            $report->where('inventory.inventory_ref_id', '');
        }

        if ($request->page and $request->page == 'invioce') {
            $reports = $report->get();
        } else {
            $reports = $report->paginate(50);
        }

        $index = 0;
        foreach ($reports as $data) {

            //current stock
            $prev_stock_in = DB::table('inventory')
                ->where('inventory_ref_id', '<=', $data->inventory_ref_id)
                ->where('stock_type', 'in')
                ->where('inventory.products_id', $request->products_id)
                ->sum('stock');

            $prev_stock_out = DB::table('inventory')
                ->where('inventory_ref_id', '<=', $data->inventory_ref_id)
                ->where('stock_type', 'out')
                ->where('inventory.products_id', $request->products_id)
                ->sum('stock');

            if ($prev_stock_out > 0) {
                $reports[$index]->current_stock = abs($prev_stock_in - $prev_stock_out);
            } else {
                $reports[$index]->current_stock = $prev_stock_in;
            }
            $index++;

        }

        return $reports;

    }

    public function suppliersmainreport($request)
    {
        $report = DB::table('supplier_main')
            ->leftjoin('suppliers', 'suppliers.supplier_main_id', '=', 'supplier_main.supplier_main_id')
            ->leftjoin('inventory', 'inventory.inventory_ref_id', '=', 'suppliers.inventory_id')
            ->leftjoin('user_supplier', 'user_supplier.id', '=', 'supplier_main.user_supplier_id')
            ->leftjoin('supplier_detail', 'supplier_detail.supplier_main_id', '=', 'supplier_main.supplier_main_id')
            ->select('supplier_main.*', 'supplier_detail.price as paiedPrice', 'supplier_detail.date_added as dateAdded', 'supplier_main.user_supplier_id', 'user_supplier.name as supplier_name', 'inventory.reference_code')
            ->groupBy('supplier_main.supplier_main_id');

            // dd($report);
        if(auth()->user()->role_id != 1) {
            $report->where('suppliers.admin_id', '=', auth()->user()->id);
        } elseif(auth()->user()->role_id == 12) {
            $report->where('suppliers.admin_id', '=', auth()->user()->parent_admin_id);
        }

        if (isset($request->user_supplier_id)) {
            $report->where('supplier_main.user_supplier_id', $request->user_supplier_id);

            if (isset($request->dateRange)) {
                $range = explode('-', $request->dateRange);

                $startdate = trim($range[0]);
                $enddate = trim($range[1]);

                $dateFrom = date('Y-m-d ' . '00:00:00', strtotime($startdate));
                $dateTo = date('Y-m-d ' . '23:59:59', strtotime($enddate));
                $report->whereBetween('supplier_main.created_at', [$dateFrom, $dateTo]);
            }

        }

        if ($request->page and $request->page == 'invioce') {
            $reports = $report->get();
        } else {
            $reports = $report->paginate(50);
        }

        // dd($report);
        return $reports;
    }

    public function suppliersreport($request, $id)
    {
        $report = DB::table('suppliers')
            ->leftjoin('inventory', 'inventory.inventory_ref_id', '=', 'suppliers.inventory_id')
            ->leftjoin('products_description', 'products_description.products_id', '=', 'inventory.products_id')
            ->where('language_id', 1)
            ->select('suppliers.*', 'inventory.products_id', 'products_description.products_name')
            ->where('suppliers.supplier_main_id', $id);

        if (isset($request->user_supplier_id)) {
            // $report->where('suppliers.user_supplier_id', $request->user_supplier_id);

            if (isset($request->dateRange)) {
                $range = explode('-', $request->dateRange);

                $startdate = trim($range[0]);
                $enddate = trim($range[1]);

                $dateFrom = date('Y-m-d ' . '00:00:00', strtotime($startdate));
                $dateTo = date('Y-m-d ' . '23:59:59', strtotime($enddate));
                $report->whereBetween('suppliers.created_at', [$dateFrom, $dateTo]);
            }

        }

        if ($request->page and $request->page == 'invioce') {
            $reports = $report->get();
        } else {
            $reports = $report->paginate(50);
        }

        return $reports;
    }

    public function suppliersreportTotalPrice($request, $id)
    {
        $report = DB::table('suppliers')
            ->leftjoin('inventory', 'inventory.inventory_ref_id', '=', 'suppliers.inventory_id')
            ->leftjoin('products_description', 'products_description.products_id', '=', 'inventory.products_id')
            ->where('language_id', 1)
            ->select('suppliers.*', 'inventory.products_id', 'products_description.products_name')
            ->where('suppliers.supplier_main_id', $id);

        if (isset($request->user_supplier_id)) {
            // $report->where('suppliers.user_supplier_id', $request->user_supplier_id);

            if (isset($request->dateRange)) {
                $range = explode('-', $request->dateRange);

                $startdate = trim($range[0]);
                $enddate = trim($range[1]);

                $dateFrom = date('Y-m-d ' . '00:00:00', strtotime($startdate));
                $dateTo = date('Y-m-d ' . '23:59:59', strtotime($enddate));
                $report->whereBetween('suppliers.created_at', [$dateFrom, $dateTo]);
            }

        }

        $reports = $report->sum('suppliers.price');

        return $reports;
    }

    public function suppliersreportDetail($request, $id)
    {
        $report = DB::table('supplier_detail')
            ->select('supplier_detail.*')
            ->where('supplier_detail.supplier_main_id', $id);

        if (isset($request->user_supplier_id)) {

            if (isset($request->dateRange)) {
                $range = explode('-', $request->dateRange);

                $startdate = trim($range[0]);
                $enddate = trim($range[1]);

                $dateFrom = date('Y-m-d ' . '00:00:00', strtotime($startdate));
                $dateTo = date('Y-m-d ' . '23:59:59', strtotime($enddate));
                $report->whereBetween('supplier_detail.created_at', [$dateFrom, $dateTo]);
            }

        }

        if ($request->page and $request->page == 'invioce') {
            $reports = $report->get();
        } else {
            $reports = $report->paginate(50);
        }

        return $reports;
    }

    public function suppliersreportDetailTotal($request, $id)
    {
        $report = DB::table('supplier_detail')
            ->select('supplier_detail.*')
            ->where('supplier_detail.supplier_main_id', $id);

        if (isset($request->user_supplier_id)) {

            if (isset($request->dateRange)) {
                $range = explode('-', $request->dateRange);

                $startdate = trim($range[0]);
                $enddate = trim($range[1]);

                $dateFrom = date('Y-m-d ' . '00:00:00', strtotime($startdate));
                $dateTo = date('Y-m-d ' . '23:59:59', strtotime($enddate));
                $report->whereBetween('supplier_detail.created_at', [$dateFrom, $dateTo]);
            }

        }

        $reports = $report->sum('supplier_detail.price');


        return $reports;
    }

    public function minstock($request)
    {
        $report = DB::table('inventory')
            ->leftjoin('manage_min_max', 'manage_min_max.products_id', '=', 'inventory.products_id')
            ->leftjoin('products_description', 'products_description.products_id', '=', 'inventory.products_id')
            ->select('inventory.products_id', 'products_description.products_name', 'manage_min_max.min_level', DB::raw("( SUM(IF(stock_type = 'in', stock, 0)) -
                    SUM(IF(stock_type = 'out', stock, 0)) ) as 'stocks'", DB::raw("having ( SUM(IF(stock_type = 'in', stock, 0)) -
                    SUM(IF(stock_type = 'out', stock, 0)) )")))
            ->where('manage_min_max.min_level', '>', '0')
            ->where('language_id', 1)
            ->groupby('inventory.products_id');
        //->having(DB::raw("SUM(IF(stock_type = 'in', stock, 0)) - SUM(IF(stock_type = 'out', stock, 0))"))

        if ($request->page and $request->page == 'invioce') {
            $orders = $report->get();
        } else {
            $orders = $report->paginate(50);
        }

        return $orders;

    }

    public function maxstock($request)
    {
        $report = DB::table('inventory')
            ->leftjoin('manage_min_max', 'manage_min_max.products_id', '=', 'inventory.products_id')
            ->leftjoin('products_description', 'products_description.products_id', '=', 'inventory.products_id')
            ->select('inventory.products_id', 'products_description.products_name', 'manage_min_max.max_level', DB::raw("( SUM(IF(stock_type = 'in', stock, 0)) -
                    SUM(IF(stock_type = 'out', stock, 0)) ) as 'stocks'", DB::raw("having ( SUM(IF(stock_type = 'in', stock, 0)) -
                    SUM(IF(stock_type = 'out', stock, 0)) )")))
            ->where('manage_min_max.max_level', '>', '0')
            ->where('language_id', 1)
            ->groupby('inventory.products_id');
            // ->having(DB::raw("(SUM(IF(stock_type = 'in', stock, 0)) - SUM(IF(stock_type = 'out', stock, 0))) >= 123"))
        if ($request->page and $request->page == 'invioce') {
            $orders = $report->get();
        } else {
            $orders = $report->paginate(50);
        }

        return $orders;

    }

    public function outofstock($request)
    {
        $report = DB::table('inventory')
                    ->leftjoin('products_description', 'products_description.products_id' ,'=' ,'inventory.products_id')
                    ->leftjoin('products', 'products.products_id' ,'=' ,'inventory.products_id')
                    ->select('products_description.products_id', 'products_description.products_name')
                    ->where('products_description.language_id', 1)
                    ->groupby('inventory.products_id')
                    ->havingRaw("SUM(IF(stock_type = 'in', stock, 0)) - SUM(IF(stock_type = 'out', stock, 0)) = 0");

        if(auth()->user()->role_id != 1) {
            $report->where('products.admin_id', '=', auth()->user()->id);
        } 
        if ($request->page and $request->page == 'invioce') {
            $orders = $report->get();
        } else {
            $orders = $report->paginate(50);
        }

        return $orders;

    }

}
