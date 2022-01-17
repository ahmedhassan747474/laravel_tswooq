<?php

namespace App\Http\Resources;

use App\ImageCategories;
use App\ProductStock;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Facades\DB;

class CartCollection extends ResourceCollection
{
    public function toArray($request)
    {
  
        return 
            $this->collection->map(function($data) {
                 return
                    $this->products($data) 
                ;
            })
 
        ;
    }

    public function products($data)
    {
        $results=array();
        foreach($data->products as $product){
            $results=[
                'products_id' => $product->products_id,
                'products_quantity' =>DB::table('cart_product')->where('cart_id',$data->cart_id)->where('product_id',$product->products_id)->first()->quantity,
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
                'attributes' => $this->getattributes($product,$data)??[],
            ];
        }
        if(empty($results)){
            return null;
        }
        return $results;
    }

    public function getattributes($product,$data)
    {
        $results =[];
        $index =0;
        $results[$index]['price'] = $product->products_price;           
        $results[$index]['image'] = $product->products_image;           
        $results[$index]['quantity'] =DB::table('cart_product')->where('cart_id',$data->cart_id)->where('product_id',$product->products_id)->first()->quantity        ;           
        $stocks=ProductStock::where('id',$data->stock_id);

            if(isset($stocks) && count($stocks->get())>0){
                if(request()->minPrice && request()->maxPrice ){
                    $stocks->whereBetween('price', [request()->minPrice, request()->maxPrice]);
                }

                if(isset(request()->filters) && count(request()->filters) >0){
                    foreach(request()->filters as $filter){
                        $stocks->where('variant', 'LIKE','%'.$filter['value'].'%');
                    }
                }

                $stocks=$stocks->get();
                // dd($product->colors);
                foreach($stocks as $stock){
                    $index++;
                    $results[$index]['stock_id']=$stock->id;
                    foreach(explode('-', $stock->variant) as $i=>$variant){
                        if($product->colors && $i==0){
                            $results[$index]['color']=$variant;
                            
                        }else
                        {

                            $choice_options = json_decode($product->choice_options);
                             if($choice_options){
                                $attribute_name = DB::table('products_options_descriptions')->where('products_options_id',$choice_options[$i-1]->attribute_id)->where('language_id',request()->language_id ?? 1)->first()->options_name;
                                $results[$index][$attribute_name]=$variant;
                             }
                        }

                    }

                    $results[$index]['price']=$stock->price;
                    $results[$index]['quantity']=        DB::table('cart_product')->where('cart_id',$data->cart_id)->where('product_id',$product->products_id)->first()->quantity                    ;

                    // $results[$index]['price_pos']=$stock->price_pos;
                    // $results[$index]['quantity_pos']=$stock->qty;

                    if($stock->image){
                        $imagepath= ImageCategories::where('image_id',$stock->image)
                                    ->where(function($q){
										$q->where('image_type', '=', 'THUMBNAIL')
																->where('image_type', '!=', 'THUMBNAIL')
																->orWhere('image_type', '=', 'ACTUAL');
									})->first()->path ?? '';
         
                        $results[$index]['image']=asset($imagepath);
                    }

                    $results[$index]['SKU']=$stock->sku;

                         
                }
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
