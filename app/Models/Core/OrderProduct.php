<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;
use Illuminate\Support\Facades\DB;



class OrderProduct extends Model
{
    protected $table='order_like_card_product';
    protected $fillable = array('product_id','product_name','product_image','product_quantity','final_price','order_like_card_id');

  public function orderCard(){
      return $this->belongsto('App\Models\Core\OrderLikeCard','order_like_card_id');
  }

}
