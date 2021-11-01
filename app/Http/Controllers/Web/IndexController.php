<?php
namespace App\Http\Controllers\Web;

use App\Group;
use App\Models\AppModels\Product;
use App\Models\Web\Currency;
use App\Models\Web\Index;
use App\Models\Web\Languages;
use App\Models\Web\News;
use App\Models\Web\Order;
use App\Models\Web\Products;
use App\Models\Web\productCategory;
use Auth;
use Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Mail;
use Lang;
use View;
use DB;
use Cookie;

class IndexController extends Controller
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

    public function index()
    {
        
        //groups
        $language_id = request()->language_id ?? 1;
        $responseData=array();
        $result = array();
        $result2 = array();
        $groups = Group::paginate(10);
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
        // $productCategory = \App\Models\Web\productCategory::where('categories_id',1)->get();
        return view("web.index", ['title' => $title, 'final_theme' => $final_theme])->with(['result' => $result]);
    }

    public function maintance()
    {
        return view('errors.maintance');
    }

    public function error()
    {
        return view('errors.general_error', ['msg' => $msg]);
    }

    public function logout()
    {
        Auth::guard('customer')->logout();
        return redirect()->back();
    }
    public function test()
    {
        $productcategories = $this->products->productCategories1();
        echo print_r($productcategories);

    }

    private function setHeader($header_id)
    {
        $count = $this->order->countCompare();
        $languages = $this->languages->languages();
        $currencies = $this->currencies->getter();
        $productcategories = $this->products->productCategories();
        $title = array('pageTitle' => Lang::get("website.Home"));
        $result = array();
        $result['commonContent'] = $this->index->commonContent();

        if ($header_id == 1) {

            $header = (string) View::make('web.headers.headerOne', ['count' => $count, 'currencies' => $currencies, 'languages' => $languages, 'productcategories' => $productcategories, 'result' => $result])->render();
        } elseif ($header_id == 2) {
            $header = (string) View::make('web.headers.headerTwo');
        } elseif ($header_id == 3) {
            $header = (string) View::make('web.headers.headerThree')->render();
        } elseif ($header_id == 4) {
            $header = (string) View::make('web.headers.headerFour')->render();
        } elseif ($header_id == 5) {
            $header = (string) View::make('web.headers.headerFive')->render();
        } elseif ($header_id == 6) {
            $header = (string) View::make('web.headers.headerSix')->render();
        } elseif ($header_id == 7) {
            $header = (string) View::make('web.headers.headerSeven')->render();
        } elseif ($header_id == 8) {
            $header = (string) View::make('web.headers.headerEight')->render();
        } elseif ($header_id == 9) {
            $header = (string) View::make('web.headers.headerNine')->render();
        } else {
            $header = (string) View::make('web.headers.headerTen')->render();
        }
        return $header;
    }

    private function setBanner($banner_id)
    {
        if ($banner_id == 1) {
            $banner = (string) View::make('web.banners.banner1')->render();
        } elseif ($banner_id == 2) {
            $banner = (string) View::make('web.banners.banner2')->render();
        } elseif ($banner_id == 3) {
            $banner = (string) View::make('web.banners.banner3')->render();
        } elseif ($banner_id == 4) {
            $banner = (string) View::make('web.banners.banner4')->render();
        } elseif ($banner_id == 5) {
            $banner = (string) View::make('web.banners.banner5')->render();
        } elseif ($banner_id == 6) {
            $banner = (string) View::make('web.banners.banner6')->render();
        } elseif ($banner_id == 7) {
            $banner = (string) View::make('web.banners.banner7')->render();
        } elseif ($banner_id == 8) {
            $banner = (string) View::make('web.banners.banner8')->render();
        } elseif ($banner_id == 9) {
            $banner = (string) View::make('web.banners.banner9')->render();
        } elseif ($banner_id == 10) {
            $banner = (string) View::make('web.banners.banner10')->render();
        } elseif ($banner_id == 11) {
            $banner = (string) View::make('web.banners.banner11')->render();
        } elseif ($banner_id == 12) {
            $banner = (string) View::make('web.banners.banner12')->render();
        } elseif ($banner_id == 13) {
            $banner = (string) View::make('web.banners.banner13')->render();
        } elseif ($banner_id == 14) {
            $banner = (string) View::make('web.banners.banner14')->render();
        } elseif ($banner_id == 15) {
            $banner = (string) View::make('web.banners.banner15')->render();
        } elseif ($banner_id == 16) {
            $banner = (string) View::make('web.banners.banner16')->render();
        } elseif ($banner_id == 17) {
            $banner = (string) View::make('web.banners.banner17')->render();
        } elseif ($banner_id == 18) {
            $banner = (string) View::make('web.banners.banner18')->render();
        } elseif ($banner_id == 19) {
            $banner = (string) View::make('web.banners.banner19')->render();
        } else {
            $banner = (string) View::make('web.banners.banner20')->render();
        }
        return $banner;
    }

    private function setFooter($footer_id)
    {
        if ($footer_id == 1) {
            $footer = (string) View::make('web.footers.footer1')->render();
        } elseif ($footer_id == 2) {
            $footer = (string) View::make('web.footers.footer2')->render();
        } elseif ($footer_id == 3) {
            $footer = (string) View::make('web.footers.footer3')->render();
        } elseif ($footer_id == 4) {
            $footer = (string) View::make('web.footers.footer4')->render();
        } elseif ($footer_id == 5) {
            $footer = (string) View::make('web.footers.footer5')->render();
        } elseif ($footer_id == 6) {
            $footer = (string) View::make('web.footers.footer6')->render();
        } elseif ($footer_id == 7) {
            $footer = (string) View::make('web.footers.footer7')->render();
        } elseif ($footer_id == 8) {
            $footer = (string) View::make('web.footers.footer8')->render();
        } elseif ($footer_id == 9) {
            $footer = (string) View::make('web.footers.footer9')->render();
        } else {
            $footer = (string) View::make('web.footers.footer10')->render();
        }
        return $footer;
    }
    //page
    public function page(Request $request)
    {

        $pages = $this->order->getPages($request);
        if (count($pages) > 0) {
            $title = array('pageTitle' => $pages[0]->name);
            $final_theme = $this->theme->theme();
            $result['commonContent'] = $this->index->commonContent();
            $result['pages'] = $pages;
            return view("web.page", ['title' => $title, 'final_theme' => $final_theme])->with('result', $result);

        } else {
            return redirect()->intended('/');
        }
    }
    //myContactUs
    public function contactus(Request $request)
    {
        $title = array('pageTitle' => Lang::get("website.Contact Us"));
        $result = array();
        $final_theme = $this->theme->theme();
        $result['commonContent'] = $this->index->commonContent();

        return view("web.contact-us", ['title' => $title, 'final_theme' => $final_theme])->with('result', $result);
    }
    //processContactUs
    public function processContactUs(Request $request)
    {
        $name = $request->name;
        $email = $request->email;
        $subject = $request->subject;
        $message = $request->message;

        $result['commonContent'] = $this->index->commonContent();

        $data = array('name' => $name, 'email' => $email, 'subject' => $subject, 'message' => $message, 'adminEmail' => $result['commonContent']['setting'][3]->value);

        Mail::send('/mail/contactUs', ['data' => $data], function ($m) use ($data) {
            $m->to($data['adminEmail'])->subject(Lang::get("website.contact us title"))->getSwiftMessage()
                ->getHeaders()
                ->addTextHeader('x-mailgun-native-send', 'true');
        });

        return redirect()->back()->with('success', Lang::get("website.contact us message"));
    }

    //setcookie
    public function setcookie(Request $request)
    {
        Cookie::queue('cookies_data', 1, 4000);
        return json_encode(array('accept'=>'yes'));
    }

    //newsletter
    public function newsletter(Request $request)
    {
        if (!empty(auth()->guard('customer')->user()->id)) {
            $customers_id = auth()->guard('customer')->user()->id;  
            $existUser = DB::table('customers')
                          ->leftJoin('users','customers.customers_id','=','users.id')
                          ->where('customers.fb_id', '=', $customers_id)
                          ->first();

                      
            if($existUser){                
                DB::table('customers')->where('user_id','=',$customers_id)->update([
                    'customers_newsletter' => 1,
                ]);
            }else{
                DB::table('customers')->insertGetId([
                    'user_id' => $customers_id,
                    'customers_newsletter' => 1,
                ]);
            }
                                            
        }
        session(['newsletter' => 1]);
        
        return 'subscribed';
    }


    public function subscribeMail(Request $request){
        $settings = $this->index->commonContent();
        if(!empty($settings['setting'][122]->value) and !empty($settings['setting'][122]->value)){        
            $email = $request->email;

            $list_id = $settings['setting'][123]->value;
            $api_key = $settings['setting'][122]->value;
            
            $data_center = substr($api_key,strpos($api_key,'-')+1);
            
            $url = 'https://'. $data_center .'.api.mailchimp.com/3.0/lists/'. $list_id .'/members';
            
            $json = json_encode([
                'email_address' => $email,
                'status'        => 'subscribed', //pass 'subscribed' or 'pending'
            ]);
            
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_USERPWD, 'user:' . $api_key);
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
            $result = curl_exec($ch);
            $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            
            if($status_code==200){
                //subscribed
                print '1';
            }elseif($status_code==400){
                print '2';
            }else{
                print '0';
            }
        }else{
            print '0';
        }
        
    }
    

}
