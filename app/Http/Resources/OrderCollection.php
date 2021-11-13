<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class OrderCollection extends ResourceCollection
{
    public function toArray($request)
    {
        return [
            'data' => $this->collection->map(function($data) {
                return [
                    "orders_id" => $data->orders_id,
                    "user_id" => $data->customers_id,
                    "total_tax" => $data->total_tax,
                    "delivery_name" => $data->delivery_name,
                    "delivery_street_address" => $data->delivery_street_address,
                    "delivery_city" => $data->delivery_city,
                    "payment_method" => $data->payment_method,
                    "currency" => $data->currency,
                    "order_price" => $data->order_price,
                    "shipping_cost" => $data->shipping_cost,
                    "orders_status" => $data->orders_status,
                    "customer_comments" => $data->customer_comments,
                    "admin_comments" => $data->admin_comments,
                    
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
