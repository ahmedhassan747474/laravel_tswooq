<?php
namespace App\Http\Controllers\Web;

use App\Group;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\AppModels\Product;
use App\Models\Core\User;
use App\Models\Web\Currency;
use App\Models\Web\Index;
use App\Models\Web\Languages;
use App\Models\Web\News;
use App\Models\Web\Order;
use App\Models\Web\Products;
use App\Models\Web\package;
use Lang;
use View;
use DB;
use Auth;
use Carbon;
class shopController extends Controller
{
    public function __construct(
        Index $index,
        News $news,
        Languages $languages,
        Products $products,
        Currency $currency,
        Order $order
    ) {
        $this->index = $index;
        $this->order = $order;
        $this->news = $news;
        $this->languages = $languages;
        $this->products = $products;
        $this->currencies = $currency;
        $this->theme = new ThemeController();
    }
	public Function getShops(){
        $title = array('pageTitle' => Lang::get("website.Home"));
        $final_theme = $this->theme->theme();
/*********************************************************************/
/**                   GENERAL CONTENT TO DISPLAY                    **/
/*********************************************************************/
        $result = array();
        $result['commonContent'] = $this->index->commonContent();
        $title = array('pageTitle' => Lang::get("website.Home"));
/********************************************************************/

/*********************************************************************/
/**                   GENERAL SETTINGS TO FETCH PRODUCTS           **/
/*******************************************************************/

        /**  SET LIMIT OF PRODUCTS  **/
        if (!empty($request->limit)) {
            $limit = $request->limit;
        } else {
            $limit = 12;
        }

        /**  MINIMUM PRICE **/
        if (!empty($request->min_price)) {
            $min_price = $request->min_price;
        } else {
            $min_price = '';
        }

        /**  MAXIMUM PRICE  **/
        if (!empty($request->max_price)) {
            $max_price = $request->max_price;
        } else {
            $max_price = '';
        }
/*************************************************************************/
        /*********************************************************************/
        /**                     FETCH NEWEST PRODUCTS                       **/
        /*********************************************************************/

        $data = array('page_number' => '0', 'type' => '', 'limit' => 10, 'min_price' => $min_price, 'max_price' => $max_price);
        $newest_products = $this->products->products($data);
        $result['products'] = $newest_products;
        /*********************************************************************/
        /**                     Compare Counts                              **/
        /*********************************************************************/

/*********************************************************************/

        /***************************************************************/
        /**   CART ARRAY RECORDS TO CHECK WETHER OR NOT DISPLAYED--   **/
        /**  --PRODUCT HAS BEEN ALREADY ADDE TO CART OR NOT           **/
/***************************************************************/
        $cart = '';
        $result['cartArray'] = $this->products->cartIdArray($cart);
/**************************************************************/

        //special products
        $data = array('page_number' => '0', 'type' => 'special', 'limit' => $limit, 'min_price' => $min_price, 'max_price' => $max_price);
        $special_products = $this->products->products($data);
        $result['special'] = $special_products;
        //Flash sale

        $data = array('page_number' => '0', 'type' => 'flashsale', 'limit' => $limit, 'min_price' => $min_price, 'max_price' => $max_price);
        $flash_sale = $this->products->products($data);
        $result['flash_sale'] = $flash_sale;
        // //top seller
        $data = array('page_number' => '0', 'type' => 'topseller', 'limit' => $limit, 'min_price' => $min_price, 'max_price' => $max_price);
        $top_seller = $this->products->products($data);
        $result['top_seller'] = $top_seller;

//most liked
        $data = array('page_number' => '0', 'type' => 'mostliked', 'limit' => $limit, 'min_price' => $min_price, 'max_price' => $max_price);
        $most_liked = $this->products->products($data);
        $result['most_liked'] = $most_liked;

//is feature
        $data = array('page_number' => '0', 'type' => 'is_feature', 'limit' => $limit, 'min_price' => $min_price, 'max_price' => $max_price);
        $featured = $this->products->products($data);
        $result['featured'] = $featured;

        $data = array('page_number' => '0', 'type' => '', 'limit' => '15', 'is_feature' => 1);
        $news = $this->news->getAllNews($data);
        $result['news'] = $news;
//current time

        $currentDate = Carbon\Carbon::now();
        $currentDate = $currentDate->toDateTimeString();

        $slides = $this->index->slides($currentDate);
        $result['slides'] = $slides;
        //liked products
        $result['liked_products'] = $this->products->likedProducts();

        $orders = $this->order->getOrders();
        if (count($orders) > 0) {
            $allOrders = $orders;
        } else {
            $allOrders = $this->order->allOrders();
        }

        $temp_i = array();
        foreach ($allOrders as $orders_data) {
            $mostOrdered = $this->order->mostOrders($orders_data);
            foreach ($mostOrdered as $mostOrderedData) {
                $temp_i[] = $mostOrderedData->products_id;
            }
        }
        $detail = array();
        $temp_i = array_unique($temp_i);
        foreach ($temp_i as $temp_data) {
            $data = array('page_number' => '0', 'type' => 'topseller', 'products_id' => $temp_data, 'limit' => 7, 'min_price' => '', 'max_price' => '');
            $single_product = $this->products->products($data);
            if (!empty($single_product['product_data'][0])) {
                $detail[] = $single_product['product_data'][0];
            }
        }

        $result['weeklySoldProducts'] = array('success' => '1', 'product_data' => $detail, 'message' => "Returned all products.", 'total_record' => count($detail));
        
        session(['paymentResponseData' => '']); 
            
        session(['paymentResponse'=>'']);
        session(['payment_json','']);
        
        $category_section = array();
       if(!empty($result['commonContent']['settings']['home_category'])){
            $categories_array = explode(',',$result['commonContent']['settings']['home_category']);
            $index = 0;
            foreach($categories_array as $item){
                
                //get category section detail
                $category = $this->products->getCategorybyId($item);
                if($category){
                    $category_section[$index] = $category;
                    $data = array('page_number' => '0', 'categories_id'=>$category->categories_id, 'type' => 'topseller', 'limit' => $limit, 'min_price' => $min_price, 'max_price' => $max_price);
                    $single_product = $this->products->products($data);
                    $category_section[$index]->products = $single_product;
                    $index++;
                }
            }
            
        }
        $result['category_section'] = $category_section;
		    $users = User::with('products')->where('shop_name','!=',null)->get();
        	return view('web.getShops',['title' => $title, 'final_theme' => $final_theme])->with(['result' => $result,'users'=> $users]);
	}


