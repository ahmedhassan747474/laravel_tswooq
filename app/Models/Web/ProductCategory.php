<?php

namespace App\Models\Web;

use Illuminate\Database\Eloquent\Model;

class ProductCategory extends Model
{
    protected $table = 'products_to_categories';
    protected $guarded = [];
    public function category(){
        return $this->belongsTo('App\Models\Core\Categories','categories_id');
    }

    public function product(){
        return $this->belongsTo('App\Models\Web\Products','products_id','products_id');
    }
}
