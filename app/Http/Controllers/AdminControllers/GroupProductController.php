<?php

namespace App\Http\Controllers\AdminControllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Group;
use App\Product;
use App\GroupProduct;
use App\User;
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

class GroupProductController extends Controller
{
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
        
        $items = Product::get();
        foreach($items as $item) {
            $checkProduct = GroupProduct::where('group_id', $id)->where('product_id', $item->products_id)->count();
            if($checkProduct){
                $item->is_selected = 1;
            } else {
                $item->is_selected = 0;
            }
            $getRestaurantName = User::where('id', $item->admin_id)->first();
            // dd($getRestaurantName);
            $item->rest_name = $getRestaurantName->shop_name ?? 'admin';
        }

        // dd($items);
        return view('admin.group_product.edit',compact('info', 'items','result'));
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
            'items[]' => 'nullable|array' 
        ]);

        $deleteItems = GroupProduct::where('group_id', $id)->delete();
        foreach($request['items'] as $item) {
            $insert = GroupProduct::create([
                'group_id'  => $id,
                'product_id'    => $item
            ]);
        }     

        $message = Lang::get("labels.groupupdateMessage");
        return redirect()->back()->withErrors([$message]);
        // return response()->json('Group Product Updated');
    }
}
