<?php

namespace App\Http\Controllers\AdminControllers;

use App\Http\Controllers\Controller;
use App\Models\AppModels\Product;
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
use App\Product as AppProduct;
use App\ProductStock;
use Auth;
use Illuminate\Support\Facades\Session;

class POSController extends Controller
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
        $result = array();
        $result['commonContent'] = $this->Setting->commonContent();
        $language_id = Session::get('language_id') ? Session::get('language_id') : '2';
        $categories = DB::table('categories')
            ->leftJoin('categories_description','categories_description.categories_id', '=', 'categories.categories_id')
            ->select('categories.categories_id as id', 'categories.categories_image as image',  'categories.created_at as date_added',
            'categories.updated_at as last_modified', 'categories_description.categories_name as name',
            'categories.categories_slug as slug', 'categories.parent_id')
            ->where('categories_description.language_id','=', $language_id )
            ->where('parent_id', '0')
            ->where('categories_status', '1')
            ->get();

        $brands = DB::table('categories')
            ->leftJoin('categories_description','categories_description.categories_id', '=', 'categories.categories_id')
            ->select('categories.categories_id as id', 'categories.categories_image as image',  'categories.created_at as date_added',
            'categories.updated_at as last_modified', 'categories_description.categories_name as name',
            'categories.categories_slug as slug', 'categories.parent_id')
            ->where('categories_description.language_id','=', $language_id )
            ->where('parent_id', '!=', '0')
            ->where('categories_status', '1')
            ->get();
        // $categories = Categories::where('parent_id', 0)->get();
        // $brands = Categories::where('parent_id', '!=', 0)->get();
        $customers = User::all();
        $countries = DB::table('countries')->select('countries_id as id', 'countries_name as name')->get();
        $results = array();
        $results['categories'] = $categories;
        $results['brands'] = $brands;
        $results['customers'] = $customers;
        $results['countries'] = $countries;
        return view("admin.pos.index", compact('results', 'result'));
    }

    public function search(Request $request)
    {
        // if(Auth::user()->user_type == 'admin' || Auth::user()->user_type == 'staff'){
        //     $products = Product::where('added_by', 'admin')->where('published', '1');
        // }
        // else {
        //     $products = Product::where('user_id', Auth::user()->id)->where('published', '1');
        // }

        // if($request->category != null){
        //     $arr = explode('-', $request->category);
        //     if($arr[0] == 'category'){
        //         $category_ids = CategoryUtility::children_ids($arr[1]);
        //         $category_ids[] = $arr[1];
        //         $products = $products->whereIn('category_id', $category_ids);
        //     }
        // }

        // if($request->brand != null){
        //     $products = $products->where('brand_id', $request->brand);
        // }

        // if ($request->keyword != null) {
        //     $products = $products->where('name', 'like', '%'.$request->keyword.'%')->orWhere('barcode', $request->keyword)->orderBy('created_at', 'desc');
        // }

        // $stocks = new PosProductCollection($products->paginate(16));
        // $stocks->appends(['keyword' =>  $request->keyword]);

        if (!empty($request->page)) {
            $page_number = $request->page;
        } else {
            $page_number = 0;
        }

        if (!empty($request->limit)) {
            $limit = $request->limit;
        } else {
            $limit = 10;
        }

        if (!empty($request->type)) {
            $type = $request->type;
        } else {
            $type = '';
        }

        //min_max_price
        if (!empty($request->price)) {
            $d = explode(";", $request->price);
            $min_price = $d[0];
            $max_price = $d[1];
        } else {
            $min_price = '';
            $max_price = '';
        }
        $exist_category = '1';
        $categories_status = 1;
        //category
        if (!empty($request->category) and $request->category != 'all') {
            if(!empty($request->brand) and $request->brand != 'all') {
                $brand = $this->getBrands($request);

                if(!empty($brand) and count($brand)>0){
                    $categories_id = $brand[0]->categories_id;
                    //for main
                    if ($brand[0]->parent_id > 0) {
                        $category_name = $brand[0]->categories_name;
                        $sub_category_name = '';
                        $category_slug = '';
                        $categories_status = $brand[0]->categories_status;
                    }
                }else{
                    $categories_id = '';
                    $category_name = '';
                    $sub_category_name = '';
                    $category_slug = '';
                    $categories_status = 0;
                }
            } else {
                $category = $this->getCategories($request);

                if(!empty($category) and count($category)>0){
                    $categories_id = $category[0]->categories_id;
                    //for main
                    if ($category[0]->parent_id == 0) {
                        $category_name = $category[0]->categories_name;
                        $sub_category_name = '';
                        $category_slug = '';
                        $categories_status = $category[0]->categories_status;
                    } else {
                        //for sub
                        $main_category = $this->products->getMainCategories($category[0]->parent_id);

                        $category_slug = $main_category[0]->categories_slug;
                        $category_name = $main_category[0]->categories_name;
                        $sub_category_name = $category[0]->categories_name;
                        $categories_status = $category[0]->categories_status;
                    }
                }else{
                    $categories_id = '';
                    $category_name = '';
                    $sub_category_name = '';
                    $category_slug = '';
                    $categories_status = 0;
                }
            }
        } else if(!empty($request->brand) and $request->brand != 'all') {
            $brand = $this->getBrands($request);

            if(!empty($brand) and count($brand)>0){
                $categories_id = $brand[0]->categories_id;
                //for main
                if ($brand[0]->parent_id > 0) {
                    $category_name = $brand[0]->categories_name;
                    $sub_category_name = '';
                    $category_slug = '';
                    $categories_status = $brand[0]->categories_status;
                }
            }else{
                $categories_id = '';
                $category_name = '';
                $sub_category_name = '';
                $category_slug = '';
                $categories_status = 0;
            }
        } else {
            $categories_id = '';
            $category_name = '';
            $sub_category_name = '';
            $category_slug = '';
            $categories_status = 1;
        }

        $result['category_name'] = $category_name;
        $result['category_slug'] = $category_slug;
        $result['sub_category_name'] = $sub_category_name;
        $result['categories_status'] = $categories_status;

        //search value
        if (!empty($request->keyword)) {
            $search = $request->keyword;
        } else {
            $search = '';
        }

        $filters = array();

        $data = array('page_number' => $page_number, 'type' => $type, 'limit' => $limit,
            'categories_id' => $categories_id, 'search' => $search,
            'filters' => $filters, 'limit' => $limit, 'min_price' => $min_price, 'max_price' => $max_price);

            $request->merge([
                'language_id' => 2,
            ]);
        $product_id=Product::where('products_id',$search)->first()->products_id??0;
        // $products = $this->products($data);
        $products = Product::with('descriptions')->with('categories');
        // $products=Product::get();
        if(auth()->user()->role_id != 1) {
            $products->where('admin_id', '=', auth()->user()->id);
        } 
        if (isset($search) and !empty($search)) {
            $product_stock=ProductStock::where('sku',$search)->first();
            $products->where(function($q) use($search){
                $q->where('products_slug', 'like', '%' . $search . '%');
                $q->orWhere('products_id', '=',  $search );
                $q->orWhere('products.barcode', 'like', '%' . $search . '%');
            
            });
            
        }

        // $products->whereHas('descriptions',function($q){
        //     $q->where('descriptions.language_id',2);
        // });

        if (isset($categories_id) and !empty($categories_id)) {
            $products->whereHas('categories',function($q) use($categories_id){
                $q->where('categories.categories_id',$categories_id);
            });
            
        }

        

        $result['products'] = $products->paginate(10);

        $result['products_id'] = $product_id;
        $result['product_stock'] = $product_stock??null;

        if ($limit > $result['products']['total_record']) {
            $result['limit'] = $result['products']['total_record'];
        } else {
            $result['limit'] = $limit;
        }

        return response()->json($result);
    }

    public function products($data)
    {
        $language_id = 2;
        if (empty($data['page_number']) or $data['page_number'] == 0) {
            $skip = $data['page_number'] . '0';
        } else {
            $skip = $data['limit'] * $data['page_number'];
        }

        $min_price = $data['min_price'];
        $max_price = $data['max_price'];
        $take = $data['limit'];
        $currentDate = time();
        $type = $data['type'];

        if ($type == "atoz") {
            $sortby = "products_name";
            $order = "ASC";
        } elseif ($type == "ztoa") {
            $sortby = "products_name";
            $order = "DESC";
        } elseif ($type == "hightolow") {
            $sortby = "products_price";
            $order = "DESC";
        } elseif ($type == "lowtohigh") {
            $sortby = "products_price";
            $order = "ASC";
        } elseif ($type == "topseller") {
            $sortby = "products_ordered";
            $order = "DESC";
        } elseif ($type == "mostliked") {
            $sortby = "products_liked";
            $order = "DESC";

        } elseif ($type == "special") {
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

        $categories = DB::table('products')
            ->leftJoin('manufacturers', 'manufacturers.manufacturers_id', '=', 'products.manufacturers_id')
            ->leftJoin('manufacturers_info', 'manufacturers.manufacturers_id', '=', 'manufacturers_info.manufacturers_id')
            ->leftJoin('products_description', 'products_description.products_id', '=', 'products.products_id')
            ->LeftJoin('image_categories', 'products.products_image', '=', 'image_categories.image_id');
            // ->LeftJoin('inventory', 'inventory.products_id', '=', 'products.products_id');

            if (!empty($data['categories_id'])) {
            $categories->LeftJoin('products_to_categories', 'products.products_id', '=', 'products_to_categories.products_id')
                ->leftJoin('categories', 'categories.categories_id', '=', 'products_to_categories.categories_id')
                ->LeftJoin('categories_description', 'categories_description.categories_id', '=', 'products_to_categories.categories_id');
        }

        if (!empty($data['filters']) and empty($data['search'])) {
            $categories->leftJoin('products_attributes', 'products_attributes.products_id', '=', 'products.products_id');
        }

        if (!empty($data['search'])) {
            $categories->leftJoin('products_attributes', 'products_attributes.products_id', '=', 'products.products_id')
                ->leftJoin('products_options', 'products_options.products_options_id', '=', 'products_attributes.options_id')
                ->leftJoin('products_options_values', 'products_options_values.products_options_values_id', '=', 'products_attributes.options_values_id');
        }
        //wishlist customer id
        if ($type == "wishlist") {
            $categories->LeftJoin('liked_products', 'liked_products.liked_products_id', '=', 'products.products_id')
                ->select('products.*', 'image_categories.path as image_path', 'products_description.*', 'manufacturers.*', 'manufacturers_info.manufacturers_url');

        }
        //parameter special
        elseif ($type == "special") {
            $categories->LeftJoin('specials', 'specials.products_id', '=', 'products.products_id')
                ->select('products.*', 'image_categories.path as image_path', 'products_description.*', 'manufacturers.*', 'manufacturers_info.manufacturers_url', 'specials.specials_new_products_price as discount_price', 'specials.specials_new_products_price as discount_price');
        } elseif ($type == "flashsale") {
            //flash sale
            $categories->LeftJoin('flash_sale', 'flash_sale.products_id', '=', 'products.products_id')
                ->select(DB::raw(time() . ' as server_time'), 'products.*', 'image_categories.path as image_path', 'products_description.*', 'manufacturers.*', 'manufacturers_info.manufacturers_url', 'flash_sale.flash_start_date', 'flash_sale.flash_expires_date', 'flash_sale.flash_sale_products_price as flash_price');

        } elseif ($type == "compare") {
            //flash sale
            $categories->LeftJoin('flash_sale', 'flash_sale.products_id', '=', 'products.products_id')
                ->select(DB::raw(time() . ' as server_time'), 'products.*', 'image_categories.path as image_path', 'products_description.*', 'manufacturers.*', 'manufacturers_info.manufacturers_url', 'flash_sale.flash_start_date', 'flash_sale.flash_expires_date', 'flash_sale.flash_sale_products_price as discount_price');

        } else {
            $categories->LeftJoin('specials', function ($join) use ($currentDate) {
                $join->on('specials.products_id', '=', 'products.products_id')
                ->where('status', '=', '1')
                ->where('expires_date', '>', $currentDate);
            })->select('products.*', 'image_categories.path as image_path', 'products_description.*', 'manufacturers.*', 'manufacturers_info.manufacturers_url', 'specials.specials_new_products_price as discount_price');
        }

        if ($type == "special") { //deals special products
            $categories->where('specials.status', '=', '1')->where('expires_date', '>', $currentDate);
        }

        if ($type == "flashsale") { //flashsale
            $categories->where('flash_sale.flash_status', '=', '1')->where('flash_expires_date', '>', $currentDate);

        } elseif ($type != "compare") {
            $categories->whereNotIn('products.products_id', function ($query) use ($currentDate) {
                $query->select('flash_sale.products_id')->from('flash_sale')->where('flash_sale.flash_status', '=', '1');
            });

        }


        //get single products
        if (!empty($data['products_id']) && $data['products_id'] != "") {
            $categories->where('products.products_id', '=', $data['products_id']);
        }

        //for min and maximum price
        if (!empty($max_price)) {

            if (!empty($max_price)) {
                //check session contain default currency
                $current_currency = DB::table('currencies')->where('id', session('currency_id'))->first();
                if($current_currency->is_default == 0){
                    $max_price = $max_price / session('currency_value');
                    $min_price = $min_price / session('currency_value');
                }
            }
            $categories->whereBetween('products.products_price', [$min_price, $max_price]);
        }

        if (!empty($data['search'])) {

            $searchValue = $data['search'];

            // $categories->where('products_name', 'LIKE', '%' . $searchValue . '%')->where('products_status', '=', 1);

            $categories->where('products_options.products_options_name', 'LIKE', '%' . $searchValue . '%')->where('products_status', '=', 1);

            if (!empty($data['categories_id'])) {
                $categories->where('products_to_categories.categories_id', '=', $data['categories_id']);
            }

            if (!empty($data['filters'])) {
                $temp_key = 0;
                foreach ($data['filters']['filter_attribute']['option_values'] as $option_id_temp) {

                    if ($temp_key == 0) {

                        $categories->whereIn('products_attributes.options_id', [$data['filters']['options']])
                            ->where('products_attributes.options_values_id', $option_id_temp);
                        if (count($data['filters']['filter_attribute']['options']) > 1) {

                            $categories->where(DB::raw('(select count(*) from `products_attributes` where `products_attributes`.`products_id` = `products`.`products_id` and `products_attributes`.`options_id` in (' . $data['filters']['options'] . ') and `products_attributes`.`options_values_id` in (' . $data['filters']['option_value'] . '))'), '>=', $data['filters']['options_count']);
                        }

                    } else {
                        $categories->orwhereIn('products_attributes.options_id', [$data['filters']['options']])
                            ->where('products_attributes.options_values_id', $option_id_temp);

                        if (count($data['filters']['filter_attribute']['options']) > 1) {
                            $categories->where(DB::raw('(select count(*) from `products_attributes` where `products_attributes`.`products_id` = `products`.`products_id` and `products_attributes`.`options_id` in (' . $data['filters']['options'] . ') and `products_attributes`.`options_values_id` in (' . $data['filters']['option_value'] . '))'), '>=', $data['filters']['options_count']);
                        }

                    }
                    $temp_key++;
                }

            }

            if (!empty($max_price)) {
                $categories->whereBetween('products.products_price', [$min_price, $max_price]);
            }
            $categories->whereNotIn('products.products_id', function ($query) use ($currentDate) {
                $query->select('flash_sale.products_id')->from('flash_sale')->where('flash_sale.flash_status', '=', '1');
            });


            $categories->orWhere('products_options_values.products_options_values_name', 'LIKE', '%' . $searchValue . '%')->where('products_status', '=', 1);
            if (!empty($data['categories_id'])) {
                $categories->where('products_to_categories.categories_id', '=', $data['categories_id']);
            }

            if (!empty($data['filters'])) {
                $temp_key = 0;
                foreach ($data['filters']['filter_attribute']['option_values'] as $option_id_temp) {

                    if ($temp_key == 0) {

                        $categories->whereIn('products_attributes.options_id', [$data['filters']['options']])
                            ->where('products_attributes.options_values_id', $option_id_temp);
                        if (count($data['filters']['filter_attribute']['options']) > 1) {

                            $categories->where(DB::raw('(select count(*) from `products_attributes` where `products_attributes`.`products_id` = `products`.`products_id` and `products_attributes`.`options_id` in (' . $data['filters']['options'] . ') and `products_attributes`.`options_values_id` in (' . $data['filters']['option_value'] . '))'), '>=', $data['filters']['options_count']);
                        }

                    } else {
                        $categories->orwhereIn('products_attributes.options_id', [$data['filters']['options']])
                            ->where('products_attributes.options_values_id', $option_id_temp);

                        if (count($data['filters']['filter_attribute']['options']) > 1) {
                            $categories->where(DB::raw('(select count(*) from `products_attributes` where `products_attributes`.`products_id` = `products`.`products_id` and `products_attributes`.`options_id` in (' . $data['filters']['options'] . ') and `products_attributes`.`options_values_id` in (' . $data['filters']['option_value'] . '))'), '>=', $data['filters']['options_count']);
                        }

                    }
                    $temp_key++;
                }

            }

            if (!empty($max_price)) {
                $categories->whereBetween('products.products_price', [$min_price, $max_price]);
            }

            $categories->whereNotIn('products.products_id', function ($query) use ($currentDate) {
                $query->select('flash_sale.products_id')->from('flash_sale')->where('flash_sale.flash_status', '=', '1');
            });

            $categories->orWhere('products_name', 'LIKE', '%' . $searchValue . '%')->where('products_status', '=', 1);

            if (!empty($data['categories_id'])) {
                $categories->where('products_to_categories.categories_id', '=', $data['categories_id']);
            }

            if (!empty($data['filters'])) {
                $temp_key = 0;
                foreach ($data['filters']['filter_attribute']['option_values'] as $option_id_temp) {

                    if ($temp_key == 0) {

                        $categories->whereIn('products_attributes.options_id', [$data['filters']['options']])
                            ->where('products_attributes.options_values_id', $option_id_temp);
                        if (count($data['filters']['filter_attribute']['options']) > 1) {

                            $categories->where(DB::raw('(select count(*) from `products_attributes` where `products_attributes`.`products_id` = `products`.`products_id` and `products_attributes`.`options_id` in (' . $data['filters']['options'] . ') and `products_attributes`.`options_values_id` in (' . $data['filters']['option_value'] . '))'), '>=', $data['filters']['options_count']);
                        }

                    } else {
                        $categories->orwhereIn('products_attributes.options_id', [$data['filters']['options']])
                            ->where('products_attributes.options_values_id', $option_id_temp);

                        if (count($data['filters']['filter_attribute']['options']) > 1) {
                            $categories->where(DB::raw('(select count(*) from `products_attributes` where `products_attributes`.`products_id` = `products`.`products_id` and `products_attributes`.`options_id` in (' . $data['filters']['options'] . ') and `products_attributes`.`options_values_id` in (' . $data['filters']['option_value'] . '))'), '>=', $data['filters']['options_count']);
                        }

                    }
                    $temp_key++;
                }

            }

            if (!empty($max_price)) {
                $categories->whereBetween('products.products_price', [$min_price, $max_price]);
            }

            $categories->whereNotIn('products.products_id', function ($query) use ($currentDate) {
                $query->select('flash_sale.products_id')->from('flash_sale')->where('flash_sale.flash_status', '=', '1');
            });

            $categories->orWhere('products_model', 'LIKE', '%' . $searchValue . '%')->where('products_status', '=', 1);
            $categories->orWhere('products.barcode', 'LIKE', '%' . $searchValue . '%')->where('products_status', '=', 1);

            if (!empty($data['categories_id'])) {
                $categories->where('products_to_categories.categories_id', '=', $data['categories_id']);
            }

            if (!empty($data['filters'])) {
                $temp_key = 0;
                foreach ($data['filters']['filter_attribute']['option_values'] as $option_id_temp) {

                    if ($temp_key == 0) {

                        $categories->whereIn('products_attributes.options_id', [$data['filters']['options']])
                            ->where('products_attributes.options_values_id', $option_id_temp);
                        if (count($data['filters']['filter_attribute']['options']) > 1) {

                            $categories->where(DB::raw('(select count(*) from `products_attributes` where `products_attributes`.`products_id` = `products`.`products_id` and `products_attributes`.`options_id` in (' . $data['filters']['options'] . ') and `products_attributes`.`options_values_id` in (' . $data['filters']['option_value'] . '))'), '>=', $data['filters']['options_count']);
                        }

                    } else {
                        $categories->orwhereIn('products_attributes.options_id', [$data['filters']['options']])
                            ->where('products_attributes.options_values_id', $option_id_temp);

                        if (count($data['filters']['filter_attribute']['options']) > 1) {
                            $categories->where(DB::raw('(select count(*) from `products_attributes` where `products_attributes`.`products_id` = `products`.`products_id` and `products_attributes`.`options_id` in (' . $data['filters']['options'] . ') and `products_attributes`.`options_values_id` in (' . $data['filters']['option_value'] . '))'), '>=', $data['filters']['options_count']);
                        }

                    }
                    $temp_key++;
                }

            }
            $categories->whereNotIn('products.products_id', function ($query) use ($currentDate) {
                $query->select('flash_sale.products_id')->from('flash_sale')->where('flash_sale.flash_status', '=', '1');
            });
        }

        if (!empty($data['filters'])) {
            $temp_key = 0;
            foreach ($data['filters']['filter_attribute']['option_values'] as $option_id_temp) {

                if ($temp_key == 0) {

                    $categories->whereIn('products_attributes.options_id', [$data['filters']['options']])
                        ->where('products_attributes.options_values_id', $option_id_temp);
                    if (count($data['filters']['filter_attribute']['options']) > 1) {

                        $categories->where(DB::raw('(select count(*) from `products_attributes` where `products_attributes`.`products_id` = `products`.`products_id` and `products_attributes`.`options_id` in (' . $data['filters']['options'] . ') and `products_attributes`.`options_values_id` in (' . $data['filters']['option_value'] . '))'), '>=', $data['filters']['options_count']);
                    }

                } else {
                    $categories->orwhereIn('products_attributes.options_id', [$data['filters']['options']])
                        ->where('products_attributes.options_values_id', $option_id_temp);

                    if (count($data['filters']['filter_attribute']['options']) > 1) {
                        $categories->where(DB::raw('(select count(*) from `products_attributes` where `products_attributes`.`products_id` = `products`.`products_id` and `products_attributes`.`options_id` in (' . $data['filters']['options'] . ') and `products_attributes`.`options_values_id` in (' . $data['filters']['option_value'] . '))'), '>=', $data['filters']['options_count']);
                    }

                }
                $temp_key++;
            }

        }

        //wishlist customer id
        if ($type == "wishlist") {
            $categories->where('liked_customers_id', '=', session('customers_id'));
        }

        //wishlist customer id
        if ($type == "is_feature") {
            $categories->where('products.is_feature', '=', 1);
        }

        $categories->where('products_description.language_id', '=', $language_id)->where('products_status', '=', 1);

        //get single category products
        if (!empty($data['categories_id'])) {
            $categories->where('products_to_categories.categories_id', '=', $data['categories_id']);
            $categories->where('categories.categories_status', '=', 1);
            $categories->where('categories_description.language_id', '=', $language_id);
        }else{
            $categories->LeftJoin('products_to_categories', 'products.products_id', '=', 'products_to_categories.products_id');
            $categories->whereIn('products_to_categories.categories_id', function ($query) use ($currentDate) {
                $query->select('categories_id')->from('categories')->where('categories.categories_status',1);
            });
        }

        if ($type == "topseller") {
            $categories->where('products.products_ordered', '>', 0);
        }
        if ($type == "mostliked") {
            $categories->where('products.products_liked', '>', 0);
        }

        if(auth()->user()->role_id == 11) {
            $categories->where('products.admin_id', '=', auth()->user()->id);
        } elseif(auth()->user()->role_id == 12) {
            $categories->where('products.admin_id', '=', auth()->user()->parent_admin_id);
        }

        $product_array = array();
        $inventories = DB::table('inventory')->get();
        foreach($inventories as $inventory){
            $current_stock_in = DB::table('inventory')->where('products_id', $inventory->products_id)->where('stock_type', 'in')->sum('stock');
            $current_stock_out = DB::table('inventory')->where('products_id', $inventory->products_id)->where('stock_type', 'out')->sum('stock');
            $current_stock = $current_stock_in - $current_stock_out;
            if($current_stock > 0) {
                $product_array[] = $inventory->products_id;
                // array_push($product_array, $inventory->products_id);
            }
        }

        $getProductsIds = array_values(array_unique($product_array));
        // $getProductsIds = $product_array;

        // dd($getProductsIds);
        // $users_idss =  [1];
        $categories->whereIn('products.products_id', $getProductsIds);

        // $categories->where(function($q) use ($getProductsIds) {
        //     foreach($getProductsIds as $key => $value)
        //     {
        //         // $q->where($key, '=', $value);
        //         $q->where('products.products_id', '=', $value);
        //     }
        // });

        $categories->orderBy($sortby, $order)->groupBy('products.products_id');
        $categories->where('products.is_show_admin', '=', '1');

        //count
        $total_record = $categories->get();
        $products = $categories->skip($skip)->take($take)->get();
        $paginate = $categories->skip($skip)->paginate($take);
        // dd($skip, $take);

        $result = array();
        $result2 = array();

        //check if record exist
        if (count($products) > 0) {

            $index = 0;
            foreach ($products as $products_data) {

                $reviews = DB::table('reviews')
                    ->leftjoin('users', 'users.id', '=', 'reviews.customers_id')
                    ->leftjoin('reviews_description', 'reviews.reviews_id', '=', 'reviews_description.review_id')
                    ->select('reviews.*', 'reviews_description.reviews_text')
                    ->where('products_id', $products_data->products_id)
                    ->where('reviews_status', '1')
                    ->where('reviews_read', '1')
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
                $products_id = $products_data->products_id;

                //products_image
                $default_images = DB::table('image_categories')
                    ->where('image_id', '=', $products_data->products_image)
                    ->where('image_type', 'MEDIUM')
                    ->first();

                if ($default_images) {
                    $products_data->image_path = $default_images->path;
                } else {
                    $default_images = DB::table('image_categories')
                        ->where('image_id', '=', $products_data->products_image)
                        ->where('image_type', 'LARGE')
                        ->first();

                    if ($default_images) {
                        $products_data->image_path = $default_images->path;
                    } else {
                        $default_images = DB::table('image_categories')
                            ->where('image_id', '=', $products_data->products_image)
                            ->where('image_type', 'ACTUAL')
                            ->first();
                        $products_data->image_path = $default_images->path;
                    }

                }

                $default_images = DB::table('image_categories')
                    ->where('image_id', '=', $products_data->products_image)
                    ->where('image_type', 'LARGE')
                    ->first();
                if ($default_images) {
                    $products_data->default_images = $default_images->path;
                } else {
                    $default_images = DB::table('image_categories')
                        ->where('image_type', 'ACTUAL')
                        ->where('image_id', '=', $products_data->products_image)
                        ->first();
                    if ($default_images) {
                        $products_data->default_images = $default_images->path;
                    }
                }

                //multiple images
                $products_images = DB::table('products_images')
                    ->LeftJoin('image_categories', 'products_images.image', '=', 'image_categories.image_id')
                    ->select('image_categories.path as image_path', 'image_categories.image_type')
                    ->where('products_id', '=', $products_id)
                    ->orderBy('sort_order', 'ASC')
                    ->get();

                $products_data->images = $products_images;

                $default_image_thumb = DB::table('products')
                    ->LeftJoin('image_categories', 'products.products_image', '=', 'image_categories.image_id')
                    ->select('image_categories.path as image_path', 'image_categories.image_type')
                    ->where('products_id', '=', $products_id)
                    ->where('image_type', '=', 'THUMBNAIL')
                    ->first();
                if ($default_image_thumb) {
                    $products_data->default_thumb = $default_image_thumb->image_path;
                } else {
                    $products_data->default_thumb = $products_data->default_images;
                }

                //categories
                $categories = DB::table('products_to_categories')
                    ->leftjoin('categories', 'categories.categories_id', 'products_to_categories.categories_id')
                    ->leftjoin('categories_description', 'categories_description.categories_id', 'products_to_categories.categories_id')
                    ->select('categories.categories_id', 'categories_description.categories_name', 'categories.categories_image', 'categories.categories_icon', 'categories.parent_id', 'categories.categories_slug', 'categories.categories_status')
                    ->where('products_id', '=', $products_id)
                    ->where('categories_description.language_id', '=', $language_id)
                    ->where('categories.categories_status', 1)
                    ->orderby('parent_id', 'ASC')->get();

                $products_data->categories = $categories;
                array_push($result, $products_data);

                $options = array();
                $attr = array();

                $stocks = 0;
                $stockOut = 0;
                if ($products_data->products_type == '0') {
                    $stocks = DB::table('inventory')->where('products_id', $products_data->products_id)->where('stock_type', 'in')->sum('stock');
                    $stockOut = DB::table('inventory')->where('products_id', $products_data->products_id)->where('stock_type', 'out')->sum('stock');
                }

                $result[$index]->defaultStock = $stocks - $stockOut;

                //like product
                if (!empty(session('customers_id'))) {
                    $liked_customers_id = session('customers_id');
                    $categories = DB::table('liked_products')->where('liked_products_id', '=', $products_id)->where('liked_customers_id', '=', $liked_customers_id)->get();

                    if (count($categories) > 0) {
                        $result[$index]->isLiked = '1';
                    } else {
                        $result[$index]->isLiked = '0';
                    }
                } else {
                    $result[$index]->isLiked = '0';
                }

                // fetch all options add join from products_options table for option name
                $products_attribute = DB::table('products_attributes')->where('products_id', '=', $products_id)->groupBy('options_id')->get();
                if (count($products_attribute)) {
                    $index2 = 0;
                    foreach ($products_attribute as $attribute_data) {

                        $option_name = DB::table('products_options')
                            ->leftJoin('products_options_descriptions', 'products_options_descriptions.products_options_id', '=', 'products_options.products_options_id')->select('products_options.products_options_id', 'products_options_descriptions.options_name as products_options_name', 'products_options_descriptions.language_id')->where('language_id', '=', $language_id)->where('products_options.products_options_id', '=', $attribute_data->options_id)->get();

                        if (count($option_name) > 0) {

                            $temp = array();
                            $temp_option['id'] = $attribute_data->options_id;
                            $temp_option['name'] = $option_name[0]->products_options_name;
                            $temp_option['is_default'] = $attribute_data->is_default;
                            $attr[$index2]['option'] = $temp_option;

                            // fetch all attributes add join from products_options_values table for option value name
                            $attributes_value_query = DB::table('products_attributes')->where('products_id', '=', $products_id)->where('options_id', '=', $attribute_data->options_id)->get();
                            $k = 0;
                            foreach ($attributes_value_query as $products_option_value) {

                                $option_value = DB::table('products_options_values')->leftJoin('products_options_values_descriptions', 'products_options_values_descriptions.products_options_values_id', '=', 'products_options_values.products_options_values_id')->select('products_options_values.products_options_values_id', 'products_options_values_descriptions.options_values_name as products_options_values_name')->where('products_options_values_descriptions.language_id', '=', $language_id)->where('products_options_values.products_options_values_id', '=', $products_option_value->options_values_id)->get();

                                $attributes = DB::table('products_attributes')->where([['products_id', '=', $products_id], ['options_id', '=', $attribute_data->options_id], ['options_values_id', '=', $products_option_value->options_values_id]])->get();

                                $temp_i['products_attributes_id'] = $attributes[0]->products_attributes_id;
                                $temp_i['id'] = $products_option_value->options_values_id;
                                $temp_i['value'] = $option_value[0]->products_options_values_name;
                                $temp_i['price'] = $products_option_value->options_values_price;
                                $temp_i['price_prefix'] = $products_option_value->price_prefix;
                                array_push($temp, $temp_i);

                            }
                            $attr[$index2]['values'] = $temp;
                            $result[$index]->attributes = $attr;
                            $index2++;
                        }
                    }
                } else {
                    $result[$index]->attributes = array();
                }
                $index++;
            }
            $responseData = array(
                'success' => '1',
                'product_data' => $result,
                'message' => Lang::get('website.Returned all products'),
                'total_record' => count($total_record),
                'paginate' => $paginate
            );

        } else {
            $responseData = array(
                'success' => '0',
                'product_data' => $result,
                'message' => Lang::get('website.Empty record'),
                'total_record' => count($total_record),
                'paginate' => $paginate
            );
        }

        return ($responseData);
    }

    public function getCategories($request)
    {
        $category = DB::table('categories')
        ->leftJoin('categories_description', 'categories_description.categories_id', '=', 'categories.categories_id')
        ->where('categories.categories_id', $request->category)->where('language_id', 2)
        ->get();
        return $category;
    }

    public function getBrands($request)
    {
        $category = DB::table('categories')
        ->leftJoin('categories_description', 'categories_description.categories_id', '=', 'categories.categories_id')
        ->where('categories.categories_id', $request->brand)->where('language_id', 2)
        ->get();
        return $category;
    }

    public function getVarinats(Request $request){
        // return $request->id;
        $stocks = AppProduct::where('products_id',$request->id)->first()->stocks;
        // return $stocks;
        if(count($stocks) > 0){
            return view('admin.pos.variants', compact('stocks'));
        }
        else {
            return 0;
        }
    }

    public function addToPOS(Request $request)
    {
        // dd($request->all());
        $products = DB::table('pos_standby')->where('customer_id',$request->customer_id_pos)->get();
        foreach($products as $item){
            $product = AppProduct::where('products_id', $item->product_id)->first();
            // dd($product);
            if(!$product){
                continue;
            }
            $data = array();
            $data['id'] = $product->products_id;
            $tax = 0;
            $data['variant'] = $request->variant;

            if($request->variant != null){
                $product_stock = $product->stocks->where('variant', $request->variant)->first();
                $price = $product_stock->pos_price;
                $quantity = $product_stock->pos_qty;

                if($request['quantity'] > $quantity){
                    return 0;
                }
            }
            else{
                $price = $product->products_price;
            }

            $tax = 0;

            $data['quantity'] = $item->quantity;
            $data['price'] = $price;
            $data['tax'] = 0;
            $data['shipping'] = 0;//$product->shipping_cost;

            if($request->session()->has('posCart')){
                $foundInCart = false;
                $cart = collect();

                foreach ($request->session()->get('posCart') as $key => $cartItem){
                    if($cartItem['id'] == $item->product_id){
                        if($cartItem['variant'] == $request->variant){
                            $foundInCart = true;
                            $product = AppProduct::where('products_id', $cartItem['id'])->first();//\App\Product::find($cartItem['id']);
                            $current_stock_in = DB::table('inventory')->where('products_id', $cartItem['id'])->Where('stock_type', '=', 'in')->sum('stock');
                            $current_stock_out = DB::table('inventory')->where('products_id', $cartItem['id'])->Where('stock_type', '=', 'out')->sum('stock');
                            $current_stock = $current_stock_in - $current_stock_out;
                            if($cartItem['variant'] != null ){
                                $product_stock = $product->stocks->where('variant', $cartItem['variant'])->first();
                                $quantity = $product_stock->pos_qty;
                                if($quantity >= $item->quantity){
                                    if($item->quantity >= $product->min_qty){
                                        $cartItem['quantity'] = $item->quantity;
                                    }
                                }
                            }
                            elseif ($current_stock >= $item->quantity) {
                                // if($item->quantity >= $product->min_qty){
                                    $cartItem['quantity'] = $item->quantity;
                                // }
                            }
                        }
                    }
                    $cart->push($cartItem);
                }

                if (!$foundInCart) {
                    $cart->push($data);
                }
                $request->session()->put('posCart', $cart);
            }
            else{
                $cart = collect([$data]);
                $request->session()->put('posCart', $cart);
            }

        }

        $customer =DB::table('users')->where('id',$request->customer_id_pos) ->first();
        DB::table('pos_standby')->where('customer_id',$request->customer_id_pos)->delete();
        return redirect()->back()->with(['customerData'=>$customer]);
    }

    public function addToCart(Request $request)
    {
        $product = AppProduct::where('products_id', $request->product_id)->first();

        $data = array();
        $data['id'] = $product->products_id;
        $tax = 0;
        $data['variant'] = $request->variant;

        if($request->variant != null ){
            $product_stock = $product->stocks->where('variant', $request->variant)->first();
            $price = $product_stock->pos_price;
            $quantity = $product_stock->pos_qty;

            if($request['quantity'] > $quantity){
                return 0;
            }
        }
        else{
            $price = $product->products_price;
        }

        //discount calculation based on flash deal and regular discount
        //calculation of taxes

        // $flash_deals = \App\FlashDeal::where('status', 1)->get();
        // $inFlashDeal = false;
        // foreach ($flash_deals as $flash_deal) {
        //     if ($flash_deal != null && $flash_deal->status == 1  && strtotime(date('d-m-Y')) >= $flash_deal->start_date && strtotime(date('d-m-Y')) <= $flash_deal->end_date && \App\FlashDealProduct::where('flash_deal_id', $flash_deal->id)->where('product_id', $product->id)->first() != null) {
        //         $flash_deal_product = \App\FlashDealProduct::where('flash_deal_id', $flash_deal->id)->where('product_id', $product->id)->first();
        //         if($flash_deal_product->discount_type == 'percent'){
        //             $price -= ($price*$flash_deal_product->discount)/100;
        //         }
        //         elseif($flash_deal_product->discount_type == 'amount'){
        //             $price -= $flash_deal_product->discount;
        //         }
        //         $inFlashDeal = true;
        //         break;
        //     }
        // }
        // if (!$inFlashDeal) {
        //     if($product->discount_type == 'percent'){
        //         $price -= ($price*$product->discount)/100;
        //     }
        //     elseif($product->discount_type == 'amount'){
        //         $price -= $product->discount;
        //     }
        // }

        // if($product->tax_type == 'percent'){
        //     $tax = ($price*$product->tax)/100;
        // }
        // elseif($product->tax_type == 'amount'){
        //     $tax = $product->tax;
        // }

        $tax = 0;

        $data['quantity'] = $request->quantity;
        $data['price'] = $price;
        $data['tax'] = 0;
        $data['shipping'] = 0;//$product->shipping_cost;

        if($request->session()->has('posCart')){
            $foundInCart = false;
            $cart = collect();

            foreach ($request->session()->get('posCart') as $key => $cartItem){
                if($cartItem['id'] == $request->product_id){
                    if($cartItem['variant'] == $request->variant){
                        $foundInCart = true;
                        $product = AppProduct::where('products_id', $cartItem['id'])->first();//\App\Product::find($cartItem['id']);
                        $current_stock_in = DB::table('inventory')->where('products_id', $cartItem['id'])->Where('stock_type', '=', 'in')->sum('stock');
                        $current_stock_out = DB::table('inventory')->where('products_id', $cartItem['id'])->Where('stock_type', '=', 'out')->sum('stock');
                        $current_stock = $current_stock_in - $current_stock_out;
                        if($cartItem['variant'] != null ){
                            // if($cartItem['variant'] != null && $product->variant_product){
                            $product_stock = $product->stocks->where('variant', $cartItem['variant'])->first();
                            $quantity = $product_stock->pos_qty;
                            if($quantity >= $request->quantity){
                                if($request->quantity >= $product->min_qty){
                                    $cartItem['quantity'] += $request->quantity;
                                }
                            }
                        }
                        elseif ($current_stock >= $request->quantity) {
                            // if($request->quantity >= $product->min_qty){
                                $cartItem['quantity'] += $request->quantity;
                            // }
                        }
                    }
                    else{
                        $cartItem['quantity'] += $request->quantity;
                    }
                    
                }
                $cart->push($cartItem);
            }

            if (!$foundInCart) {
                $cart->push($data);
            }
            $request->session()->put('posCart', $cart);
        }
        else{
            $cart = collect([$data]);
            $request->session()->put('posCart', $cart);
        }

        return view('admin.pos.cart');
    }
    public function addToCartNew(Request $request)
    {

        // dd('ddfdf');
        $data = array();
        $data['id'] = 0;
        $tax = $request->tax ? $request->tax : 0 ;


        $data['quantity'] = $request->ProductQuantity;
        $data['name'] = $request->ProductName;
        $data['price'] = $request->ProductPrice;
        $data['tax'] = $tax;
        $data['shipping'] = 0;//$product->shipping_cost;

        if($request->session()->has('posCartNew')){
            $foundInCart = false;
            $cart = collect();

            foreach ($request->session()->get('posCartNew') as $key => $cartItem){

                $cart->push($cartItem);
            }

            if (!$foundInCart) {
                $cart->push($data);
            }
            $request->session()->put('posCartNew', $cart);
        }
        else{
            $cart = collect([$data]);
            $request->session()->put('posCartNew', $cart);
        }

        return redirect()->back();
    }

    //updated the quantity for a cart item
    public function updateQuantity(Request $request)
    {
        $cart = $request->session()->get('posCart', collect([]));
        $cart = $cart->map(function ($object, $key) use ($request) {
            if($key == $request->key){
                // $product = \App\Product::find($object['id']);
                $product = AppProduct::where('products_id', $object['id'])->first();
                $current_stock_in = DB::table('inventory')->where('products_id', $object['id'])->Where('stock_type', '=', 'in')->sum('stock');
                $current_stock_out = DB::table('inventory')->where('products_id', $object['id'])->Where('stock_type', '=', 'out')->sum('stock');
                $current_stock = $current_stock_in - $current_stock_out;
                if($object['variant'] != null ){
                    $product_stock = $product->stocks->where('variant', $object['variant'])->first();
                    $quantity = $product_stock->pos_qty;
                    if($quantity >= $request->quantity){
                        if($request->quantity >= $product->min_qty){
                            $object['quantity'] = $request->quantity;
                        }
                    }
                }
                elseif ($current_stock >= $request->quantity) {
                    // if($request->quantity >= $product->min_qty){
                        $object['quantity'] = $request->quantity;
                    // }
                }
            }
            return $object;
        });
        $request->session()->put('posCart', $cart);

        return view('admin.pos.cart');
    }

    //updated the quantity for a cart item
    public function updateQuantityNew(Request $request)
    {
        $cart = $request->session()->get('posCartNew', collect([]));
        $cart = $cart->map(function ($object, $key) use ($request) {
            if($key == $request->key){
                $object['quantity'] = $request->quantity;
            }
            return $object;
        });
        $request->session()->put('posCartNew', $cart);

        return view('admin.pos.cart');
    }

    //removes from Cart
    public function removeFromCart(Request $request)
    {
        if(Session::has('posCart')){
            $cart = Session::get('posCart', collect([]));
            $cart->forget($request->key);
            Session::put('posCart', $cart);
        }

        return view('admin.pos.cart');
    }

    //removes from Cart
    public function removeFromCartNew(Request $request)
    {
        if(Session::has('posCartNew')){
            $cart = Session::get('posCartNew', collect([]));
            $cart->forget($request->key);
            Session::put('posCartNew', $cart);
        }

        return view('admin.pos.cart');
    }

    //Shipping Address for admin
    public function getShippingAddress(Request $request){
        // $user_id = $request->id;
        // if($user_id == ''){
        //     return view('pos.guest_shipping_address');
        // }
        // else{
        //     return view('pos.shipping_address', compact('user_id'));
        // }
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

    //set Discount
    public function setDiscount(Request $request){
        if($request->discount >= 0){
            Session::put('pos_discount', $request->discount);
        }
        return view('admin.pos.cart');
    }

    //set Shipping Cost
    public function setShipping(Request $request){
        if($request->shipping != null){
            Session::put('shipping', $request->shipping);
        }
        return view('admin.pos.cart');
    }

    //order place
    public function order_store(Request $request)
    {
        // return $request->all();
        if(Session::has('posCart') && count(Session::get('posCart')) > 0 || Session::has('posCartNew') && count(Session::get('posCartNew')) > 0 ){
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
            // $delivery_latitude = session('shipping_address')->latitude;
            // $delivery_longitude = session('shipping_address')->longitude;

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

            // $data['name']           = $name;
            // $data['email']          = $email;
            // $data['address']        = $address;
            // $data['country']        = $country;
            // $data['city']           = $city;
            // $data['postal_code']    = $postal_code;
            // $data['phone']          = $phone;

            // $order->shipping_address = json_encode($data);

            $payment_method = 'cash_on_delivery';
            $shipping_method = "";//smsaexpress
            $order_information = array();

            //price
            if (!empty(session('shipping_detail'))) {
                $shipping_price = session('shipping_detail')->shipping_price;
            } else {
                $shipping_price = 0;
            }

            $taxes_rate = 0;
            if(Session::has('posCart') && count(Session::get('posCart')) > 0)
            {
                foreach (Session::get('posCart') as $key => $cartItem){
                    $taxes_rate += $cartItem['tax']*$cartItem['quantity'];
                }
            }
            $tax_rate = $taxes_rate;
            // $coupon_discount = number_format((float) session('coupon_discount'), 2, '.', '');
            // $order_price = (session('products_price') + $tax_rate + $shipping_price) - $coupon_discount;
            $order_price = $request->total_price;

            // if($shipping_method == "smsaexpress") {
            //     $passKey = "10001000";
            //     $refno = 'refno1000';
            //     $arrayToSendShip = [
            //         "passkey"       => $passKey,
            //         "refno"         => $refno,
            //         "sentDate"      => time(),
            //         "idNo"          => $customers_id,
            //         "cName"         => $delivery_firstname . ' ' . $delivery_lastname,
            //         "cntry"         => $delivery_country,
            //         "cCity"         => $delivery_city,
            //         "cZip"          => $delivery_postcode,
            //         // "cPOBox"        => "string",
            //         "cMobile"       => $delivery_phone,
            //         // "cTel1"         => "string",
            //         // "cTel2"         => "string",
            //         "cAddr1"        => $delivery_street_address,
            //         // "cAddr2"        => "string",
            //         // "shipType"      => "string",
            //         // "PCs"           => "string",
            //         "cEmail"        => $email,
            //         "carrValue"     => $order_price,
            //         "carrCurr"      => "SAR",
            //         // "codAmt"        => "string",
            //         // "weight"        => "string",
            //         // "itemDesc"      => "string",
            //         // "custVal"       => "string",
            //         // "custCurr"      => "string",
            //         // "insrAmt"       => "string",
            //         // "insrCurr"      => "string",
            //         // "sName"         => "string",
            //         // "sContact"      => "string",
            //         // "sAddr1"        => "string",
            //         // "sAddr2"        => "string",
            //         // "sCity"         => "string",
            //         // "sPhone"        => "string",
            //         // "sCntry"        => "string",
            //         // "prefDelvDate"  => "string",
            //         // "gpsPoints"     => $delivery_latitude . ',' . $delivery_longitude
            //     ];

            //     $json = json_encode($arrayToSendShip);

            //     $curl = curl_init();

            //     curl_setopt_array($curl, array(
            //         CURLOPT_URL => "https://track.smsaexpress.com/SecomRestWebApi/api/addship",
            //         CURLOPT_RETURNTRANSFER => true,
            //         CURLOPT_ENCODING => "",
            //         CURLOPT_MAXREDIRS => 10,
            //         CURLOPT_TIMEOUT => 30,
            //         CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            //         CURLOPT_CUSTOMREQUEST => "POST",
            //         CURLOPT_POSTFIELDS => $json,
            //         CURLOPT_HTTPHEADER => array(
            //             // "authorization: Bearer sk_test_AZ4bmEMR1rqGLzoTShvkwFNK",
            //             "content-type: application/json"
            //         ),
            //     ));

            //     $responseShip = curl_exec($curl);
            //     $err = curl_error($curl);

            //     curl_close($curl);

            //     if ($err) {
            //         // echo "cURL Error #:" . $err;
            //         dd($err);
            //         $shipment_status = 'failed';
            //     } else {
            //         // echo $response;
            //         $resultsResponseShip = json_decode($responseShip);
            //         $shipment_status = 'success';
            //     }

            // }

            $orders_status = '1';
            $code = '';
            $coupon_amount = '';
            $comments = $request->comment;
            $date_added = date('Y-m-d h:i:s');

            $web_setting = DB::table('settings')->get();
            $currency = $web_setting[19]->value;
            // $total_tax = number_format((float) session('tax_rate'), 2, '.', '');
            $products_tax = 0;

            if($request->payment_type == 'cash') {
                $payment_method_name = 'Cash';
            } else {
                $payment_method_name = 'Visa';
            }

            // if($request->first_name && $request->first_name){
            //     $customers_name = $delivery_firstname . ' ' . $delivery_lastname;
            // } else {
            //     $customers_name = null;
            // }

            if ($request->customer_id) {
                $get_detail_customer = DB::table('users')->where('id', $request->customer_id)->first();
                if ($get_detail_customer) {
                    $customers_name         = $get_detail_customer->first_name . ' ' . $get_detail_customer->last_name;
                    $customers_telephone = $get_detail_customer -> phone;
                }
            }else
            {
                $customers_name = 'بدون اسم';
                $customers_telephone = 'بدون هاتف';
            }

            $orders_id = DB::table('orders')->insertGetId([
                'customers_id' => $customers_id,
                'customers_name' => $customers_name,
                'customers_street_address' => $delivery_street_address,
                'customers_suburb' => $delivery_suburb,
                'customers_city' => $delivery_city,
                'customers_postcode' => $delivery_postcode,
                'customers_state' => $delivery_state,
                'customers_country' => $delivery_country,
                'customers_telephone' => $customers_telephone,
                'email' => $email,
                // 'customers_address_format_id' => $delivery_address_format_id,

                'delivery_name' => $customers_name,
                'delivery_street_address' => $delivery_street_address,
                'delivery_suburb' => $delivery_suburb,
                'delivery_city' => $delivery_city,
                'delivery_postcode' => $delivery_postcode,
                'delivery_state' => $delivery_state,
                'delivery_country' => $delivery_country,
                // 'delivery_address_format_id' => $delivery_address_format_id,

                'billing_name' => $customers_name,
                'billing_street_address' => $billing_street_address,
                'billing_suburb' => $billing_suburb,
                'billing_city' => $billing_city,
                'billing_postcode' => $billing_postcode,
                'billing_state' => $billing_state,
                'billing_country' => $billing_country,
                //'billing_address_format_id' => $billing_address_format_id,

                'payment_method' => $payment_method_name,
                'cc_type' => $cc_type,
                'cc_owner' => $cc_owner,
                'cc_number' => $cc_number,
                'cc_expires' => $cc_expires,
                'last_modified' => $last_modified,
                'date_purchased' => $date_purchased,
                'order_price' => $order_price,
                'shipping_cost' => $shipping_price,
                'shipping_method' => $shipping_method,
                // 'orders_status' => $orders_status,
                //'orders_date_finished'  => $orders_date_finished,
                'currency' => $currency,
                'order_information' => json_encode($order_information),
                'coupon_code' => $code,
                'coupon_amount' => $coupon_amount,
                'total_tax' => $tax_rate,
                'ordered_source' => '3',
                'delivery_phone' => $delivery_phone,
                'billing_phone' => $billing_phone,

                // 'delivery_latitude' => $delivery_latitude,
                // 'delivery_longitude' => $delivery_longitude,
                // 'transaction_id'    => $transaction_id,
                // 'bank_account_image' => $bank_account_image,
                // 'bank_account_iban' => $bank_account_iban,
                'admin_discount' => $request->discount,
                'cache' => $request->cache,
                'paied' => $request->paied??0,
                'duration' => $request->duration??0,
                'invoice_type' => $request->invoice_type??1,
                'delivery_date' => $request->delivery_date??null,
                'admin_id'  => auth()->user()->id
            ]);

            //orders status history
            $orders_history_id = DB::table('orders_status_history')->insertGetId([
                'orders_id' => $orders_id,
                'orders_status_id' => $orders_status,
                'date_added' => $date_added,
                'customer_notified' => '1',
                'comments' => $comments,
            ]);

            if($orders_id) {
                $subtotal = 0;
                $tax = 0;
                $shipping = 0;
                $total_tax = 0;

                if(Session::has('posCart') && count(Session::get('posCart')) > 0)
                {
                    foreach (Session::get('posCart') as $key => $cartItem){
                        // $product = Product::find($cartItem['id']);

                        $product = DB::table('products')
                            ->leftJoin('products_description', 'products_description.products_id', '=', 'products.products_id')
                            ->where('products.products_id', $cartItem['id'])
                            ->where('language_id', 1)
                            ->first();
                        $current_stock_in = DB::table('inventory')->where('products_id', $cartItem['id'])->Where('stock_type', '=', 'in')->sum('stock');
                        $current_stock_out = DB::table('inventory')->where('products_id', $cartItem['id'])->Where('stock_type', '=', 'out')->sum('stock');
                        $current_stock = $current_stock_in - $current_stock_out;

                        $subtotal += $cartItem['price']*$cartItem['quantity'];
                        $tax += $cartItem['tax']*$cartItem['quantity'];

                        $product_variation = $cartItem['variant'];

                        $total_tax += $cartItem['tax']*$cartItem['quantity'];

                        if($product_variation != null){
                            // $product_stock = $product->stocks->where('variant', $product_variation)->first();
                            // if($cartItem['quantity'] > $product_stock->pos_qty){
                            //     $order->delete();
                            //     return 0;
                            // }
                            // else {
                            //     $product_stock->pos_qty -= $cartItem['quantity'];
                            //     $product_stock->save();
                            // }
                        }
                        else {
                            if ($cartItem['quantity'] > $current_stock) {
                                // $order->delete();
                                // return 0;
                                $responseData = array('status' => 2, 'message' => "Sorry, ".$product->products_name." is out of stock");
                                return response()->json($responseData);
                            }
                            // else {
                            //     // $product->current_stock -= $cartItem['quantity'];
                            //     // $product->save();
                            //     $minusStock = DB::table('inventory')->insert([])
                            // }
                        }

                        DB::table('products')->where('products_id', $cartItem['id'])->increment('products_ordered', 1);
                        $orders_products_id = DB::table('orders_products')->insertGetId([
                            'orders_id' => $orders_id,
                            'products_id' => $cartItem['id'],
                            'products_name' => $product->products_name,
                            'products_price' => $product->products_price,
                            'final_price' => $subtotal + $total_tax,
                            'products_tax' => $products_tax,
                            'products_quantity' => $cartItem['quantity'],
                        ]);
                        $inventory_ref_id = DB::table('inventory')->insertGetId([
                            'products_id' => $cartItem['id'],
                            'reference_code' => '',
                            'stock' => $cartItem['quantity'],
                            'admin_id' => 0,
                            'added_date' => time(),
                            'purchase_price' => 0,
                            'stock_type' => 'out',
                            'orders_id' => $orders_id,
                        ]);

                        // if (Session::get('guest_checkout') == 1) {
                        //     DB::table('customers_basket')->where('session_id', Session::getId())->where('products_id', $cartItem['id'])->update(['is_order' => '1']);

                        // } else {
                            DB::table('customers_basket')->where('customers_id', $customers_id)->where('products_id', $cartItem['id'])->update(['is_order' => '1']);
                        // }

                        // if (!empty($products->attributes) and count($products->attributes)>0) {

                        //     foreach ($products->attributes as $attribute) {
                        //         DB::table('orders_products_attributes')->insert([
                        //             'orders_id' => $orders_id,
                        //             'products_id' => $products->products_id,
                        //             'orders_products_id' => $orders_products_id,
                        //             'products_options' => $attribute->attribute_name,
                        //             'products_options_values' => $attribute->attribute_value,
                        //             'options_values_price' => $attribute->values_price,
                        //             'price_prefix' => $attribute->prefix,
                        //         ]);

                        //         DB::table('inventory_detail')->insert([
                        //             'inventory_ref_id' => $inventory_ref_id,
                        //             'products_id' => $products->products_id,
                        //             'attribute_id' => $attribute->products_attributes_id,
                        //             'orders_id' => $orders_id,
                        //         ]);
                        //     }
                        // }

                        $request->session()->put('order_id', $orders_id);

                    }
                }

                if(Session::has('posCartNew') && count(Session::get('posCartNew')) > 0)
                {
                    foreach (Session::get('posCartNew') as $key => $cartItem){
                        // $product = Product::find($cartItem['id']);


                        $subtotal += $cartItem['price']*$cartItem['quantity'];
                        $tax += $cartItem['tax']*$cartItem['quantity'];

                        // $product_variation = $cartItem['variant'];

                        $total_tax += $cartItem['tax']*$cartItem['quantity'];


                        $orders_products_id = DB::table('orders_products')->insertGetId([
                            'orders_id' => $orders_id,
                            'products_id' => $cartItem['id'],
                            'products_name' => $cartItem['name'],
                            'products_price' => $cartItem['price'],
                            'final_price' => $subtotal + $total_tax,
                            'products_tax' => $cartItem['tax'],
                            'products_quantity' => $cartItem['quantity'],
                        ]);
                        $inventory_ref_id = DB::table('inventory')->insertGetId([
                            'products_id' => $cartItem['id'],
                            'reference_code' => '',
                            'stock' => $cartItem['quantity'],
                            'admin_id' => 0,
                            'added_date' => time(),
                            'purchase_price' => 0,
                            'stock_type' => 'out',
                            'orders_id' => $orders_id,
                        ]);


                        // } else {
                            DB::table('customers_basket')->where('customers_id', $customers_id)->where('products_id', $cartItem['id'])->update(['is_order' => '1']);
                        // }


                        $request->session()->put('order_id', $orders_id);

                    }
                }


                Session::forget('pos_shipping_info');
                    Session::forget('shipping');
                    Session::forget('pos_discount');
                    Session::forget('posCart');
                    Session::forget('posCartNew');
                    // return 1;

                    $responseData = array(
                        'success' => '1',
                        'data' => 1,
                        'message' => "Order has been placed successfully.",
                        'order_id' => $orders_id,
                        'print_url' => route('invoiceprint', $orders_id)
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
