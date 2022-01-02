<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Facades\DB;

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
                    "date_purchased" =>$data->date_purchased,
                    "customer_comments" => $data->customer_comments,
                    "admin_comments" => $data->admin_comments,
                    
                    'products' => $this->products($data) 
                    
                ];
            })
        ];
    }

    public function products($data)
    {
        $results=array();
        foreach($data->products as $product){
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
        $like_products=DB::table('orders_products')->where('type',2)->where('orders_id',$data->orders_id)->get();
        foreach($like_products??[] as $product){
            $results[]=[
                'products_id' => $product->products_id,
                'products_quantity' => $product->products_quantity,
                'products_image' => $product->products_image,
                'products_name' => $product->products_name,
                'price' => $product->products_price,
                'type' => $product->type,
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
