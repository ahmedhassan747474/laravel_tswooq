<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\V1\BaseController;
use App\Transformers\Api\V1\UserTransformer;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Password;
use Illuminate\Mail\Message;
use App\User;
use JWTAuth;
use Hash;
use Auth;
use Mail;
Use Image;
use Str;
use App;
use Illuminate\Support\Facades\File;
use App\Models\AppModels\Product;
use App\Models\AppModels\Banner;
use Carbon;
use DB;

class SliderController extends BaseController
{
    function __construct(UserTransformer $user_transformer) 
    {
        config(['auth.defaults.guard' => 'api']);
        $this->user_transformer = $user_transformer;
        App::setLocale(request()->header('Accept-Language'));
    }
    //get allcategories
	public function getsliders(Request $request){
        $language_id = $request->language_id;

        //current time
        $currentDate = Carbon\Carbon::now();
        $currentDate = $currentDate->toDateTimeString();

        $banners = DB::table('banners')
            ->LeftJoin('image_categories', function ($join) {
                $join->on('image_categories.image_id', '=', 'banners.banners_image')
                    ->where(function ($query) {
                        $query->where('image_categories.image_type', '=', 'THUMBNAIL')
                            ->where('image_categories.image_type', '!=', 'THUMBNAIL')
                            ->orWhere('image_categories.image_type', '=', 'ACTUAL');
                    });
            })
            ->select('banners_id as id', 'banners_title as title', 'banners_url as url', 'image_categories.image_id', 'image_categories.path as image', 'type', 'banners_title as title')
            ->where('status', '=', '1')
            ->where('expires_date', '>', $currentDate)
            ->where('languages_id', $language_id)
            ->get();
        
        foreach($banners as $banner){
            $banner->image = asset($banner->image);
        }

        if (count($banners) > 0) {
            $responseData = array('success' => '1', 'data' => $banners, 'message' => "Sliders are returned successfull.");
        } else {
            $banners = array();
            $responseData = array('success' => '0', 'data' => $banners, 'message' => "Sliders are empty.");
        }

        return response()->json($responseData);
    }
}