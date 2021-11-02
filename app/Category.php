<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App;

class Category extends Model {

    protected $append = ['categories_image'];

    // public function setCategoriesImageAttribute(){
    //     return $this->categories_image != null ? asset($this->categories_image) :  asset() ;
    // }

    

    // public function image() {
    //     return $this->belongsTo('App\Image','categories_image','image_id');
    // }

    public function getCategoriesImageAttribute($value)
    {
        $imagepath= ImageCategories::where('image_id',$value)
                                    ->where('image_type', '=', 'THUMBNAIL')
                                    ->where('image_type', '!=', 'THUMBNAIL')
                                    ->orWhere('image_type', '=', 'ACTUAL')->first()->path ?? '';
        return asset($imagepath);
    }

    public function getCategoriesIconAttribute($value)
    {
        $imagepath= ImageCategories::where('image_id',$value)
                                    ->where('image_type', '=', 'THUMBNAIL')
                                    ->where('image_type', '!=', 'THUMBNAIL')
                                    ->orWhere('image_type', '=', 'ACTUAL')->first()->path ?? '';
        return asset($imagepath);
    }

}
