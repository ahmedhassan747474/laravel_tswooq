<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;

use App\Http\Controllers\Api\V1\BaseController;
use App\Transformers\Api\V1\UserTransformer;

use App\Http\Controllers\AdminControllers\SiteSettingController;

use App;
use App\Group;
use App\Http\Resources\ProductCollection;
use App\Traits\ProductTrait;
use Illuminate\Support\Facades\File;
use App\Models\AppModels\Product;
use App\Product as AppProduct;
use Carbon;
use DB;

class ProductController extends BaseController
{
    use ProductTrait;

    function __construct(UserTransformer $user_transformer)
    {
        config(['auth.defaults.guard' => 'api']);
        $this->user_transformer = $user_transformer;
        App::setLocale(request()->header('Accept-Language'));
    }
    //get allcategories
	public function allcategories(Request $request){
        $language_id = $request->language_id;

        $item = DB::table('categories')
            ->leftJoin('categories_description', 'categories_description.categories_id', '=', 'categories.categories_id')
            ->LeftJoin('image_categories', function ($join) {
                $join->on('image_categories.image_id', '=', 'categories.categories_image')
                    ->where(function ($query) {
                        $query->where('image_categories.image_type', '=', 'THUMBNAIL')
                            ->where('image_categories.image_type', '!=', 'THUMBNAIL')
                            ->orWhere('image_categories.image_type', '=', 'ACTUAL');
                    });
            })
            ->LeftJoin('image_categories as icon_categories', function ($join) {
                $join->on('icon_categories.image_id', '=', 'categories.categories_icon')
                    ->where(function ($query) {
                        $query->where('icon_categories.image_type', '=', 'THUMBNAIL')
                            ->where('icon_categories.image_type', '!=', 'THUMBNAIL')
                            ->orWhere('icon_categories.image_type', '=', 'ACTUAL');
                    });
            })

            ->select('categories.categories_id', 'categories_description.categories_name', 'categories.parent_id',
                'image_categories.path as image', 'icon_categories.path as icon')
            ->where('categories.parent_id', '=', '0')
            ->where('categories_description.language_id', '=', $language_id);

        $categories = $item->where('categories_status', '1')
            ->orderby('categories_id', 'ASC')
            ->groupby('categories_id')
            ->get();

        // dd($categories);

        if (count($categories) > 0) {

            $items = array();
            $index = 0;
            foreach ($categories as $category) {
                $category->image = asset($category->image);
                $category->icon = asset($category->icon);
                array_push($items, $category);
                $content = DB::table('products_to_categories')
                    ->LeftJoin('products', 'products.products_id', '=', 'products_to_categories.products_id')
                    ->LeftJoin('flash_sale', 'flash_sale.products_id', '=', 'products.products_id')
                    ->whereNotIn('products.products_id', function ($query) {
                        $query->select('flash_sale.products_id')->from('flash_sale');
                    })
                    ->where('products_to_categories.categories_id', $category->categories_id)
                    ->select(DB::raw('COUNT(DISTINCT products.products_id) as total_products'))->first();
                $items[$index++]->total_products = $content->total_products;
            }

            $responseData = array('success' => '1', 'data' => $items, 'message' => "Returned all categories.", 'categories' => count($categories));
        } else {
            $responseData = array('success' => '0', 'data' => array(), 'message' => "No category found.", 'categories' => array());
        }

        return response()->json($responseData);
    }

