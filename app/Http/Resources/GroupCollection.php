<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class GroupCollection extends ResourceCollection
{
    public function toArray($request)
    {
        return [
            'data' => $this->collection->map(function($data) {
                return [
                    "id" => $data->id,
                    "vendor_id" => $data->vendor_id,
                    "name_en" => $data->name_en,
                    "name_ar" => $data->name_ar,
                    "vendor_id" => $data->vendor_id,
                    'products' => $this->products($data->products) 
                    
                ];
            })
        ];
    }

    public function products($products)
    {
        $results=array();
        foreach($products as $product){
            $results[]=[
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
