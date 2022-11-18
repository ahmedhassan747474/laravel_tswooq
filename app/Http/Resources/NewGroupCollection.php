<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class NewGroupCollection extends ResourceCollection
{
    public function toArray($request)
    {
        return [
            'data' => $this->collection->map(function($data) {

                $products = collect($this->products($data->products))->groupBy('categoriy.categories_slug');
                $newProducts = [];
                foreach($products as $name => $value ){
                    array_push($newProducts,["category"=>$name,"products_category"=>$value]);
                }
                return [
                    "id" => $data->id,
                    "vendor_id" => $data->vendor_id,
                    "name_en" => $data->name_en,
                    "name_ar" => $data->name_ar,
                    "vendor_id" => $data->vendor_id,
                    // 'products' => $data->products
                    
                    'products' => $newProducts
                    
                ];
            })
        ];
    }

    public function products($products)
    {
       
        $results=array();
        foreach($products as $product){
            //  print_r($product);
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
                'categoriy' => $product->subCategories[0]??[],
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
