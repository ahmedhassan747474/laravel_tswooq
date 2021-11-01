<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App;
use Illuminate\Support\Facades\DB;

class Product extends Model {

    protected $fillable = [
        'name', 'added_by', 'user_id', 'category_id', 'brand_id', 'video_provider', 'video_link', 'unit_price',
        'purchase_price', 'unit', 'slug', 'colors', 'choice_options', 'variations', 'current_stock', 'thumbnail_img'
    ];

    // protected $appends = ['shop_name','products_name','products_description'];

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

    // public function category() {
    //     return $this->belongsTo(Category::class);
    // }

    public function categories()
    {
        return $this->belongsToMany('App\Category','products_to_categories','products_id','categories_id','products_id','categories_id');
    }

    // public function images()
    // {
    //     return $this->belongsToMany('App\ImageCategories','products_images','products_id','image','products_id','image_id');
    // }

    // public function getProductsImageAttribute($value)
    // {
    //     $imagepath= ImageCategories::where('image_id',$value)
    //                                 ->where('image_type', '=', 'THUMBNAIL')
    //                                 ->where('image_type', '!=', 'THUMBNAIL')
    //                                 ->orWhere('image_type', '=', 'ACTUAL')->first()->path ?? '';
    //     return asset($imagepath);
    // }

    public function getShopNameAttribute($value)
    {
        $getName=  DB::table('users')->where('id', $this->admin_id)->first()->shop_name;
     
        return $getName;
    }

    // public function getProductsNameAttribute($value)
    // {
    //     $getName=  $this->descriptions[0]->products_name ?? $this->products_slug ;
     
    //     return $getName;
    // }

    // public function getProductsDescriptionAttribute($value)
    // {
    //     $getName=  $this->descriptions[0]->products_name ?? '';

    //     return $getName;
    // }

    // public function descriptions() {
    //     return $this->hasMany(ProductDescription::class,'products_id','products_id');
    // }

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
	 return $this->belongsToMany('App\Group','group_product','product_id','group_id','products_id');
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
                            
    //                             $attribute_name = DB::table('products_options_descriptions')->where('products_options_id',$choice_options[$i-1]->attribute_id)->where('language_id',$request->language_id ?? 1)->first()->options_name;
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
