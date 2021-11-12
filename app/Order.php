<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App;

class Order extends Model {

    // protected $append = ['categories_image'];
    protected $table = 'orders';

    protected $appends = ['shop_name','items'];
    
    public function getShopNameAttribute()
    {
        $getName=  DB::table('users')->where('id', $this->admin_id)->first()->shop_name??'';
     
        return $getName;
    }

    public function getItemsAttribute()
    {
        $getName=  $this->descriptions[0]->products_name ?? $this->products_slug ;
     
        return $getName;
    }


}
