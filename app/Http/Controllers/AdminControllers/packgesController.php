<?php
namespace App\Http\Controllers\AdminControllers;

use App\Http\Controllers\AdminControllers\SiteSettingController;
use App\Http\Controllers\Controller;
use App\Models\Core\Images;
use App\Models\Core\Setting;
use App\Models\Web\package;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use App\Models\Web\PackgeRequest;

class packgesController extends Controller
{
    public function __construct(Setting $setting)
    {
        $this->myVarsetting = new SiteSettingController($setting);
        $this->Setting = $setting;
    }
    public function getAllPackges(){
        $title = array('pageTitle' => Lang::get("labels.ListingBanners"));
        $result = array();
        $message = array();
        $result['message'] = $message;
        $result['commonContent'] = $this->Setting->commonContent();
        $packges = package::get();
        return view('admin.packges.index',compact('result','packges'));
    }

    public function editPackge($id){
        $title = array('pageTitle' => Lang::get("labels.ListingBanners"));
        $result = array();
        $message = array();
        $result['message'] = $message;
        $result['commonContent'] = $this->Setting->commonContent();
        $packges = package::findOrFail($id);
        return view('admin.packges.edit',compact('result','packges'));
    }

    public function updatePackge($id,Request $request){
        $packges = package::findOrFail($id);
        if($packges){
            $packges->name = $request->name;
            $packges->price = $request->price;
                        $packges->price_discont = $request->price_discont;
            $packges->price12 = $request->price12;
            $packges->price_discont_12 = $request->price_discont_12;
            $packges->discount = $request->discount;
            $packges->description = $request->description;
            $packges->type = $request->type;

            $packges->save();
            return redirect()->back();
        }
    }

    public function addPackge(){
        $title = array('pageTitle' => Lang::get("labels.ListingBanners"));
        $result = array();
        $message = array();
        $result['message'] = $message;
        $result['commonContent'] = $this->Setting->commonContent();
        
        return view('admin.packges.add',compact('result'));
    }

    public function insertPackge(Request $request){
        $packges = new package();
        if($packges){
            $packges->name = $request->name;
            $packges->price = $request->price;
            $packges->price_discont = $request->price_discont;
            $packges->price12 = $request->price12;
            $packges->price_discont_12 = $request->price_discont_12;
            $packges->discount = $request->discount;
            $packges->description = $request->description;
            $packges->type = $request->type;
            $packges->save();
            return redirect()->back();
        }
    }

    public function deletePackge($id){
        $packges = package::findOrFail($id);
        $packges->delete();
        return redirect()->back();
    }



    public function getAllRequests(){
        $title = array('pageTitle' => Lang::get("labels.ListingBanners"));
        $result = array();
        $message = array();
        $result['message'] = $message;
        $result['commonContent'] = $this->Setting->commonContent();
        $packgesRequest = PackgeRequest::get();

    //  return   $packgesRequests ;
        return view('admin.packge_requests.index',compact('packgesRequest','result'));
    }
}




