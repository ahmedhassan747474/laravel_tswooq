<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class LikeImage extends Model
{
    protected $table="like_images";
    protected $guarded=[];
}
