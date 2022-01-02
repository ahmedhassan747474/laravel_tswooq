<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class ProductCollection extends ResourceCollection
{
    public function toArray($request)
    {
        return [
            'data' => $this->collection->map(function($data) {
                // dd($data['product_data']);
                return [
                    'products_id' => $data->products_id,
                    'products_quantity' => $data->products_quantity,
                    'products_image' => $data->products_image,
                    'barcode' => $data->barcode,
                    'discount_price'=>$data->discount_price,
                    'rating'=>$data->rating,
                    'number_of_likes'=>$data->products_liked,
                    'products_liked'=>$data->products_like,
                    'shop_name' => $data->shop_name,
                    'products_name' => $data->products_name,
                    'products_description' => $data->products_description,
                    'images' => $data->images,
                    'attributes' => $data->products_attributes??[],
                    
                    // 'links' => [
                    //     'details' => route('products.show', $data->id),
                    //     'reviews' => route('api.reviews.index', $data->id),
                    //     'related' => route('products.related', $data->id),
                    //     'top_from_seller' => route('products.topFromSeller', $data->id)
                    // ]
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