    //get all brands
    public function allbrands(Request $request) {
        $language_id = $request->language_id;

        $item = DB::table('categories')
            ->leftJoin('categories_description', 'categories_description.categories_id', '=', 'categories.categories_id')
            ->LeftJoin('image_categories', function ($join) {
                $join->on('image_categories.image_id', '=', 'categories.categories_image')
                    ->where(function ($query) {
                        $query->where('image_categories.image_type', '=', 'THUMBNAIL')
                            ->where('image_categories.image_type', '!=', 'THUMBNAIL')
                            ->orWhere('image_categories.image_type', '=', 'ACTUAL');
                    });
            })
            ->LeftJoin('image_categories as icon_categories', function ($join) {
                $join->on('icon_categories.image_id', '=', 'categories.categories_icon')
                    ->where(function ($query) {
                        $query->where('icon_categories.image_type', '=', 'THUMBNAIL')
                            ->where('icon_categories.image_type', '!=', 'THUMBNAIL')
                            ->orWhere('icon_categories.image_type', '=', 'ACTUAL');
                    });
            })

            ->select('categories.categories_id', 'categories_description.categories_name', 'categories.parent_id',
                'image_categories.path as image', 'icon_categories.path as icon')
            ->where('categories.parent_id', '>', '0')
            ->where('categories_description.language_id', '=', $language_id);

        $categories = $item->where('categories_status', '1')
            ->orderby('categories_id', 'ASC')
            ->groupby('categories_id')
            ->get();

        // dd($categories);

        if (count($categories) > 0) {

            $items = array();
            $index = 0;
            foreach ($categories as $category) {
                $category->image = asset($category->image);
                $category->icon = asset($category->icon);
                array_push($items, $category);
                $content = DB::table('products_to_categories')
                    ->LeftJoin('products', 'products.products_id', '=', 'products_to_categories.products_id')
                    ->LeftJoin('flash_sale', 'flash_sale.products_id', '=', 'products.products_id')
                    ->whereNotIn('products.products_id', function ($query) {
                        $query->select('flash_sale.products_id')->from('flash_sale');
                    })
                    ->where('products_to_categories.categories_id', $category->categories_id)
                    ->select(DB::raw('COUNT(DISTINCT products.products_id) as total_products'))->first();
                $items[$index++]->total_products = $content->total_products;
            }

            $responseData = array('success' => '1', 'data' => $items, 'message' => "Returned all categories.", 'categories' => count($categories));
        } else {
            $responseData = array('success' => '0', 'data' => array(), 'message' => "No category found.", 'categories' => array());
        }

        return response()->json($responseData);
    }

    //get all brands
    public function allbrandsbycategory(Request $request) {
        $language_id = $request->language_id;
        $category_id = $request->category_id;

        $item = DB::table('categories')
            ->leftJoin('categories_description', 'categories_description.categories_id', '=', 'categories.categories_id')
            ->LeftJoin('image_categories', function ($join) {
                $join->on('image_categories.image_id', '=', 'categories.categories_image')
                    ->where(function ($query) {
                        $query->where('image_categories.image_type', '=', 'THUMBNAIL')
                            ->where('image_categories.image_type', '!=', 'THUMBNAIL')
                            ->orWhere('image_categories.image_type', '=', 'ACTUAL');
                    });
            })
            ->LeftJoin('image_categories as icon_categories', function ($join) {
                $join->on('icon_categories.image_id', '=', 'categories.categories_icon')
                    ->where(function ($query) {
                        $query->where('icon_categories.image_type', '=', 'THUMBNAIL')
                            ->where('icon_categories.image_type', '!=', 'THUMBNAIL')
                            ->orWhere('icon_categories.image_type', '=', 'ACTUAL');
                    });
            })

            ->select('categories.categories_id', 'categories_description.categories_name', 'categories.parent_id',
                'image_categories.path as image', 'icon_categories.path as icon')
            ->where('categories.parent_id', '=', $category_id)
            ->where('categories_description.language_id', '=', $language_id);

        $categories = $item->where('categories_status', '1')
            ->orderby('categories_id', 'ASC')
            ->groupby('categories_id')
            ->get();

        // dd($categories);

        if (count($categories) > 0) {

            $items = array();
            $index = 0;
            foreach ($categories as $category) {
                $category->image = asset($category->image);
                $category->icon = asset($category->icon);
                array_push($items, $category);
                $content = DB::table('products_to_categories')
                    ->LeftJoin('products', 'products.products_id', '=', 'products_to_categories.products_id')
                    ->LeftJoin('flash_sale', 'flash_sale.products_id', '=', 'products.products_id')
                    ->whereNotIn('products.products_id', function ($query) {
                        $query->select('flash_sale.products_id')->from('flash_sale');
                    })
                    ->where('products_to_categories.categories_id', $category->categories_id)
                    ->select(DB::raw('COUNT(DISTINCT products.products_id) as total_products'))->first();
                $items[$index++]->total_products = $content->total_products;
            }

            $responseData = array('success' => '1', 'data' => $items, 'message' => "Returned all categories.", 'categories' => count($categories));
        } else {
            $responseData = array('success' => '0', 'data' => array(), 'message' => "No category found.", 'categories' => array());
        }

        return response()->json($responseData);
    }

    //getallproducts
    public function getallproducts(Request $request){
      
        $products = AppProduct::latest()
        ->with(['stocks','images'])->get();
        return new ProductCollection($products);
    }

