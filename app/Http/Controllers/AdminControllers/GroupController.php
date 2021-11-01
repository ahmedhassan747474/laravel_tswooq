<?php

namespace App\Http\Controllers\AdminControllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Group;
use Illuminate\Support\Str;
use Auth;
use DB;


use App\Http\Controllers\AdminControllers\AlertController;
use App\Http\Controllers\AdminControllers\SiteSettingController;
use App\Models\Core\Categories;
use App\Models\Core\Images;
use App\Models\Core\Languages;
use App\Models\Core\Manufacturers;
use App\Models\Core\Products;
use App\Models\Core\Reviews;
use App\Models\Core\Setting;
use Illuminate\Support\Facades\Lang;
use App\Product;
class GroupController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct(Products $products, Languages $language, Images $images, Categories $category, Setting $setting,
        Manufacturers $manufacturer, Reviews $reviews) {
        $this->category = $category;
        $this->reviews = $reviews;
        $this->language = $language;
        $this->images = $images;
        $this->manufacturer = $manufacturer;
        $this->products = $products;
        $this->myVarsetting = new SiteSettingController($setting);
        $this->myVaralter = new AlertController($setting);
        $this->Setting = $setting;

    }

    public function index(Request $request)
    {
        $title = array('pageTitle' => Lang::get("labels.reviews"));
        $result = array();
        $data = $this->reviews->paginator();
        $result['reviews'] = $data;
        $result['commonContent'] = $this->Setting->commonContent();

        if (!empty($request->query)) {
            $req=$request->qry;   
        }
        else{
            $req='';
        }
        // dd($req);
        return  view('admin.group.index',compact('req','result'));
        
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name_en' => 'required|max:100',
            'name_ar' => 'required|max:100'
        ]);

        $group=new Group;
        $group->name_en=$request->name_en;
        $group->name_ar=$request->name_ar;
        $group->vendor_id=auth()->user()->id;
        $group->save();
        $message = Lang::get("labels.groupstoreMessage");
        return redirect()->back()->withErrors([$message]);

        // return response()->json('group created');

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $title = array('pageTitle' => Lang::get("labels.reviews"));
        $result = array();
        $data = $this->reviews->paginator();
        $result['reviews'] = $data;
        $result['commonContent'] = $this->Setting->commonContent();

        $info=Group::find($id);
        return view('admin.group.edit',compact('info','result'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'name_en' => 'required|max:100',
            'name_ar' => 'required|max:100'
        ]);

        // $checktitle= Group::where('name_en',$request->name)->orWhere('name_ar',$request->name)->where('id','!=',$id)->first();

        // if (!empty($checktitle)) {
        //     return response()->json(['Group Name Must Be unique'],401);
        // }        

        $group=Group::find($id);
        $group->name_en=$request->name_en;
        $group->name_ar=$request->name_ar;
        $group->vendor_id=auth()->user()->id;
        $group->save();
        $message = Lang::get("labels.groupupdateMessage");
        return redirect()->back()->withErrors([$message]);
        // return response()->json('Group Updated');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {   
        if ($request->method=='delete') {
             if ($request->ids) {
                foreach ($request->ids as $id) {
                    Group::destroy($id);
                }
             }
        }
       
        $message = Lang::get("labels.groupdeleteMessage");
        return redirect()->back()->withErrors([$message]);
        // return response()->json('Group Removed');
    }
}
