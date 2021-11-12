<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class OrderCollection extends ResourceCollection
{
    public function toArray($request)
    {
        return [
            'data' => $this->collection->map(function($data) {
                dd($data->data[0]['product_data']);
                return [
                    "orders_id" => $data->orders_id,
            // "total_tax": 0,
            
            // "delivery_name": " ",
            // "delivery_company": null,
            // "delivery_street_address": "غغغغغغغ",
            // "delivery_suburb": null,
            // "delivery_city": "451451",
            // "delivery_postcode": null,
            // "delivery_state": "other",
            // "delivery_country": null,
            // "delivery_address_format_id": null,
            
            // "payment_method": "Cash on Delivery",
            
           
            // "currency": "SAR",
            // "order_price": 540,
            // "shipping_cost": 0,
            // "is_seen": 1,
            // "free_shipping": 0,
            // "delivery_phone": null,
            // "delivery_time": null,
            // "delivery_latitude": null,
            // "delivery_longitude": null,
            // "admin_discount": null,
            // "admin_id": null,
            // "deliveryboy_info": [],
            // "discount_type": [],
            // "amount": [],
            // "orders_status": "Pending",
            // "customer_comments": "اااا",
            // "admin_comments": ""
                    'products' => 
                    [
                        'products_id' => $data['product_data']->products_id,
                        'products_quantity' => $data['product_data']->products_quantity,
                        'products_image' => $data['product_data']->products_image,
                        'barcode' => $data['product_data']->barcode,
                        'shop_name' => $data['product_data']->shop_name,
                        'products_name' => $data['product_data']->products_name,
                        'products_description' => $data['product_data']->products_description,
                        'images' => $data['product_data']->images,
                        'attributes' => $data['attributes']??[],
                    ]
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