    //getallproducts
    public function get_all_group_products(Request $request){
        $group_id = $request->group_id;
        if ($group_id){
            $products=AppProduct::whereHas('groups',function($q) use($group_id){
                $q->where('group_id',$group_id);
            })->with(['stocks','images'])->latest();
       
            $responseData = $this->productObject($request,$products);
            return new ProductCollection($responseData);
        }

    }

    public function get_groups_by_vendor(Request $request)
    {
 
        $groups = Group::with('products')->where('vendor_id',$request->vendor_id)->get();
        
        return response()->json(['data'=>$groups]);
    }
    public function get_all_groups(Request $request)
    {
        $language_id = $request->language_id??1;
        $skip = $request->page_number . '0';
        $currentDate = time();
        $type = $request->type;

        if ($type == "a to z") {
            $sortby = "products_name";
            $order = "ASC";
        } elseif ($type == "z to a") {
            $sortby = "products_name";
            $order = "DESC";
        } elseif ($type == "high to low") {
            $sortby = "products_price";
            $order = "DESC";
        } elseif ($type == "low to high") {
            $sortby = "products_price";
            $order = "ASC";
        } elseif ($type == "top seller") {
            $sortby = "products_ordered";
            $order = "DESC";
        } elseif ($type == "most liked") {
            $sortby = "products_liked";
            $order = "DESC";
        } elseif ($type == "special") { //deals special products
            $sortby = "specials.products_id";
            $order = "desc";
        } elseif ($type == "flashsale") { //flashsale products
            $sortby = "flash_sale.flash_start_date";
            $order = "asc";
        } else {
            $sortby = "products.products_id";
            $order = "desc";
        }

        $filterProducts = array();
        $eliminateRecord = array();
        
        $responseData=array();
        $result = array();
        $result2 = array();
        $groups = Group::with('products')->get();
        // dd($groups[0]);
        // foreach($groups as $key=>$group){
        //     $responseData[$key]=$group;
        //     $language_id = request()->language_id ?? 1;
        // //   $categories =  $this->belongsToMany('App\Product','group_product','group_id','product_id','id','products_id')
      

        // // $categories = DB::table('products')
        // $categories =  DB::table('products')
        // // AppProduct::whereHas('productsofgroup', function($query) use ($group_id) {
        // // 	$query->where('group_id', $group_id);
        // // })
        //     ->leftJoin('group_product', 'group_product.product_id', '=', 'products.products_id')
        //     ->leftJoin('manufacturers', 'manufacturers.manufacturers_id', '=', 'products.manufacturers_id')
        //     ->leftJoin('manufacturers_info', 'manufacturers.manufacturers_id', '=', 'manufacturers_info.manufacturers_id')
        //     ->leftJoin('products_description', 'products_description.products_id', '=', 'products.products_id');

        // $categories->LeftJoin('image_categories', function ($join) {
        //     $join->on('image_categories.image_id', '=', 'products.products_image')
        //         ->where(function ($query) {
        //             $query->where('image_categories.image_type', '=', 'THUMBNAIL')
        //                 ->where('image_categories.image_type', '!=', 'THUMBNAIL')
        //                 ->orWhere('image_categories.image_type', '=', 'ACTUAL');
        //         });
        // });

        // if (!empty($request->categories_id)) {

        //     $categories->LeftJoin('products_to_categories', 'products.products_id', '=', 'products_to_categories.products_id')
        //         ->leftJoin('categories', 'categories.categories_id', '=', 'products_to_categories.categories_id')
        //         ->LeftJoin('categories_description', 'categories_description.categories_id', '=', 'products_to_categories.categories_id');
        // }

        // //wishlist customer id
        // if ($type == "wishlist") {
        //     $categories->LeftJoin('liked_products', 'liked_products.liked_products_id', '=', 'products.products_id')
        //         ->select(DB::raw(time() . ' as server_time'), 'products.*', 'products_description.*', 'manufacturers.*', 'manufacturers_info.manufacturers_url', 'image_categories.path as products_image');
        // }

        // //parameter special
        // elseif ($type == "special") {
        //     $categories->LeftJoin('specials', 'specials.products_id', '=', 'products.products_id')
        //         ->select(DB::raw(time() . ' as server_time'), 'products.*', 'products_description.*', 'manufacturers.*', 'manufacturers_info.manufacturers_url', 'specials.specials_new_products_price as discount_price', 'specials.specials_new_products_price as discount_price', 'image_categories.path as products_image');
        // } elseif ($type == "flashsale") {
        //     //flash sale
        //     $categories->
        //         LeftJoin('flash_sale', 'flash_sale.products_id', '=', 'products.products_id')
        //         ->select(DB::raw(time() . ' as server_time'), 'products.*', 'products_description.*', 'manufacturers.*', 'manufacturers_info.manufacturers_url', 'flash_sale.flash_start_date', 'flash_sale.flash_expires_date', 'flash_sale.flash_sale_products_price as flash_price', 'image_categories.path as products_image');

        // } else {
        //     $categories->LeftJoin('specials', function ($join) use ($currentDate) {
        //         $join->on('specials.products_id', '=', 'products.products_id')->where('specials.status', '=', '1')->where('expires_date', '>', $currentDate);
        //     })->select(DB::raw(time() . ' as server_time'), 'products.*', 'products_description.*', 'manufacturers.*', 'manufacturers_info.manufacturers_url', 'specials.specials_new_products_price as discount_price', 'image_categories.path as products_image');
        // }

        // if ($type == "special") { //deals special products
        //     $categories->where('specials.status', '=', '1')->where('expires_date', '>', $currentDate);
        // }

        // if ($type == "flashsale") { //flashsale
        //     $categories->where('flash_sale.flash_status', '=', '1')->where('flash_expires_date', '>', $currentDate);
        // } else {
        //     $categories->whereNotIn('products.products_id', function ($query) {
        //         $query->select('flash_sale.products_id')->from('flash_sale');
        //     });
        // }

        // //get single category products
        // if (!empty($request->categories_id)) {
        //     $categories->where('products_to_categories.categories_id', '=', $request->categories_id)
        //         ->where('categories_description.language_id', '=', $language_id);
        // }

        // //get single products
        // if (!empty($request->products_id) && $request->products_id != "") {
        //     $categories->where('products.products_id', '=', $request->products_id);
        // }

        // if (!empty($request->products_slug) && $request->products_slug != "") {
        //     $categories->where('products.products_slug', '=', $request->products_slug);
        // }

       
        // $categories->where('group_product.group_id',$group->group_id);

        // if ($type == "top seller") {
        //     $categories->where('products.products_ordered', '>', 0);
        // }

        // if ($type == "most liked") {
        //     $categories->where('products.products_liked', '>', 0);
        // }

        // //wishlist customer id
        // if ($type == "wishlist") {
        //     $categories->where('liked_customers_id', '=', $request->customers_id);
        // }

        // $categories->where('products_description.language_id', '=', $language_id)
        //     ->where('products.products_status', '=', '1')
        //     ->orderBy($sortby, $order);

        // if ($type == "special") { //deals special products
        //     $categories->groupBy('products.products_id');
        // }

        // //count
        // $total_record = $categories->get();
        // $data = $categories->take(10)->get();


        // $result = array();
        // $result2 = array();
        // //check if record exist
        // if (count($data) > 0) {
        //     $index = 0;
        //     foreach ($data as $products_data) {
        //         $products_data->products_image = asset($products_data->products_image);
        //         //check currency start
        //         $requested_currency = 'SAR';//$request->currency_code;
        //         $current_price = $products_data->products_price;
        //         //dd($current_price, $requested_currency);

        //         $products_price = Product::convertprice($current_price, $requested_currency);
        //         ////////// for discount price    /////////
        //         if (!empty($products_data->discount_price)) {
        //             $discount_price = Product::convertprice($products_data->discount_price, $requested_currency);
        //             $products_data->discount_price = $discount_price;
        //         }

        //         $products_data->products_price = $products_price;
        //         $getName=  DB::table('users')->where('id', $products_data->admin_id)->first()->shop_name;
        //         $products_data->shop_name=$getName;
        //         $products_data->currency = $requested_currency;
        //         //check currency end

        //         //for flashsale currency price start
        //         if ($type == "flashsale") {
        //             $current_price = $products_data->flash_price;
        //             $flash_price = Product::convertprice($current_price, $requested_currency);
        //             $products_data->flash_price = $flash_price;
        //         }

        //         //for flashsale currency price end
        //         $products_id = $products_data->products_id;

        //         //multiple images
        //         $products_images = DB::table('products_images')
        //             ->LeftJoin('image_categories', function ($join) {
        //                 $join->on('image_categories.image_id', '=', 'products_images.image')
        //                     ->where(function ($query) {
        //                         $query->where('image_categories.image_type', '=', 'THUMBNAIL')
        //                             ->where('image_categories.image_type', '!=', 'THUMBNAIL')
        //                             ->orWhere('image_categories.image_type', '=', 'ACTUAL');
        //                     });
        //             })
        //             ->select('products_images.*', 'image_categories.path as image')
        //             ->where('products_id', '=', $products_id)->orderBy('sort_order', 'ASC')->get();
        //         $products_data->images = $products_images;

        //         $categories = DB::table('products_to_categories')
        //             ->leftjoin('categories', 'categories.categories_id', 'products_to_categories.categories_id')
        //             ->leftjoin('categories_description', 'categories_description.categories_id', 'products_to_categories.categories_id')
        //             ->select('categories.categories_id', 'categories_description.categories_name', 'categories.categories_image', 'categories.categories_icon', 'categories.parent_id')
        //             ->where('products_id', '=', $products_id)
        //             ->where('categories_description.language_id', '=', $language_id)->get();

        //         $products_data->categories = $categories;

        //         $reviews = DB::table('reviews')
        //             ->join('users', 'users.id', '=', 'reviews.customers_id')
        //             ->select('reviews.*', 'users.avatar as image')
        //             ->where('products_id', $products_data->products_id)
        //             ->where('reviews_status', '1')
        //             ->get();

        //         $avarage_rate = 0;
        //         $total_user_rated = 0;

        //         if (count($reviews) > 0) {

        //             $five_star = 0;
        //             $five_count = 0;

        //             $four_star = 0;
        //             $four_count = 0;

        //             $three_star = 0;
        //             $three_count = 0;

        //             $two_star = 0;
        //             $two_count = 0;

        //             $one_star = 0;
        //             $one_count = 0;

        //             foreach ($reviews as $review) {

        //                 //five star ratting
        //                 if ($review->reviews_rating == '5') {
        //                     $five_star += $review->reviews_rating;
        //                     $five_count++;
        //                 }

        //                 //four star ratting
        //                 if ($review->reviews_rating == '4') {
        //                     $four_star += $review->reviews_rating;
        //                     $four_count++;
        //                 }
        //                 //three star ratting
        //                 if ($review->reviews_rating == '3') {
        //                     $three_star += $review->reviews_rating;
        //                     $three_count++;
        //                 }
        //                 //two star ratting
        //                 if ($review->reviews_rating == '2') {
        //                     $two_star += $review->reviews_rating;
        //                     $two_count++;
        //                 }

        //                 //one star ratting
        //                 if ($review->reviews_rating == '1') {
        //                     $one_star += $review->reviews_rating;
        //                     $one_count++;
        //                 }

        //             }

        //             $five_ratio = round($five_count / count($reviews) * 100);
        //             $four_ratio = round($four_count / count($reviews) * 100);
        //             $three_ratio = round($three_count / count($reviews) * 100);
        //             $two_ratio = round($two_count / count($reviews) * 100);
        //             $one_ratio = round($one_count / count($reviews) * 100);
        //             if(($five_star + $four_star + $three_star + $two_star + $one_star) > 0){
        //                 $avarage_rate = (5 * $five_star + 4 * $four_star + 3 * $three_star + 2 * $two_star + 1 * $one_star) / ($five_star + $four_star + $three_star + $two_star + $one_star);
        //             }else{
        //                 $avarage_rate = 0;
        //             }

        //             $total_user_rated = count($reviews);
        //             $reviewed_customers = $reviews;
        //         } else {
        //             $reviewed_customers = array();
        //             $avarage_rate = 0;
        //             $total_user_rated = 0;

        //             $five_ratio = 0;
        //             $four_ratio = 0;
        //             $three_ratio = 0;
        //             $two_ratio = 0;
        //             $one_ratio = 0;
        //         }

        //         $products_data->rating = number_format($avarage_rate, 2);
        //         $products_data->total_user_rated = $total_user_rated;

        //         $products_data->five_ratio = $five_ratio;
        //         $products_data->four_ratio = $four_ratio;
        //         $products_data->three_ratio = $three_ratio;
        //         $products_data->two_ratio = $two_ratio;
        //         $products_data->one_ratio = $one_ratio;

        //         //review by users
        //         $products_data->reviewed_customers = $reviewed_customers;

        //         array_push($result, $products_data);
        //         $options = array();
        //         $attr = array();

        //         $stocks = 0;
        //         $stockOut = 0;
        //         $defaultStock = 0;
        //         if ($products_data->products_type == '0') {
        //             $stocks = DB::table('inventory')->where('products_id', $products_data->products_id)->where('stock_type', 'in')->sum('stock');
        //             $stockOut = DB::table('inventory')->where('products_id', $products_data->products_id)->where('stock_type', 'out')->sum('stock');
        //             $defaultStock = $stocks - $stockOut;
        //         }

        //         if ($products_data->products_max_stock < $defaultStock && $products_data->products_max_stock > 0) {
        //             $result[$index]->defaultStock = $products_data->products_max_stock;
        //         } else {
        //             $result[$index]->defaultStock = $defaultStock;
        //         }

        //         //like product
        //         if (auth()->check()) {
        //             // dd(auth()->user()->id);
        //             $liked_customers_id = auth()->user()->id;
        //             $categories = DB::table('liked_products')->where('liked_products_id', '=', $products_id)->where('liked_customers_id', '=', $liked_customers_id)->get();
        //             if (count($categories) > 0) {
        //                 $result[$index]->isLiked = '1';
        //             } else {
        //                 $result[$index]->isLiked = '0';
        //             }
        //         } else {
        //             $result[$index]->isLiked = '0';
        //         }

        //         // // fetch all options add join from products_options table for option name
        //         // $products_attribute = DB::table('products_attributes')->where('products_id', '=', $products_id)->groupBy('options_id')->get();
        //         // if (count($products_attribute) > 0) {
        //         //     $index2 = 0;
        //         //     foreach ($products_attribute as $attribute_data) {
        //         //         $option_name = DB::table('products_options')
        //         //             ->leftJoin('products_options_descriptions', 'products_options_descriptions.products_options_id', '=', 'products_options.products_options_id')->select('products_options.products_options_id', 'products_options_descriptions.options_name as products_options_name', 'products_options_descriptions.language_id')->where('language_id', '=', $language_id)->where('products_options.products_options_id', '=', $attribute_data->options_id)->get();
        //         //         if (count($option_name) > 0) {
        //         //             $temp = array();
        //         //             $temp_option['id'] = $attribute_data->options_id;
        //         //             $temp_option['name'] = $option_name[0]->products_options_name;
        //         //             $attr[$index2]['option'] = $temp_option;

        //         //             // fetch all attributes add join from products_options_values table for option value name
        //         //             $attributes_value_query = DB::table('products_attributes')->where('products_id', '=', $products_id)->where('options_id', '=', $attribute_data->options_id)->get();
        //         //             foreach ($attributes_value_query as $products_option_value) {

        //         //                 //$option_value = DB::table('products_options_values')->leftJoin('products_options_values_descriptions','products_options_values_descriptions.products_options_values_id','=','products_options_values.products_options_values_id')->select('products_options_values.products_options_values_id', 'products_options_values_descriptions.options_values_name as products_options_values_name' )->where('products_options_values_descriptions.language_id','=', $language_id)->where('products_options_values.products_options_values_id','=', $products_option_value->options_values_id)->get();
        //         //                 $option_value = DB::table('products_options_values')->where('products_options_values_id', '=', $products_option_value->options_values_id)->get();

        //         //                 $attributes = DB::table('products_attributes')->where([['products_id', '=', $products_id], ['options_id', '=', $attribute_data->options_id], ['options_values_id', '=', $products_option_value->options_values_id]])->get();
        //         //                 $temp_i['products_attributes_id'] = $attributes[0]->products_attributes_id;
        //         //                 $temp_i['id'] = $products_option_value->options_values_id;

        //         //                 if (!empty($option_value[0]->products_options_values_name)) {
        //         //                     $temp_i['value'] = $option_value[0]->products_options_values_name;
        //         //                 } else {
        //         //                     $temp_i['value'] = '';
        //         //                 }

        //         //                 //check currency start
        //         //                 $current_price = $products_option_value->options_values_price;

        //         //                 $attribute_price = Product::convertprice($current_price, $requested_currency);

        //         //                 //check currency end

        //         //                 //$temp_i['price'] = $products_option_value->options_values_price;
        //         //                 $temp_i['price'] = $attribute_price;
        //         //                 $temp_i['price_prefix'] = $products_option_value->price_prefix;
        //         //                 array_push($temp, $temp_i);

        //         //             }
        //         //             $attr[$index2]['values'] = $temp;
        //         //             $result[$index]->attributes = $attr;
        //         //             $index2++;
        //         //         }
        //         //     }
        //         // } else {
        //         //     $result[$index]->attributes = array();
        //         // }
        //         $listOfAttributes = array();
        //         $index3 = 0;

        //         // dd($getAllProductsParallel);
        //         $getAllAttributes = DB::table('products_attributes')
        //             ->where('products_id', '=', $products_id)
        //             // ->whereIn('products_id', $getAllProductsParallel)
        //             ->select('options_id', 'options_values_id', 'products_id','options_values_price')
        //             ->get();
        //         // dd($getAllAttributes);

        //         foreach($getAllAttributes as $attribute){
        //             $option_name = DB::table('products_options_descriptions')->where('products_options_id', '=', $attribute->options_id)->where('language_id', '=', $language_id)->first();
        //             $attribute_option_name = $option_name != null ? $option_name->options_name : 'Not Exist';
        //             $option_value = DB::table('products_options_values_descriptions')->where('products_options_values_id', '=', $attribute->options_values_id)->where('language_id', '=', $language_id)->first();
        //             $attribute_option_value = $option_value != null ? $option_value->options_values_name : 'Not Exist';
        //             $listOfAttributes[$index3][$attribute_option_name] =  $attribute_option_value ;


        //         }
        //         $listOfAttributes[$index3]['id'] = $products_id;
        //         $listOfAttributes[$index3]['price'] = $products_data->products_price;
        //         $listOfAttributes[$index3]['home_image'] =asset($products_data->products_image);
        //         $listOfAttributes[$index3]['images'] = $products_images;

        //         $index3++;
        //         // $result['getAllAttributes'] = $getAllAttributes;

        //         $getParallel = DB::table('products')->where('product_parent_id', '=', $products_id)->select('products_id','products_price','products_image')->get();
        //         if($getParallel) {
        //             foreach ($getParallel as $parallel) {
        //                 $getAllAttributesParallel = DB::table('products_attributes')->where('products_id', '=', $parallel->products_id)->select('options_id', 'options_values_id', 'products_id','options_values_price')->get();
        //                 foreach($getAllAttributesParallel as $attribute){
        //                     $option_name = DB::table('products_options_descriptions')->where('products_options_id', '=', $attribute->options_id)->where('language_id', '=', $language_id)->first();
        //                     $attribute_option_name = $option_name != null ? $option_name->options_name : 'Not Exist';
        //                     $option_value = DB::table('products_options_values_descriptions')->where('products_options_values_id', '=', $attribute->options_values_id)->where('language_id', '=', $language_id)->first();
        //                     $attribute_option_value = $option_value != null ? $option_value->options_values_name : 'Not Exist';
        //                     $listOfAttributes[$index3][$attribute_option_name] =  $attribute_option_value ;
        //                     // $listOfAttributes[$index3]['name'][] = $attribute_option_name;
        //                     // $listOfAttributes[$index3]['value'][] = $attribute_option_value;
        //                     // $listOfAttributes[$index3]['price'][] = $attribute->options_values_price;
        //                 }
        //                 $listOfAttributes[$index3]['id'] = $parallel->products_id;
        //                 $listOfAttributes[$index3]['home_image'] =asset($parallel->products_image);
        //                 $listOfAttributes[$index3]['price'] = $parallel->products_price;

        //                 //multiple images
        //                 $products_images = array();
        //                 $products_images = DB::table('products_images')
        //                     ->LeftJoin('image_categories', function ($join) {
        //                         $join->on('image_categories.image_id', '=', 'products_images.image')
        //                             ->where(function ($query) {
        //                                 $query->where('image_categories.image_type', '=', 'THUMBNAIL')
        //                                     ->where('image_categories.image_type', '!=', 'THUMBNAIL')
        //                                     ->orWhere('image_categories.image_type', '=', 'ACTUAL');
        //                             });
        //                     })
        //                     ->select('products_images.*', 'image_categories.path as image')
        //                     ->where('products_id', '=', $parallel->products_id)->orderBy('sort_order', 'ASC')->get();

        //                     // $products_data->images=$products_images;
        //                     $listOfAttributes[$index3]['images'] = $products_images;

        //                 $index3++;
        //             }
        //         }
        //         // dd($listOfAttributes);
        //         $result[$index]->attributes = $listOfAttributes;
        //         // $result[$index]->images2 = $products_images;
        //         $index++;
        //         $responseData[$key]['products']=$result;

        //     }
        // }
        // }
        return response()->json(['data'=>$groups]);
    }
    // likeproduct
    public function likeproduct(Request $request){
        $user = $this->getAuthenticatedUser();
        $liked_products_id = $request->product_id;
        $liked_customers_id = $user->id;
        $date_liked = date('Y-m-d H:i:s');
        //to avoide duplicate record
        DB::table('liked_products')->where([
            'liked_products_id' => $liked_products_id,
            'liked_customers_id' => $liked_customers_id,
        ])->delete();

        DB::table('liked_products')->insert([
            'liked_products_id' => $liked_products_id,
            'liked_customers_id' => $liked_customers_id,
            'date_liked' => $date_liked,
        ]);

        $response = DB::table('liked_products')->select('liked_products_id')->where('liked_customers_id', '=', $liked_customers_id)->get();
        DB::table('products')->where('products_id', '=', $liked_products_id)->increment('products_liked');
        
        $responseData = array('success' => '1', 'product_data' => $response, 'message' => "Product is liked.");
        return response()->json($responseData);
    }

