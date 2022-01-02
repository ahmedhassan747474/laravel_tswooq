<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class CartCollection extends ResourceCollection
{
    public function toArray($request)
    {
  
        return 
            $this->collection->map(function($data) {
                 return
                    $this->products($data->products) 
                ;
            })
 
        ;
    }

    public function products($products)
    {
        $results=array();
        foreach($products as $product){
            $results=[
                'products_id' => $product->products_id,
                'products_quantity' => $product->products_quantity,
                'products_image' => $product->products_image,
                'barcode' => $product->barcode,
                'discount_price'=>$product->discount_price,
                'rating'=>$product->rating,
                'number_of_likes'=>$product->products_liked,
                'products_liked'=>$product->products_like,
                'shop_name' => $product->shop_name,
                'products_name' => $product->products_name,
                'products_description' => $product->products_description,
                'images' => $product->images,
                'attributes' => $product->products_attributes??[],
            ];
        }
        if(empty($results)){
            return null;
        }
        return $results;
    }
    public function with($request)
    {
        return [
            'success' => true,
            'status' => 200
        ];
    }
}
