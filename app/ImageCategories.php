<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App;

class ImageCategories extends Model {

    // protected $append = ['categories_image'];
    protected $table = 'image_categories';

    // public function setCategoriesImageAttribute(){
    //     return $this->categories_image != null ? asset($this->categories_image) :  asset() ;
    // }

    public function getPathAttribute($value)
    {
        return asset($value);
    }

    


}
