<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Product extends Model {

    protected $fillable = [
        'name', 'added_by', 'user_id', 'category_id', 'brand_id', 'video_provider', 'video_link', 'unit_price',
        'purchase_price', 'unit', 'slug', 'colors', 'choice_options', 'variations', 'current_stock', 'thumbnail_img'
    ];

    protected $appends = ['shop_name','rating','products_name','products_description','products_attributes','products_like'];

    protected $hidden =[
        'colors',
        'choice_options',
        'attributes',
        'stocks',
        'descriptions',
        "products_quantity",
        "products_model",
        "products_video_link",
        "unit_price",
        "current_stock", 
        "purchase_price",
        "price_buy",
        "products_price",
        "products_date_added",
        "products_last_modified",
        "products_date_available",
        "products_status",
        "is_current",
        "products_tax_class_id",
        "manufacturers_id",
        "products_ordered",
        "products_liked",
        "low_limit",
        "is_feature",
        "products_slug",
        "products_type",
        "products_min_order",
        "products_max_stock",
        "is_show_web",
        "is_show_app",
        "is_show_admin",
        "admin_id",
        "product_parent_id",
        "created_at",
        "updated_at",
    ];

    public function getTranslation($field = '', $lang = false) {
        $lang = $lang == false ? App::getLocale() : $lang;
        $product_translations = $this->hasMany(ProductTranslation::class)->where('lang', $lang)->first();
        return $product_translations != null ? $product_translations->$field : $this->$field;
    }

    public function product_translations() {
        return $this->hasMany(ProductTranslation::class);
    }

    public function getRatingAttribute()
    {
        $reviews = DB::table('reviews')
                                    ->join('users', 'users.id', '=', 'reviews.customers_id')
                                    ->select('reviews.*', 'users.avatar as image')
                                    ->where('products_id', $this->products_id)
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

                                    $avarage_rate = (5 * $five_star + 4 * $four_star + 3 * $three_star + 2 * $two_star + 1 * $one_star) / ($five_star + $four_star + $three_star + $two_star + $one_star);
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

                                return number_format($avarage_rate, 2);
    }
    // public function category() {
    //     return $this->belongsTo(Category::class);
    // }

    public function groups()
    {
	    return $this->belongsToMany('App\Group','group_product','product_id','group_id','products_id','id');

        // return $this->belongsToMany('App\Models\AppModels\Product','group_product','group_id','product_id','id','products_id');
    }

    public function categories()
    {
        return $this->belongsToMany('App\Category','products_to_categories','products_id','categories_id','products_id','categories_id');
    }

    public function images()
    {
        return $this->belongsToMany('App\ImageCategories','products_images','products_id','image','products_id','image_id');
    }

    public function carts()
    {
        return $this->belongsToMany('App\Cart','cart_product','product_id','cart_id','products_id','cart_id');
    }

    public function orders()
    {
        return $this->belongsToMany('App\Order','orders_products','products_id','orders_id','products_id','orders_id');
    }

    public function getProductsImageAttribute($value)
    {
        // dd($value);
        $imagepath= ImageCategories::where('image_id',$value)->where(function($q){
            $q->where('image_type', '=', 'THUMBNAIL')
                                    ->where('image_type', '!=', 'THUMBNAIL')
                                    ->orWhere('image_type', '=', 'ACTUAL');
        })->first()->path ?? '';
                                    
        // dd($imagepath);
        return asset($imagepath);
    }

    public function getShopNameAttribute($value)
    {
        $getName=  DB::table('users')->where('id', $this->admin_id)->first()->shop_name??'';
     
        return $getName;
    }

    public function getProductsNameAttribute()
    {
        $getName=  $this->descriptions[0]->products_name ?? $this->products_slug ;
     
        return $getName;
    }

    public function getProductsLikeAttribute()
    { 
        if(Auth::check()){
            $liked = DB::table('liked_products')->where('liked_customers_id', '=', auth()->user()->id)->where('liked_products_id', '=', $this->products_id)->first();
            if($liked){
                return 1;
            }
        }
        return 0;
    }

    public function getProductsAttributesAttribute()
    {
        $results =[];
        $index =0;
        $results[$index]['price'] = $this->products_price;           
        $results[$index]['image'] = $this->products_image;           
        $results[$index]['quantity'] = $this->products_quantity;           
        $stocks=ProductStock::where('product_id',$this->products_id);

            if(isset($stocks) && count($stocks->get())>0){
                if(request()->minPrice && request()->maxPrice ){
                    $stocks->whereBetween('price', [request()->minPrice, request()->maxPrice]);
                }

                if(isset(request()->filters) && count(request()->filters) >0){
                    foreach(request()->filters as $filter){
                        $stocks->where('variant', 'LIKE','%'.$filter['value'].'%');
                    }
                }

                $stocks=$stocks->get();
                // dd($this->colors);
                foreach($stocks as $stock){
                    $index++;
                    $results[$index]['stock_id']=$stock->id;
                    foreach(explode('-', $stock->variant) as $i=>$variant){
                        if($this->colors && $i==0){
                            $results[$index]['color']=$variant;
                            
                        }else
                        {

                            $choice_options = json_decode($this->choice_options);
                             if($choice_options){
                                $attribute_name = DB::table('products_options_descriptions')->where('products_options_id',$choice_options[$i-1]->attribute_id)->where('language_id',request()->language_id ?? 1)->first()->options_name;
                                $results[$index][$attribute_name]=$variant;
                             }
                        }

                    }

                    $results[$index]['price']=$stock->price;
                    $results[$index]['quantity']=$stock->qty;

                    // $results[$index]['price_pos']=$stock->price_pos;
                    // $results[$index]['quantity_pos']=$stock->qty;

                    if($stock->image){
                        $imagepath= ImageCategories::where('image_id',$stock->image)
                                    ->where(function($q){
										$q->where('image_type', '=', 'THUMBNAIL')
																->where('image_type', '!=', 'THUMBNAIL')
																->orWhere('image_type', '=', 'ACTUAL');
									})->first()->path ?? '';
         
                        $results[$index]['image']=asset($imagepath);
                    }

                    $results[$index]['SKU']=$stock->sku;

                         
                }
            }
            return $results;
    }

    public function getProductsDescriptionAttribute($value)
    {
        $getName=  $this->descriptions[0]->products_name ?? '';

        return $getName;
    }

    public function descriptions() {
        return $this->hasMany(ProductDescription::class,'products_id','products_id');
    }

    public function brand() {
        return $this->belongsTo(Brand::class);
    }

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function orderDetails() {
        return $this->hasMany(OrderDetail::class);
    }

    public function reviews() {
        return $this->hasMany(Review::class)->where('status', 1);
    }

    public function wishlists() {
        return $this->hasMany(Wishlist::class);
    }

    public function stocks() {
        return $this->hasMany(ProductStock::class,'product_id','products_id');
    }
    
    public function taxes() {
        return $this->hasMany(ProductTax::class);
    }
    
    public function flash_deal_product() {
        return $this->hasOne(FlashDealProduct::class);
    }

    public function productsofgroup()
	{
	 return $this->belongsToMany('App\Group','group_product','product_id','group_id');
	}

    // public function getAttributesAttribute($value)
    // {
        
    //     $results=array();
       
          
    //         if(isset($this->stocks)){
                
    //             foreach($this->stocks as $index=>$stock){
    //                 foreach(explode('-', $stock->variant) as $i=>$variant){
    //                     // dd($variant);
    //                     if($this->colors && $i==0){
    //                         $results[$index]['color']=$variant;
                            
    //                             // dd($attribute_name);    
    //                     }else
    //                     {
    //                         $choice_options = json_decode($this->choice_options);
                            
    //                             $attribute_name = DB::table('products_options_descriptions')->where('products_options_id',$choice_options[$i-1]->attribute_id)->where('language_id',request()->language_id ?? 1)->first()->options_name;
    //                             $results[$index][$attribute_name]=$variant;
                                
    //                     }

    //                 }

    //                 $results[$index]['price']=$stock->price;
    //                 if($stock->image){
    //                     $imagepath= App\ImageCategories::where('image_id',$stock->image)
    //                                 ->where('image_type', '=', 'THUMBNAIL')
    //                                 ->where('image_type', '!=', 'THUMBNAIL')
    //                                 ->orWhere('image_type', '=', 'ACTUAL')->first()->path ?? '';
         
    //                     $results[$index]['image']=asset($imagepath);
    //                 }

    //                 $results[$index]['quantity']=$stock->qty;
    //                 $results[$index]['SKU']=$stock->sku;

                         
    //             }
    //         }
    //     return $results;
    // }


}