	/// get 

	public Function Become_merchant_with_us(){
        $title = array('pageTitle' => Lang::get("website.Home"));
        $final_theme = $this->theme->theme();
/*********************************************************************/
/**                   GENERAL CONTENT TO DISPLAY                    **/
/*********************************************************************/
        $result = array();
        $result['commonContent'] = $this->index->commonContent();
        $title = array('pageTitle' => Lang::get("website.Home"));
/********************************************************************/

/*********************************************************************/
/**                   GENERAL SETTINGS TO FETCH PRODUCTS           **/
/*******************************************************************/

        /**  SET LIMIT OF PRODUCTS  **/
        if (!empty($request->limit)) {
            $limit = $request->limit;
        } else {
            $limit = 12;
        }

        /**  MINIMUM PRICE **/
        if (!empty($request->min_price)) {
            $min_price = $request->min_price;
        } else {
            $min_price = '';
        }

        /**  MAXIMUM PRICE  **/
        if (!empty($request->max_price)) {
            $max_price = $request->max_price;
        } else {
            $max_price = '';
        }
/*************************************************************************/
        /*********************************************************************/
        /**                     FETCH NEWEST PRODUCTS                       **/
        /*********************************************************************/

        $data = array('page_number' => '0', 'type' => '', 'limit' => 10, 'min_price' => $min_price, 'max_price' => $max_price);
        $newest_products = $this->products->products($data);
        $result['products'] = $newest_products;
        /*********************************************************************/
        /**                     Compare Counts                              **/
        /*********************************************************************/

/*********************************************************************/

        /***************************************************************/
        /**   CART ARRAY RECORDS TO CHECK WETHER OR NOT DISPLAYED--   **/
        /**  --PRODUCT HAS BEEN ALREADY ADDE TO CART OR NOT           **/
/***************************************************************/
        $cart = '';
        $result['cartArray'] = $this->products->cartIdArray($cart);
/**************************************************************/

        //special products
        $data = array('page_number' => '0', 'type' => 'special', 'limit' => $limit, 'min_price' => $min_price, 'max_price' => $max_price);
        $special_products = $this->products->products($data);
        $result['special'] = $special_products;
        //Flash sale

        $data = array('page_number' => '0', 'type' => 'flashsale', 'limit' => $limit, 'min_price' => $min_price, 'max_price' => $max_price);
        $flash_sale = $this->products->products($data);
        $result['flash_sale'] = $flash_sale;
        // //top seller
        $data = array('page_number' => '0', 'type' => 'topseller', 'limit' => $limit, 'min_price' => $min_price, 'max_price' => $max_price);
        $top_seller = $this->products->products($data);
        $result['top_seller'] = $top_seller;

//most liked
        $data = array('page_number' => '0', 'type' => 'mostliked', 'limit' => $limit, 'min_price' => $min_price, 'max_price' => $max_price);
        $most_liked = $this->products->products($data);
        $result['most_liked'] = $most_liked;

//is feature
        $data = array('page_number' => '0', 'type' => 'is_feature', 'limit' => $limit, 'min_price' => $min_price, 'max_price' => $max_price);
        $featured = $this->products->products($data);
        $result['featured'] = $featured;

        $data = array('page_number' => '0', 'type' => '', 'limit' => '15', 'is_feature' => 1);
        $news = $this->news->getAllNews($data);
        $result['news'] = $news;
//current time

        $currentDate = Carbon\Carbon::now();
        $currentDate = $currentDate->toDateTimeString();

        $slides = $this->index->slides($currentDate);
        $result['slides'] = $slides;
        //liked products
        $result['liked_products'] = $this->products->likedProducts();

        $orders = $this->order->getOrders();
        if (count($orders) > 0) {
            $allOrders = $orders;
        } else {
            $allOrders = $this->order->allOrders();
        }

        $temp_i = array();
        foreach ($allOrders as $orders_data) {
            $mostOrdered = $this->order->mostOrders($orders_data);
            foreach ($mostOrdered as $mostOrderedData) {
                $temp_i[] = $mostOrderedData->products_id;
            }
        }
        $detail = array();
        $temp_i = array_unique($temp_i);
        foreach ($temp_i as $temp_data) {
            $data = array('page_number' => '0', 'type' => 'topseller', 'products_id' => $temp_data, 'limit' => 7, 'min_price' => '', 'max_price' => '');
            $single_product = $this->products->products($data);
            if (!empty($single_product['product_data'][0])) {
                $detail[] = $single_product['product_data'][0];
            }
        }

        $result['weeklySoldProducts'] = array('success' => '1', 'product_data' => $detail, 'message' => "Returned all products.", 'total_record' => count($detail));
        
        session(['paymentResponseData' => '']); 
            
        session(['paymentResponse'=>'']);
        session(['payment_json','']);
        
        $category_section = array();
       if(!empty($result['commonContent']['settings']['home_category'])){
            $categories_array = explode(',',$result['commonContent']['settings']['home_category']);
            $index = 0;
            foreach($categories_array as $item){
                
                //get category section detail
                $category = $this->products->getCategorybyId($item);
                if($category){
                    $category_section[$index] = $category;
                    $data = array('page_number' => '0', 'categories_id'=>$category->categories_id, 'type' => 'topseller', 'limit' => $limit, 'min_price' => $min_price, 'max_price' => $max_price);
                    $single_product = $this->products->products($data);
                    $category_section[$index]->products = $single_product;
                    $index++;
                }
            }
            
        }
        // dd($category_section);
        $result['category_section'] = $category_section;
		$users = User::with('products')->where('shop_name','!=',null)->get();
        $packges = package::get();
        return view('web.Become_merchant_with_us',['title' => $title, 'final_theme' => $final_theme])->with(['result' => $result,'users'=> $users,'packges' => $packges]);
	}

