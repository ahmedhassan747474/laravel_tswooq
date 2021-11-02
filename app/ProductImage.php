<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App;

class ProductImage extends Model {

    protected $table = 'products_images';

    // public function setCategoriesImageAttribute(){
    //     return $this->categories_image != null ? asset($this->categories_image) :  asset() ;
    // }


    // public function image() {
    //     return $this->belongsTo('App\Image','categories_image','image_id');
    // }

    // public function getCategoriesImageAttribute($value)
    // {
    //     $imagepath= Image::where('image_id',$value)->first()->path;
    //     return asset($imagepath);
    // }

    // public function getCategoriesIconAttribute($value)
    // {
    //     $imagepath= Image::where('image_id',$value)->first()->path;
    //     return asset($imagepath);
    // }

}
