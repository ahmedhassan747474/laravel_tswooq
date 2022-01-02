<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;

use App\Http\Controllers\Api\V1\BaseController;
use App\Transformers\Api\V1\UserTransformer;

use App\Http\Controllers\AdminControllers\SiteSettingController;

use App;
use App\Group;
use App\Http\Resources\GroupCollection;
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
    
            return new ProductCollection($products);
        }

    }

    public function get_groups_by_vendor(Request $request)
    {
        $groups = Group::with('products')->where('vendor_id',$request->vendor_id)->get(); 
        return new GroupCollection($groups);
        // return response()->json(['data'=>$groups]);
    }
    public function get_all_groups(Request $request)
    { 
        $groups = Group::with('products')->get();
        return new GroupCollection($groups);   
    }
    // likeproduct
    public function likeproduct(Request $request){
        $user = $this->getAuthenticatedUser();
        if($user){
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
        }else
        {
            return response()->json(['message' => trans('common.user_not_exist'), 'status_code' => 401], 401);
        }
        
    }

    // likeProduct
    public function unlikeproduct(Request $request){
        $user = $this->getAuthenticatedUser();
        if($user){
            $liked_products_id = $request->product_id;
            $liked_customers_id = $user->id;
            $date_liked = date('Y-m-d H:i:s');

            DB::table('liked_products')->where([
                'liked_products_id' => $liked_products_id,
                'liked_customers_id' => $liked_customers_id,
            ])->delete();

            DB::table('products')->where('products_id', '=', $liked_products_id)->where('products_liked','>',0)->decrement('products_liked');

            $response = DB::table('liked_products')->select('liked_products_id')->where('liked_customers_id', '=', $liked_customers_id)->get();
            $responseData = array('success' => '1', 'product_data' => $response, 'message' => "Product is unliked.");

            return response()->json($responseData);
        }else
        {
            return response()->json(['message' => trans('common.user_not_exist'), 'status_code' => 401], 401);
        }
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

            if($request->categories_id){
                $products->whereHas('categories',function($query) use($request){
                   return $query->where('products_to_categories.categories_id',$request->categories_id);
                });
            }
    
            // $responseData = $this->productObject($request,$products);
            return new ProductCollection($products->paginate(10));
    }

    public function getproductsbybrand(Request $request){
        $products = AppProduct::latest()
            ->with(['stocks','images']);

        if($request->brand_id){
            $products->whereHas('categories',function($query) use($request){
               return $query->where('products_to_categories.categories_id',$request->brand_id);
            });
        }

        // $responseData = $this->productObject($request,$products);
        return new ProductCollection($products->paginate(10));
    }
    public function getproductbyid(Request $request){
        $products = AppProduct::latest()
        ->with(['stocks','images']);
        
        if($request->id){
            $products->where('products_id',$request->id);
        }

        // $responseData = $this->productObject($request,$products);
        return new ProductCollection($products->paginate(10));
    }

    

    public function getproductsyfavourite(Request $request){

        $user = $this->getAuthenticatedUser();

        if($user)
        {
            $products=$user->favoriteProducts;
            // $responseData = $this->productObject($request,$products);
            return new ProductCollection($products);

        }
        else{
            return response()->json(['message'=>'User Not Exist'],401);
        }
    }
}