    public function checkout($id,$month){
        $title = array('pageTitle' => Lang::get("website.Home"));
        $final_theme = $this->theme->theme();
/*********************************************************************/
/**                   GENERAL CONTENT TO DISPLAY                    **/
/*********************************************************************/
        $result = array();
        $result['commonContent'] = $this->index->commonContent();
        $title = array('pageTitle' => Lang::get("website.Home"));
/********************************************************************/

/*********************************************************************/
/**                   GENERAL SETTINGS TO FETCH PRODUCTS           **/
/*******************************************************************/

        /**  SET LIMIT OF PRODUCTS  **/
        if (!empty($request->limit)) {
            $limit = $request->limit;
        } else {
            $limit = 12;
        }

        /**  MINIMUM PRICE **/
        if (!empty($request->min_price)) {
            $min_price = $request->min_price;
        } else {
            $min_price = '';
        }

        /**  MAXIMUM PRICE  **/
        if (!empty($request->max_price)) {
            $max_price = $request->max_price;
        } else {
            $max_price = '';
        }
/*************************************************************************/
        /*********************************************************************/
        /**                     FETCH NEWEST PRODUCTS                       **/
        /*********************************************************************/

        $data = array('page_number' => '0', 'type' => '', 'limit' => 10, 'min_price' => $min_price, 'max_price' => $max_price);
        $newest_products = $this->products->products($data);
        $result['products'] = $newest_products;
        /*********************************************************************/
        /**                     Compare Counts                              **/
        /*********************************************************************/

/*********************************************************************/

        /***************************************************************/
        /**   CART ARRAY RECORDS TO CHECK WETHER OR NOT DISPLAYED--   **/
        /**  --PRODUCT HAS BEEN ALREADY ADDE TO CART OR NOT           **/
/***************************************************************/
        $cart = '';
        $result['cartArray'] = $this->products->cartIdArray($cart);
/**************************************************************/

        //special products
        $data = array('page_number' => '0', 'type' => 'special', 'limit' => $limit, 'min_price' => $min_price, 'max_price' => $max_price);
        $special_products = $this->products->products($data);
        $result['special'] = $special_products;
        //Flash sale

        $data = array('page_number' => '0', 'type' => 'flashsale', 'limit' => $limit, 'min_price' => $min_price, 'max_price' => $max_price);
        $flash_sale = $this->products->products($data);
        $result['flash_sale'] = $flash_sale;
        // //top seller
        $data = array('page_number' => '0', 'type' => 'topseller', 'limit' => $limit, 'min_price' => $min_price, 'max_price' => $max_price);
        $top_seller = $this->products->products($data);
        $result['top_seller'] = $top_seller;

//most liked
        $data = array('page_number' => '0', 'type' => 'mostliked', 'limit' => $limit, 'min_price' => $min_price, 'max_price' => $max_price);
        $most_liked = $this->products->products($data);
        $result['most_liked'] = $most_liked;

//is feature
        $data = array('page_number' => '0', 'type' => 'is_feature', 'limit' => $limit, 'min_price' => $min_price, 'max_price' => $max_price);
        $featured = $this->products->products($data);
        $result['featured'] = $featured;

        $data = array('page_number' => '0', 'type' => '', 'limit' => '15', 'is_feature' => 1);
        $news = $this->news->getAllNews($data);
        $result['news'] = $news;
//current time

        $currentDate = Carbon\Carbon::now();
        $currentDate = $currentDate->toDateTimeString();

        $slides = $this->index->slides($currentDate);
        $result['slides'] = $slides;
        //liked products
        $result['liked_products'] = $this->products->likedProducts();

        $orders = $this->order->getOrders();
        if (count($orders) > 0) {
            $allOrders = $orders;
        } else {
            $allOrders = $this->order->allOrders();
        }

        $temp_i = array();
        foreach ($allOrders as $orders_data) {
            $mostOrdered = $this->order->mostOrders($orders_data);
            foreach ($mostOrdered as $mostOrderedData) {
                $temp_i[] = $mostOrderedData->products_id;
            }
        }
        $detail = array();
        $temp_i = array_unique($temp_i);
        foreach ($temp_i as $temp_data) {
            $data = array('page_number' => '0', 'type' => 'topseller', 'products_id' => $temp_data, 'limit' => 7, 'min_price' => '', 'max_price' => '');
            $single_product = $this->products->products($data);
            if (!empty($single_product['product_data'][0])) {
                $detail[] = $single_product['product_data'][0];
            }
        }

        $result['weeklySoldProducts'] = array('success' => '1', 'product_data' => $detail, 'message' => "Returned all products.", 'total_record' => count($detail));
        
        session(['paymentResponseData' => '']); 
            
        session(['paymentResponse'=>'']);
        session(['payment_json','']);
        
        $category_section = array();
       if(!empty($result['commonContent']['settings']['home_category'])){
            $categories_array = explode(',',$result['commonContent']['settings']['home_category']);
            $index = 0;
            foreach($categories_array as $item){
                
                //get category section detail
                $category = $this->products->getCategorybyId($item);
                if($category){
                    $category_section[$index] = $category;
                    $data = array('page_number' => '0', 'categories_id'=>$category->categories_id, 'type' => 'topseller', 'limit' => $limit, 'min_price' => $min_price, 'max_price' => $max_price);
                    $single_product = $this->products->products($data);
                    $category_section[$index]->products = $single_product;
                    $index++;
                }
            }
            
        }
        // dd($month);
        // dd($category_section);
        $result['category_section'] = $category_section;
		$users = User::where('shop_name','!=',null)->get();
        $package = package::where('id',$id)->first();
        return view('web.packgCheckout',['title' => $title, 'final_theme' => $final_theme])->with(['package' => $package,'result' => $result,'users'=> $users,'month'=>$month]);      
    }