    // likeProduct
    public function unlikeproduct(Request $request){
        $user = $this->getAuthenticatedUser();
        $liked_products_id = $request->product_id;
        $liked_customers_id = $user->id;
        $date_liked = date('Y-m-d H:i:s');

        DB::table('liked_products')->where([
            'liked_products_id' => $liked_products_id,
            'liked_customers_id' => $liked_customers_id,
        ])->delete();

        DB::table('products')->where('products_id', '=', $liked_products_id)->decrement('products_liked');

        $response = DB::table('liked_products')->select('liked_products_id')->where('liked_customers_id', '=', $liked_customers_id)->get();
        $responseData = array('success' => '1', 'product_data' => $response, 'message' => "Product is unliked.");

        return response()->json($responseData);
    }


    public function getfilterproducts(Request $request)
    {
        $products = AppProduct::latest()
        ->with(['stocks','images']);
       
        //filter data
        if($request->search){
            $products->whereHas('descriptions',function($query) use($request){
               return $query->where('products_name','LIKE','%'.$request->search.'%')
                      ->orWhere('products_description','LIKE','%'.$request->search.'%');
            });
            $products->orWhere('barcode','LIKE','%'.$request->search.'%');
        }
           
        if($request->minPrice && $request->maxPrice ){
            $products->whereHas('stocks',function($query) use($request){
               return $query->whereBetween('price', [$request->minPrice, $request->maxPrice]);

            });
        }        
        if(isset($request->filters) && count($request->filters) >0){
            foreach($request->filters as $filter){
                $products->whereHas('stocks',function($query) use($request,$filter){
                    return $query->where('variant', 'LIKE','%'.$filter['value'].'%');
                 });
            }    
        }

        if($request->categories_id){
            $products->whereHas('categories',function($query) use($request){
               return $query->where('products_to_categories.categories_id',$request->categories_id);
            });
        }

        // $responseData = $this->productObject($request,$products);
        return new ProductCollection($products->paginate(10));
    }
    

    public function getproductsbycategory(Request $request)
    {
        $products = AppProduct::latest()
            ->with(['stocks','images']);
        $responseData = $this->productObject($request,$products);
        return new ProductCollection($responseData);
    }

    public function getproductsbybrand(Request $request){
        $products = AppProduct::latest()
            ->with(['stocks','images']);
        $responseData = $this->productObject($request,$products);
        return new ProductCollection($responseData);
    }
    public function getproductbyid(Request $request){
        $products = AppProduct::latest()
        ->with(['stocks','images']);
        $responseData = $this->productObject($request,$products);
        return new ProductCollection($responseData);
    }

    

    public function getproductsyfavourite(Request $request){

        $user = $this->getAuthenticatedUser();

        if($user)
        {
            $products=$user->favoriteProducts;
            $responseData = $this->productObject($request,$products);
            return new ProductCollection($responseData);

        }
        else{
            return response()->json(['message'=>'User Not Exist'],401);
        }
    }
}
