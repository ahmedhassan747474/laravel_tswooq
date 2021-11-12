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
                    'products_id' => $data['product_data']->products_id,
                    'products_quantity' => $data['product_data']->products_quantity,
                    'products_image' => $data['product_data']->products_image,
                    'barcode' => $data['product_data']->barcode,
                    'shop_name' => $data['product_data']->shop_name,
                    'products_name' => $data['product_data']->products_name,
                    'products_description' => $data['product_data']->products_description,
                    'images' => $data['product_data']->images,
                    'attributes' => $data['attributes']??[],
                    
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