	public function shopGetProducts($id){
		$title = array('pageTitle' => Lang::get("website.Home"));
        $final_theme = $this->theme->theme();
        /*********************************************************************/
        /**                   GENERAL CONTENT TO DISPLAY                    **/
        /*********************************************************************/
                $result = array();
                $result['commonContent'] = $this->index->commonContent();
                $title = array('pageTitle' => Lang::get("website.Home"));
        /********************************************************************/

        /*********************************************************************/
        /**                   GENERAL SETTINGS TO FETCH PRODUCTS           **/
        /*******************************************************************/

        /**  SET LIMIT OF PRODUCTS  **/
        if (!empty($request->limit)) {
            $limit = $request->limit;
        } else {
            $limit = 12;
        }

        /**  MINIMUM PRICE **/
        if (!empty($request->min_price)) {
            $min_price = $request->min_price;
        } else {
            $min_price = '';
        }

        /**  MAXIMUM PRICE  **/
        if (!empty($request->max_price)) {
            $max_price = $request->max_price;
        } else {
            $max_price = '';
        }
        /*************************************************************************/
        /*********************************************************************/
        /**                     FETCH NEWEST PRODUCTS                       **/
        /*********************************************************************/

        $data = array('page_number' => '0', 'type' => '', 'limit' => 10, 'min_price' => $min_price, 'max_price' => $max_price);
        $newest_products = $this->products->products($data);
        $result['products'] = $newest_products;
        /*********************************************************************/
        /**                     Compare Counts                              **/
        /*********************************************************************/

    /*********************************************************************/

            /***************************************************************/
            /**   CART ARRAY RECORDS TO CHECK WETHER OR NOT DISPLAYED--   **/
            /**  --PRODUCT HAS BEEN ALREADY ADDE TO CART OR NOT           **/
    /***************************************************************/
            $cart = '';
            $result['cartArray'] = $this->products->cartIdArray($cart);
    /**************************************************************/

        //special products
        $data = array('page_number' => '0', 'type' => 'special', 'limit' => $limit, 'min_price' => $min_price, 'max_price' => $max_price);
        $special_products = $this->products->products($data);
        $result['special'] = $special_products;
        //Flash sale

        $data = array('page_number' => '0', 'type' => 'flashsale', 'limit' => $limit, 'min_price' => $min_price, 'max_price' => $max_price);
        $flash_sale = $this->products->products($data);
        $result['flash_sale'] = $flash_sale;
        // //top seller
        $data = array('page_number' => '0', 'type' => 'topseller', 'limit' => $limit, 'min_price' => $min_price, 'max_price' => $max_price);
        $top_seller = $this->products->products($data);
        $result['top_seller'] = $top_seller;

//most liked
        $data = array('page_number' => '0', 'type' => 'mostliked', 'limit' => $limit, 'min_price' => $min_price, 'max_price' => $max_price);
        $most_liked = $this->products->products($data);
        $result['most_liked'] = $most_liked;

//is feature
        $data = array('page_number' => '0', 'type' => 'is_feature', 'limit' => $limit, 'min_price' => $min_price, 'max_price' => $max_price);
        $featured = $this->products->products($data);
        $result['featured'] = $featured;

        $data = array('page_number' => '0', 'type' => '', 'limit' => '15', 'is_feature' => 1);
        $news = $this->news->getAllNews($data);
        $result['news'] = $news;
//current time

        $currentDate = Carbon\Carbon::now();
        $currentDate = $currentDate->toDateTimeString();

        $slides = $this->index->slides($currentDate);
        $result['slides'] = $slides;
        //liked products
        $result['liked_products'] = $this->products->likedProducts();

        $orders = $this->order->getOrders();
        if (count($orders) > 0) {
            $allOrders = $orders;
        } else {
            $allOrders = $this->order->allOrders();
        }

        $temp_i = array();
        foreach ($allOrders as $orders_data) {
            $mostOrdered = $this->order->mostOrders($orders_data);
            foreach ($mostOrdered as $mostOrderedData) {
                $temp_i[] = $mostOrderedData->products_id;
            }
        }
        $detail = array();
        $temp_i = array_unique($temp_i);
        foreach ($temp_i as $temp_data) {
            $data = array('page_number' => '0', 'type' => 'topseller', 'products_id' => $temp_data, 'limit' => 7, 'min_price' => '', 'max_price' => '');
            $single_product = $this->products->products($data);
            if (!empty($single_product['product_data'][0])) {
                $detail[] = $single_product['product_data'][0];
            }
        }

        $result['weeklySoldProducts'] = array('success' => '1', 'product_data' => $detail, 'message' => "Returned all products.", 'total_record' => count($detail));
        
        session(['paymentResponseData' => '']); 
            
        session(['paymentResponse'=>'']);
        session(['payment_json','']);
        
        $category_section = array();
       if(!empty($result['commonContent']['settings']['home_category'])){
            $categories_array = explode(',',$result['commonContent']['settings']['home_category']);
            $index = 0;
            foreach($categories_array as $item){
                
                //get category section detail
                $category = $this->products->getCategorybyId($item);
                if($category){
                    $category_section[$index] = $category;
                    $data = array('page_number' => '0', 'categories_id'=>$category->categories_id, 'type' => 'topseller', 'limit' => $limit, 'min_price' => $min_price, 'max_price' => $max_price);
                    $single_product = $this->products->products($data);
                    $category_section[$index]->products = $single_product;
                    $index++;
                }
            }
            
        }
        // dd($category_section);
        $result['category_section'] = $category_section;
        //$results['products'] = $products;
        $products = Products::where('admin_id',$id)->with('image')->get();
		// $user = User::with(['products'])->findOrFail($id);
		return view('web.shopProduct',['title' => $title, 'final_theme' => $final_theme])->with(['result' => $result,'product'=> $products]);
	}

