<?php

namespace App\Traits;

use App\ImageCategories;
use App\Product;
use App\ProductStock;
use Illuminate\Support\Facades\DB;

trait ProductTrait {
    public function productObject($request,$products)
    {
       
        //filter data
        if($request->search){
            $products->whereHas('descriptions',function($query) use($request){
               return $query->where('products_name','LIKE','%'.$request->search.'%')
                      ->orWhere('products_description','LIKE','%'.$request->search.'%');
            });
            $products->orWhere('barcode','LIKE','%'.$request->search.'%');
        }
           
        if($request->minPrice && $request->maxPrice ){
            $products->whereHas('stocks',function($query) use($request){
               return $query->whereBetween('price', [$request->minPrice, $request->maxPrice]);

            });
        }        
        if(isset($request->filters) && count($request->filters) >0){
            foreach($request->filters as $filter){
                $products->whereHas('stocks',function($query) use($request,$filter){
                    return $query->where('variant', 'LIKE','%'.$filter['value'].'%');
                 });
            }    
        }

        if($request->categories_id){
            $products->whereHas('categories',function($query) use($request){
               return $query->where('products_to_categories.categories_id',$request->categories_id);
            });
        }

        if($request->brand_id){
            $products->whereHas('categories',function($query) use($request){
               return $query->where('products_to_categories.categories_id',$request->brand_id);
            });
        }

        if($request->id){
            $products->where('products_id',$request->id);
        }

        $products=$products->get();
        $results=array();
        // foreach($products as $key=>$product){
            
        //     $results[$key]['product_data']=$product;
          
        //     $stocks=ProductStock::where('product_id',$product->products_id);

        //     if(isset($stocks) && count($stocks->get())>0){
        //         if($request->minPrice && $request->maxPrice ){
        //             $stocks->whereBetween('price', [$request->minPrice, $request->maxPrice]);
        //         }

        //         if(isset($request->filters) && count($request->filters) >0){
        //             foreach($request->filters as $filter){
        //                 $stocks->where('variant', 'LIKE','%'.$filter['value'].'%');
        //             }

        //         }

        //         $stocks=$stocks->get();
        //         // dd($product->colors);
        //         foreach($stocks as $index=>$stock){
        //             $results[$key]['attributes'][$index]['stock_id']=$stock->id;
        //             foreach(explode('-', $stock->variant) as $i=>$variant){
        //                 if($product->colors && $i==0){
        //                     $results[$key]['attributes'][$index]['color']=$variant;
                            
        //                 }else
        //                 {

        //                     $choice_options = json_decode($product->choice_options);
        //                      if($choice_options){
        //                         $attribute_name = DB::table('products_options_descriptions')->where('products_options_id',$choice_options[$i-1]->attribute_id)->where('language_id',$request->language_id ?? 1)->first()->options_name;
        //                         $results[$key]['attributes'][$index][$attribute_name]=$variant;
        //                      }
        //                 }

        //             }

        //             $results[$key]['attributes'][$index]['price']=$stock->price;
        //             if($stock->image){
        //                 $imagepath= ImageCategories::where('image_id',$stock->image)
        //                             ->where('image_type', '=', 'THUMBNAIL')
        //                             ->where('image_type', '!=', 'THUMBNAIL')
        //                             ->orWhere('image_type', '=', 'ACTUAL')->first()->path ?? '';
         
        //                 $results[$key]['attributes'][$index]['image']=asset($imagepath);
        //             }

        //             $results[$key]['attributes'][$index]['quantity']=$stock->qty;
        //             $results[$key]['attributes'][$index]['SKU']=$stock->sku;

                         
        //         }
        //     }
        //     // unset($results[$key]['product_data']->colors);
        //     // unset($results[$key]['product_data']->choice_options);
        //     // unset($results[$key]['product_data']->attributes);
        //     // unset($results[$key]['product_data']->admin_id);
        // }

        return $results;
        
    }
}