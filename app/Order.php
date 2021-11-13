<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App;

class Order extends Model {

    // protected $append = ['categories_image'];
    protected $table = 'orders';

    protected $appends = ['shop_name'];
    
    public function getShopNameAttribute()
    {
        $getName=  DB::table('users')->where('id', $this->admin_id)->first()->shop_name??'';
     
        return $getName;
    }

    public function products()
    {
       return $this->belongsToMany(Product::class,'orders_products','orders_id','products_id','orders_id','products_id');
    }
}