    public function shopGetGroups($id){

		//groups
        $language_id = request()->language_id ?? 1;
        $responseData=array();
        $result = array();
        $result2 = array();
        $groups = Group::where('vendor_id',$id)->paginate(10);
        // dd($groups[0]);
        foreach($groups as $key=>$group){
            $responseData[$key]=$group;
            $language_id = request()->language_id ?? 1;
        //   $categories =  $this->belongsToMany('App\Product','group_product','group_id','product_id','id','products_id')
      

        // $categories = DB::table('products')
       
        $categories =  DB::table('products')
        // AppProduct::whereHas('productsofgroup', function($query) use ($group_id) {
        // 	$query->where('group_id', $group_id);
        // })
            ->leftJoin('group_product', 'group_product.product_id', '=', 'products.products_id')
            
            ->leftJoin('manufacturers', 'manufacturers.manufacturers_id', '=', 'products.manufacturers_id')
            ->leftJoin('manufacturers_info', 'manufacturers.manufacturers_id', '=', 'manufacturers_info.manufacturers_id')
            ->leftJoin('products_description', 'products_description.products_id', '=', 'products.products_id');

        $categories->LeftJoin('image_categories', function ($join) {
            $join->on('image_categories.image_id', '=', 'products.products_image')
                ->where(function ($query) {
                    $query->where('image_categories.image_type', '=', 'THUMBNAIL')
                        ->where('image_categories.image_type', '!=', 'THUMBNAIL')
                        ->orWhere('image_categories.image_type', '=', 'ACTUAL');
                });
        });
        

        $categories->where('products_description.language_id', '=', $language_id)
                    ->where('group_product.group_id', '=', $group->id)
                    // ->where('group_product.product_id', '=', $group->id)
            ->where('products.products_status', '=', '1');
        

            $data = $categories->get();
            if (count($data) > 0) {
                $result =[];
                $index = 0;
                foreach ($data as $products_data) {
                    $products_data->products_image = asset($products_data->products_image);
                    //check currency start
                    $requested_currency = 'SAR';//$request->currency_code;
                    $current_price = $products_data->products_price;
                    //dd($current_price, $requested_currency);
    
                    $products_price = Product::convertprice($current_price, $requested_currency);
                    ////////// for discount price    /////////
                    if (!empty($products_data->discount_price)) {
                        $discount_price = Product::convertprice($products_data->discount_price, $requested_currency);
                        $products_data->discount_price = $discount_price;
                    }
    
                    $products_data->products_price = $products_price;
                    $getName=  DB::table('users')->where('id', $products_data->admin_id)->first()->shop_name;
                    $products_data->shop_name=$getName;
                    $products_data->currency = $requested_currency;
                    //check currency end
    
                    
    
                    //for flashsale currency price end
                    $products_id = $products_data->products_id;
    
                    //multiple images
                    $products_images = DB::table('products_images')
                        ->LeftJoin('image_categories', function ($join) {
                            $join->on('image_categories.image_id', '=', 'products_images.image')
                                ->where(function ($query) {
                                    $query->where('image_categories.image_type', '=', 'THUMBNAIL')
                                        ->where('image_categories.image_type', '!=', 'THUMBNAIL')
                                        ->orWhere('image_categories.image_type', '=', 'ACTUAL');
                                });
                        })
                        ->select('products_images.*', 'image_categories.path as image')
                        ->where('products_id', '=', $products_id)->orderBy('sort_order', 'ASC')->get();
                    $products_data->images = $products_images;
    
                    $categories = DB::table('products_to_categories')
                        ->leftjoin('categories', 'categories.categories_id', 'products_to_categories.categories_id')
                        ->leftjoin('categories_description', 'categories_description.categories_id', 'products_to_categories.categories_id')
                        ->select('categories.categories_id', 'categories_description.categories_name', 'categories.categories_image', 'categories.categories_icon', 'categories.parent_id')
                        ->where('products_id', '=', $products_id)
                        ->where('categories_description.language_id', '=', $language_id)->get();
    
                    $products_data->categories = $categories;
    
                    $reviews = DB::table('reviews')
                        ->join('users', 'users.id', '=', 'reviews.customers_id')
                        ->select('reviews.*', 'users.avatar as image')
                        ->where('products_id', $products_data->products_id)
                        ->where('reviews_status', '1')
                        ->get();
    
                    $avarage_rate = 0;
                    $total_user_rated = 0;
    
                    if (count($reviews) > 0) {
    
                        $five_star = 0;
                        $five_count = 0;
    
                        $four_star = 0;
                        $four_count = 0;
    
                        $three_star = 0;
                        $three_count = 0;
    
                        $two_star = 0;
                        $two_count = 0;
    
                        $one_star = 0;
                        $one_count = 0;
    
                        foreach ($reviews as $review) {
    
                            //five star ratting
                            if ($review->reviews_rating == '5') {
                                $five_star += $review->reviews_rating;
                                $five_count++;
                            }
    
                            //four star ratting
                            if ($review->reviews_rating == '4') {
                                $four_star += $review->reviews_rating;
                                $four_count++;
                            }
                            //three star ratting
                            if ($review->reviews_rating == '3') {
                                $three_star += $review->reviews_rating;
                                $three_count++;
                            }
                            //two star ratting
                            if ($review->reviews_rating == '2') {
                                $two_star += $review->reviews_rating;
                                $two_count++;
                            }
    
                            //one star ratting
                            if ($review->reviews_rating == '1') {
                                $one_star += $review->reviews_rating;
                                $one_count++;
                            }
    
                        }
    
                        $five_ratio = round($five_count / count($reviews) * 100);
                        $four_ratio = round($four_count / count($reviews) * 100);
                        $three_ratio = round($three_count / count($reviews) * 100);
                        $two_ratio = round($two_count / count($reviews) * 100);
                        $one_ratio = round($one_count / count($reviews) * 100);
                        if(($five_star + $four_star + $three_star + $two_star + $one_star) > 0){
                            $avarage_rate = (5 * $five_star + 4 * $four_star + 3 * $three_star + 2 * $two_star + 1 * $one_star) / ($five_star + $four_star + $three_star + $two_star + $one_star);
                        }else{
                            $avarage_rate = 0;
                        }
    
                        $total_user_rated = count($reviews);
                        $reviewed_customers = $reviews;
                    } else {
                        $reviewed_customers = array();
                        $avarage_rate = 0;
                        $total_user_rated = 0;
    
                        $five_ratio = 0;
                        $four_ratio = 0;
                        $three_ratio = 0;
                        $two_ratio = 0;
                        $one_ratio = 0;
                    }
    
                    $products_data->rating = number_format($avarage_rate, 2);
                    $products_data->total_user_rated = $total_user_rated;
    
                    $products_data->five_ratio = $five_ratio;
                    $products_data->four_ratio = $four_ratio;
                    $products_data->three_ratio = $three_ratio;
                    $products_data->two_ratio = $two_ratio;
                    $products_data->one_ratio = $one_ratio;
    
                    //review by users
                    $products_data->reviewed_customers = $reviewed_customers;
    
                    array_push($result, $products_data);
                    $options = array();
                    $attr = array();
    
                    $stocks = 0;
                    $stockOut = 0;
                    $defaultStock = 0;
                    if ($products_data->products_type == '0') {
                        $stocks = DB::table('inventory')->where('products_id', $products_data->products_id)->where('stock_type', 'in')->sum('stock');
                        $stockOut = DB::table('inventory')->where('products_id', $products_data->products_id)->where('stock_type', 'out')->sum('stock');
                        $defaultStock = $stocks - $stockOut;
                    }
    
                    if ($products_data->products_max_stock < $defaultStock && $products_data->products_max_stock > 0) {
                        $result[$index]->defaultStock = $products_data->products_max_stock;
                    } else {
                        $result[$index]->defaultStock = $defaultStock;
                    }
    
                    //like product
                    if (auth()->check()) {
                        // dd(auth()->user()->id);
                        $liked_customers_id = auth()->user()->id;
                        $categories = DB::table('liked_products')->where('liked_products_id', '=', $products_id)->where('liked_customers_id', '=', $liked_customers_id)->get();
                        if (count($categories) > 0) {
                            $result[$index]->isLiked = '1';
                        } else {
                            $result[$index]->isLiked = '0';
                        }
                    } else {
                        $result[$index]->isLiked = '0';
                    }
    
                    
                    $listOfAttributes = array();
                    $index3 = 0;
    
                    // dd($getAllProductsParallel);
                    $getAllAttributes = DB::table('products_attributes')
                        ->where('products_id', '=', $products_id)
                        // ->whereIn('products_id', $getAllProductsParallel)
                        ->select('options_id', 'options_values_id', 'products_id','options_values_price')
                        ->get();
                    // dd($getAllAttributes);
    
                    foreach($getAllAttributes as $attribute){
                        $option_name = DB::table('products_options_descriptions')->where('products_options_id', '=', $attribute->options_id)->where('language_id', '=', $language_id)->first();
                        $attribute_option_name = $option_name != null ? $option_name->options_name : 'Not Exist';
                        $option_value = DB::table('products_options_values_descriptions')->where('products_options_values_id', '=', $attribute->options_values_id)->where('language_id', '=', $language_id)->first();
                        $attribute_option_value = $option_value != null ? $option_value->options_values_name : 'Not Exist';
                        $listOfAttributes[$index3][$attribute_option_name] =  $attribute_option_value ;
    
    
                    }
                    $listOfAttributes[$index3]['id'] = $products_id;
                    $listOfAttributes[$index3]['price'] = $products_data->products_price;
                    $listOfAttributes[$index3]['home_image'] =asset($products_data->products_image);
                    $listOfAttributes[$index3]['images'] = $products_images;
    
                    $index3++;
                    // $result['getAllAttributes'] = $getAllAttributes;
    
                    $getParallel = DB::table('products')->where('product_parent_id', '=', $products_id)->select('products_id','products_price','products_image')->get();
                    if($getParallel) {
                        foreach ($getParallel as $parallel) {
                            $getAllAttributesParallel = DB::table('products_attributes')->where('products_id', '=', $parallel->products_id)->select('options_id', 'options_values_id', 'products_id','options_values_price')->get();
                            foreach($getAllAttributesParallel as $attribute){
                                $option_name = DB::table('products_options_descriptions')->where('products_options_id', '=', $attribute->options_id)->where('language_id', '=', $language_id)->first();
                                $attribute_option_name = $option_name != null ? $option_name->options_name : 'Not Exist';
                                $option_value = DB::table('products_options_values_descriptions')->where('products_options_values_id', '=', $attribute->options_values_id)->where('language_id', '=', $language_id)->first();
                                $attribute_option_value = $option_value != null ? $option_value->options_values_name : 'Not Exist';
                                $listOfAttributes[$index3][$attribute_option_name] =  $attribute_option_value ;
                                // $listOfAttributes[$index3]['name'][] = $attribute_option_name;
                                // $listOfAttributes[$index3]['value'][] = $attribute_option_value;
                                // $listOfAttributes[$index3]['price'][] = $attribute->options_values_price;
                            }
                            $listOfAttributes[$index3]['id'] = $parallel->products_id;
                            $listOfAttributes[$index3]['home_image'] =asset($parallel->products_image);
                            $listOfAttributes[$index3]['price'] = $parallel->products_price;
    
                            //multiple images
                            $products_images = array();
                            $products_images = DB::table('products_images')
                                ->LeftJoin('image_categories', function ($join) {
                                    $join->on('image_categories.image_id', '=', 'products_images.image')
                                        ->where(function ($query) {
                                            $query->where('image_categories.image_type', '=', 'THUMBNAIL')
                                                ->where('image_categories.image_type', '!=', 'THUMBNAIL')
                                                ->orWhere('image_categories.image_type', '=', 'ACTUAL');
                                        });
                                })
                                ->select('products_images.*', 'image_categories.path as image')
                                ->where('products_id', '=', $parallel->products_id)->orderBy('sort_order', 'ASC')->get();
    
                                // $products_data->images=$products_images;
                                $listOfAttributes[$index3]['images'] = $products_images;
    
                            $index3++;
                        }
                    }
                    // dd($listOfAttributes);
                    $result[$index]->attributes = $listOfAttributes;
                    // $result[$index]->images2 = $products_images;
                    $index++;
                }
            }
            $responseData[$key]['products']=$result;

        }
        $title = array('pageTitle' => Lang::get("website.Home"));
        $final_theme = $this->theme->theme();
/*********************************************************************/
/**                   GENERAL CONTENT TO DISPLAY                    **/
/*********************************************************************/
        $result = array();
        $result['groups']=$responseData;

                $result['commonContent'] = $this->index->commonContent();
                $title = array('pageTitle' => Lang::get("website.Home"));
        /********************************************************************/

        /*********************************************************************/
        /**                   GENERAL SETTINGS TO FETCH PRODUCTS           **/
        /*******************************************************************/

        /**  SET LIMIT OF PRODUCTS  **/
        if (!empty($request->limit)) {
            $limit = $request->limit;
        } else {
            $limit = 12;
        }

        /**  MINIMUM PRICE **/
        if (!empty($request->min_price)) {
            $min_price = $request->min_price;
        } else {
            $min_price = '';
        }

        /**  MAXIMUM PRICE  **/
        if (!empty($request->max_price)) {
            $max_price = $request->max_price;
        } else {
            $max_price = '';
        }
        /*************************************************************************/
        /*********************************************************************/
        /**                     FETCH NEWEST PRODUCTS                       **/
        /*********************************************************************/

        $data = array('page_number' => '0', 'type' => '', 'limit' => 10, 'min_price' => $min_price, 'max_price' => $max_price);
        $newest_products = $this->products->products($data);
        $result['products'] = $newest_products;
        /*********************************************************************/
        /**                     Compare Counts                              **/
        /*********************************************************************/

    /*********************************************************************/

            /***************************************************************/
            /**   CART ARRAY RECORDS TO CHECK WETHER OR NOT DISPLAYED--   **/
            /**  --PRODUCT HAS BEEN ALREADY ADDE TO CART OR NOT           **/
    /***************************************************************/
            $cart = '';
            $result['cartArray'] = $this->products->cartIdArray($cart);
    /**************************************************************/

        //special products
        $data = array('page_number' => '0', 'type' => 'special', 'limit' => $limit, 'min_price' => $min_price, 'max_price' => $max_price);
        $special_products = $this->products->products($data);
        $result['special'] = $special_products;
        //Flash sale

        $data = array('page_number' => '0', 'type' => 'flashsale', 'limit' => $limit, 'min_price' => $min_price, 'max_price' => $max_price);
        $flash_sale = $this->products->products($data);
        $result['flash_sale'] = $flash_sale;
        // //top seller
        $data = array('page_number' => '0', 'type' => 'topseller', 'limit' => $limit, 'min_price' => $min_price, 'max_price' => $max_price);
        $top_seller = $this->products->products($data);
        $result['top_seller'] = $top_seller;

//most liked
        $data = array('page_number' => '0', 'type' => 'mostliked', 'limit' => $limit, 'min_price' => $min_price, 'max_price' => $max_price);
        $most_liked = $this->products->products($data);
        $result['most_liked'] = $most_liked;

//is feature
        $data = array('page_number' => '0', 'type' => 'is_feature', 'limit' => $limit, 'min_price' => $min_price, 'max_price' => $max_price);
        $featured = $this->products->products($data);
        $result['featured'] = $featured;

        $data = array('page_number' => '0', 'type' => '', 'limit' => '15', 'is_feature' => 1);
        $news = $this->news->getAllNews($data);
        $result['news'] = $news;
//current time

        $currentDate = Carbon\Carbon::now();
        $currentDate = $currentDate->toDateTimeString();

        $slides = $this->index->slides($currentDate);
        $result['slides'] = $slides;
        //liked products
        $result['liked_products'] = $this->products->likedProducts();

        $orders = $this->order->getOrders();
        if (count($orders) > 0) {
            $allOrders = $orders;
        } else {
            $allOrders = $this->order->allOrders();
        }

        $temp_i = array();
        foreach ($allOrders as $orders_data) {
            $mostOrdered = $this->order->mostOrders($orders_data);
            foreach ($mostOrdered as $mostOrderedData) {
                $temp_i[] = $mostOrderedData->products_id;
            }
        }
        $detail = array();
        $temp_i = array_unique($temp_i);
        foreach ($temp_i as $temp_data) {
            $data = array('page_number' => '0', 'type' => 'topseller', 'products_id' => $temp_data, 'limit' => 7, 'min_price' => '', 'max_price' => '');
            $single_product = $this->products->products($data);
            if (!empty($single_product['product_data'][0])) {
                $detail[] = $single_product['product_data'][0];
            }
        }

        $result['weeklySoldProducts'] = array('success' => '1', 'product_data' => $detail, 'message' => "Returned all products.", 'total_record' => count($detail));
        
        session(['paymentResponseData' => '']); 
            
        session(['paymentResponse'=>'']);
        session(['payment_json','']);
        
        $category_section = array();
       if(!empty($result['commonContent']['settings']['home_category'])){
            $categories_array = explode(',',$result['commonContent']['settings']['home_category']);
            $index = 0;
            foreach($categories_array as $item){
                
                //get category section detail
                $category = $this->products->getCategorybyId($item);
                if($category){
                    $category_section[$index] = $category;
                    $data = array('page_number' => '0', 'categories_id'=>$category->categories_id, 'type' => 'topseller', 'limit' => $limit, 'min_price' => $min_price, 'max_price' => $max_price);
                    $single_product = $this->products->products($data);
                    $category_section[$index]->products = $single_product;
                    $index++;
                }
            }
            
        }
        // dd($category_section);
        $result['category_section'] = $category_section;
        //$results['products'] = $products;
        $products = Products::where('admin_id',$id)->with('image')->get();
		// $user = User::with(['products'])->findOrFail($id);
        
        $user = User::where('id',$id)->first();
        $result['user']=$user;
		return view('web.shopGroup',['title' => $title, 'final_theme' => $final_theme])->with(['result' => $result,'product'=> $products]);
	}

