<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class CartLikeCollection extends ResourceCollection
{
    public function toArray($request)
    {
        return [
            'data' => $this->collection->map(function($data) {
                return [
                    'products_id' => $data->product_id,
                    'products_quantity' => $data->quantity,
                    'products_image' => $data->product_image,
                    'products_name' => $data->product_name,                    
                    'price' => $data->price,                    
                ];
            })
        ];
    }

   
    public function with($request)
    {
        return [
            'success' => true,
            'status' => 200
        ];
    }
}
