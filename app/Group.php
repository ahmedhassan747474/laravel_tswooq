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
      return $this->belongsToMany('App\Product','group_product','group_id','product_id','id','products_id')->with(['stocks','images']);
                            
    }
}