    public function getPackge($type){
        $title = array('pageTitle' => Lang::get("website.Home"));
        $final_theme = $this->theme->theme();
/*********************************************************************/
/**                   GENERAL CONTENT TO DISPLAY                    **/
/*********************************************************************/
        $result = array();
        $result['commonContent'] = $this->index->commonContent();
        $title = array('pageTitle' => Lang::get("website.Home"));
/********************************************************************/

/*********************************************************************/
/**                   GENERAL SETTINGS TO FETCH PRODUCTS           **/
/*******************************************************************/

        /**  SET LIMIT OF PRODUCTS  **/
        if (!empty($request->limit)) {
            $limit = $request->limit;
        } else {
            $limit = 12;
        }

        /**  MINIMUM PRICE **/
        if (!empty($request->min_price)) {
            $min_price = $request->min_price;
        } else {
            $min_price = '';
        }

        /**  MAXIMUM PRICE  **/
        if (!empty($request->max_price)) {
            $max_price = $request->max_price;
        } else {
            $max_price = '';
        }
/*************************************************************************/
        /*********************************************************************/
        /**                     FETCH NEWEST PRODUCTS                       **/
        /*********************************************************************/

        $data = array('page_number' => '0', 'type' => '', 'limit' => 10, 'min_price' => $min_price, 'max_price' => $max_price);
        $newest_products = $this->products->products($data);
        $result['products'] = $newest_products;
        /*********************************************************************/
        /**                     Compare Counts                              **/
        /*********************************************************************/

/*********************************************************************/

        /***************************************************************/
        /**   CART ARRAY RECORDS TO CHECK WETHER OR NOT DISPLAYED--   **/
        /**  --PRODUCT HAS BEEN ALREADY ADDE TO CART OR NOT           **/
/***************************************************************/
        $cart = '';
        $result['cartArray'] = $this->products->cartIdArray($cart);
/**************************************************************/

        //special products
        $data = array('page_number' => '0', 'type' => 'special', 'limit' => $limit, 'min_price' => $min_price, 'max_price' => $max_price);
        $special_products = $this->products->products($data);
        $result['special'] = $special_products;
        //Flash sale

        $data = array('page_number' => '0', 'type' => 'flashsale', 'limit' => $limit, 'min_price' => $min_price, 'max_price' => $max_price);
        $flash_sale = $this->products->products($data);
        $result['flash_sale'] = $flash_sale;
        // //top seller
        $data = array('page_number' => '0', 'type' => 'topseller', 'limit' => $limit, 'min_price' => $min_price, 'max_price' => $max_price);
        $top_seller = $this->products->products($data);
        $result['top_seller'] = $top_seller;

//most liked
        $data = array('page_number' => '0', 'type' => 'mostliked', 'limit' => $limit, 'min_price' => $min_price, 'max_price' => $max_price);
        $most_liked = $this->products->products($data);
        $result['most_liked'] = $most_liked;

//is feature
        $data = array('page_number' => '0', 'type' => 'is_feature', 'limit' => $limit, 'min_price' => $min_price, 'max_price' => $max_price);
        $featured = $this->products->products($data);
        $result['featured'] = $featured;

        $data = array('page_number' => '0', 'type' => '', 'limit' => '15', 'is_feature' => 1);
        $news = $this->news->getAllNews($data);
        $result['news'] = $news;
//current time

        $currentDate = Carbon\Carbon::now();
        $currentDate = $currentDate->toDateTimeString();

        $slides = $this->index->slides($currentDate);
        $result['slides'] = $slides;
        //liked products
        $result['liked_products'] = $this->products->likedProducts();

        $orders = $this->order->getOrders();
        if (count($orders) > 0) {
            $allOrders = $orders;
        } else {
            $allOrders = $this->order->allOrders();
        }

        $temp_i = array();
        foreach ($allOrders as $orders_data) {
            $mostOrdered = $this->order->mostOrders($orders_data);
            foreach ($mostOrdered as $mostOrderedData) {
                $temp_i[] = $mostOrderedData->products_id;
            }
        }
        $detail = array();
        $temp_i = array_unique($temp_i);
        foreach ($temp_i as $temp_data) {
            $data = array('page_number' => '0', 'type' => 'topseller', 'products_id' => $temp_data, 'limit' => 7, 'min_price' => '', 'max_price' => '');
            $single_product = $this->products->products($data);
            if (!empty($single_product['product_data'][0])) {
                $detail[] = $single_product['product_data'][0];
            }
        }

        $result['weeklySoldProducts'] = array('success' => '1', 'product_data' => $detail, 'message' => "Returned all products.", 'total_record' => count($detail));
        
        session(['paymentResponseData' => '']); 
            
        session(['paymentResponse'=>'']);
        session(['payment_json','']);
        
        $category_section = array();
       if(!empty($result['commonContent']['settings']['home_category'])){
            $categories_array = explode(',',$result['commonContent']['settings']['home_category']);
            $index = 0;
            foreach($categories_array as $item){
                
                //get category section detail
                $category = $this->products->getCategorybyId($item);
                if($category){
                    $category_section[$index] = $category;
                    $data = array('page_number' => '0', 'categories_id'=>$category->categories_id, 'type' => 'topseller', 'limit' => $limit, 'min_price' => $min_price, 'max_price' => $max_price);
                    $single_product = $this->products->products($data);
                    $category_section[$index]->products = $single_product;
                    $index++;
                }
            }
            
        }
        // dd($category_section);
        $result['category_section'] = $category_section;
        //$results['products'] = $products;
        $package = package::where('type',$type)->first();
        return view('web.getPackage',['title' => $title, 'final_theme' => $final_theme])->with(['result' => $result,'package'=> $package]);
    }


