<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App;

class Cart extends Model {

    // protected $append = ['categories_image'];
    protected $table = 'carts';

    // public function setCategoriesImageAttribute(){
    //     return $this->categories_image != null ? asset($this->categories_image) :  asset() ;
    // }

    // public function getCategoriesImageAttribute($value)
    // {
    //     return asset($value);
    // }

    public function products()
    {
        return $this->belongsToMany(Product::class,'cart_product','cart_id','product_id','cart_id','products_id');
    }


}
