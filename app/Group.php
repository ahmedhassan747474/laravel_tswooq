<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Group extends Model
{
    protected $table="groups";

    public  function products()
    {
    //   $language_id = request()->language_id ?? 1;
      return $this->belongsToMany('App\Models\AppModels\Product','group_product','group_id','product_id','id','products_id')
                            ->select(
                            'products_id',
                            'products_quantity',
                            'products_image',
                            'price_buy',
                            'products_price',
                            'products_liked',
                            'products_ordered',
                            'barcode',
                            'admin_id',
                            'products_ordered',
                            'products_ordered',
                        );
      
    }
}