    public function charge(Request $request){
        
        $package = package::findOrFail($request->package_id);
        $amount = $request->month == 12 ? $package->price * 12 - $package->discount : $package->price;
        \App\Models\Web\PackgeRequest::create([
            'user_id' => auth()->user()->id,
            'packge_id' => $package->id,
            'month' => $request->month,
        ]);
        return redirect()->back()->with('message','تم ارسال الطلب بنجاح');
        $curl = curl_init();
        curl_setopt_array($curl, array(
        CURLOPT_URL => "https://api.tap.company/v2/charges",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => "{\"amount\":$amount,\"currency\":\"KWD\",\"threeDSecure\":true,\"save_card\":false,\"description\":\"Test Description\",\"statement_descriptor\":\"Sample\",\"metadata\":{\"udf1\":\"test 1\",\"udf2\":\"test 2\"},\"reference\":{\"transaction\":\"txn_0001\",\"order\":\"ord_0001\"},\"receipt\":{\"email\":false,\"sms\":true},\"customer\":{\"first_name\":\"test\",\"middle_name\":\"test\",\"last_name\":\"test\",\"email\":\"test@test.com\",\"phone\":{\"country_code\":\"965\",\"number\":\"50000000\"}},\"merchant\":{\"id\":\"\"},\"source\":{\"id\":\"src_kw.knet\"},\"post\":{\"url\":\"http://your_website.com/post_url\"},\"redirect\":{\"url\":\"http://your_website.com/redirect_url\"}}",
        CURLOPT_HTTPHEADER => array(
            "authorization: Bearer sk_test_XKokBfNWv6FIYuTMg5sLPjhJ",
            "content-type: application/json"
        ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
        echo "cURL Error #:" . $err;
        } else {
        \App\Models\Web\PackgeRequest::create([
            'user_id' => auth()->user()->id,
            'packge_id' => $package->id,
        ]);
        echo $response;
        }
    }
}

?>