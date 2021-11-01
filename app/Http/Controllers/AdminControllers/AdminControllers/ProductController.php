<?php

namespace App\Http\Controllers\AdminControllers;

use App\Http\Controllers\AdminControllers\AlertController;
use App\Http\Controllers\AdminControllers\SiteSettingController;
use App\Http\Controllers\Controller;
use App\Models\Core\Categories;
use App\Models\Core\Images;
use App\Models\Core\Languages;
use App\Models\Core\Manufacturers;
use App\Models\Core\Products;
use App\Models\Core\Reviews;
use App\Models\Core\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;

use Validator;
class ProductController extends Controller
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

    public function reviews(Request $request)
    {
        $title = array('pageTitle' => Lang::get("labels.reviews"));
        $result = array();
        $data = $this->reviews->paginator();
        $result['reviews'] = $data;
        $result['commonContent'] = $this->Setting->commonContent();
        return view("admin.reviews.index", $title)->with('result', $result);

    }

    public function editreviews($id, $status)
    {
        if ($status == 1) {
            DB::table('reviews')
                ->where('reviews_id', $id)
                ->update([
                    'reviews_status' => 1,
                ]);
            DB::table('reviews')
                ->where('reviews_id', $id)
                ->update([
                    'reviews_read' => 1,
                ]);
        } elseif ($status == 0) {
            DB::table('reviews')
                ->where('reviews_id', $id)
                ->update([
                    'reviews_read' => 1,
                ]);
        } else {
            DB::table('reviews')
                ->where('reviews_id', $id)
                ->update([
                    'reviews_read' => 1,
                    'reviews_status' => -1,
                ]);
        }
        $message = Lang::get("labels.reviewupdateMessage");
        return redirect()->back()->withErrors([$message]);

    }

    public function display(Request $request)
    {
        $language_id = '1';
        $categories_id = $request->categories_id;
        $product = $request->product;
        $title = array('pageTitle' => Lang::get("labels.Products"));
        $subCategories = $this->category->allcategories($language_id);
        $products = $this->products->paginator($request);
        $results['products'] = $products;
        $results['currency'] = $this->myVarsetting->getSetting();
        $results['units'] = $this->myVarsetting->getUnits();
        $results['subCategories'] = $subCategories;
        $currentTime = array('currentTime' => time());
        $result['commonContent'] = $this->Setting->commonContent();
        return view("admin.products.index", $title)->with('result', $result)->with('results', $results)->with('categories_id', $categories_id)->with('product', $product);

    }

    public function add(Request $request)
    {
        $title = array('pageTitle' => Lang::get("labels.AddProduct"));
        $language_id = '1';
        $allimage = $this->images->getimages();
        $result = array();
        $categories = $this->category->recursivecategories($request);

        $parent_id = array();
        $option = '<ul class="list-group list-group-root well">';

        foreach ($categories as $parents) {

            if (in_array($parents->categories_id, $parent_id)) {
                $checked = 'checked';
            } else {
                $checked = '';
            }

            $option .= '<li href="#" class="list-group-item">
          <label style="width:100%">
            <input id="categories_' . $parents->categories_id . '" ' . $checked . ' type="checkbox" class=" required_one categories sub_categories" name="categories[]" value="' . $parents->categories_id . '">
          ' . $parents->categories_name . '
          </label></li>';

            if (isset($parents->childs)) {
                $option .= '<ul class="list-group">
          <li class="list-group-item">';
                $option .= $this->childcat($parents->childs, $parent_id);
                $option .= '</li></ul>';
            }
        }
        $option .= '</ul>';

        $result['categories'] = $option;

        $result['manufacturer'] = $this->manufacturer->getter($language_id);
        $taxClass = DB::table('tax_class')->get();
        $result['taxClass'] = $taxClass;
        $result['languages'] = $this->myVarsetting->getLanguages();
        $result['units'] = $this->myVarsetting->getUnits();
        $result['commonContent'] = $this->Setting->commonContent();
        $result['shops'] = DB::table('users')->where('role_id', '=', 11)->select('id', 'shop_name as name')->get();
        
        $attributes = DB::table('products_options')
            ->leftJoin('products_options_descriptions', 'products_options_descriptions.products_options_id', '=', 'products_options.products_options_id')
            ->where('products_options_descriptions.language_id', '=', 1)
            ->select('products_options.products_options_id', 'options_name')
            ->get();
        foreach($attributes as $attribute){
            $option_value = DB::table('products_options_values')
                ->leftJoin('products_options_values_descriptions', 'products_options_values_descriptions.products_options_values_id', '=', 'products_options_values.products_options_values_id')
                ->where('products_options_id', '=', $attribute->products_options_id)
                ->where('products_options_values_descriptions.language_id', '=', 1)
                ->select('products_options_values.products_options_values_id', 'products_options_values_descriptions.options_values_name')
                ->get();
            $attribute->option_value = $option_value;
        }

        $result['attributes'] = $attributes;

        $products = DB::table('products')
            ->join('products_description', 'products_description.products_id', '=', 'products.products_id')
            ->select('products.products_id as id', 'products_description.products_name as name')
            ->where('language_id', '=', 1)
            ->get();

        $result['products'] = $products;

        return view("admin.products.add", $title)->with('result', $result)->with('allimage', $allimage);

    }

    public function childcat($childs, $parent_id)
    {

        $contents = '';
        foreach ($childs as $key => $child) {

            if (in_array($child->categories_id, $parent_id)) {
                $checked = 'checked';
            } else {
                $checked = '';
            }

            $contents .= '<label> <input id="categories_' . $child->categories_id . '" parents_id="' . $child->parent_id . '"  type="checkbox" name="categories[]" class="required_one sub_categories categories sub_categories_' . $child->parent_id . '" value="' . $child->categories_id . '" ' . $checked . '> ' . $child->categories_name . '</label>';

            if (isset($child->childs)) {
                $contents .= '<ul class="list-group">
        <li class="list-group-item">';
                $contents .= $this->childcat($child->childs, $parent_id);
                $contents .= "</li></ul>";
            }

        }
        return $contents;
    }

    public function edit(Request $request)
    {
        $allimage = $this->images->getimages();
        $result = $this->products->edit($request);
        //dd($result['categories_array']);
        $categories = $this->category->recursivecategories($request);

        $parent_id = $result['categories_array'];
        $option = '<ul class="list-group list-group-root well">';

        foreach ($categories as $parents) {

            if (in_array($parents->categories_id, $parent_id)) {
                $checked = 'checked';
            } else {
                $checked = '';
            }

            $option .= '<li href="#" class="list-group-item">
        <label style="width:100%">
          <input id="categories_' . $parents->categories_id . '" ' . $checked . ' type="checkbox" class=" required_one categories sub_categories" name="categories[]" value="' . $parents->categories_id . '">
        ' . $parents->categories_name . '
        </label></li>';

            if (isset($parents->childs)) {
                $option .= '<ul class="list-group">
        <li class="list-group-item">';
                $option .= $this->childcat($parents->childs, $parent_id);
                $option .= '</li></ul>';
            }
        }

        $option .= '</ul>';
        $result['categories'] = $option;
        $title = array('pageTitle' => Lang::get("labels.EditProduct"));
        $result['commonContent'] = $this->Setting->commonContent();
        $result['shops'] = DB::table('users')->where('role_id', '=', 11)->select('id', 'shop_name as name')->get();
        $attributes = DB::table('products_options')
            ->leftJoin('products_options_descriptions', 'products_options_descriptions.products_options_id', '=', 'products_options.products_options_id')
            ->where('products_options_descriptions.language_id', '=', 1)
            ->select('products_options.products_options_id', 'options_name')
            ->get();
        foreach($attributes as $attribute){
            $option_value = DB::table('products_options_values')
                ->leftJoin('products_options_values_descriptions', 'products_options_values_descriptions.products_options_values_id', '=', 'products_options_values.products_options_values_id')
                ->where('products_options_id', '=', $attribute->products_options_id)
                ->where('products_options_values_descriptions.language_id', '=', 1)
                ->select('products_options_values.products_options_values_id', 'products_options_values_descriptions.options_values_name')
                ->get();
            $attribute->option_value = $option_value;
            foreach($option_value as $value){
                $option_value_select = DB::table('products_attributes')
                    ->where('products_id', '=', $request->id)
                    ->where('options_values_id', '=', $value->products_options_values_id)
                    ->first();
                $value->is_selected = $option_value_select ? 1 : 0;
            }
        }

        $result['attributes'] = $attributes;

        $products = DB::table('products')
            ->join('products_description', 'products_description.products_id', '=', 'products.products_id')
            ->select('products.products_id as id', 'products_description.products_name as name')
            ->where('language_id', '=', 1)
            ->get();

        $result['products'] = $products;
        
        return view("admin.products.edit", $title)->with('result', $result)->with('allimage', $allimage);

    }

    public function update(Request $request)
    {
        $result = $this->products->updaterecord($request);
        $products_id = $request->id;
        if ($request->products_type == 1) {
            return redirect('admin/products/attach/attribute/display/' . $products_id);
        } else {
            return redirect('admin/products/images/display/' . $products_id);
        }
    }

    public function delete(Request $request)
    {
        $this->products->deleterecord($request);
        return redirect()->back()->withErrors([Lang::get("labels.ProducthasbeendeletedMessage")]);

    }

    public function insert(Request $request)
    {
        $validator = Validator::make(
			array(
					'categories'    => $request->categories,
				),
			array(
					'categories'    => 'required',
                    // 'attributes'    => 'required|array|min:1',
                    // 'attributes.*'  => 'required'
				)
        );
        
		//check categories
		if($validator->fails()){
			return redirect()->back()->withErrors($validator)->withInput();
		}else{

        // if(isset($request->categories) and count){            
            $countAttr = 0;
            if(count($request['attributes']) > 0) {
                foreach($request['attributes'] as $attr){
                    if($attr != null){
                        $countAttr += 1;
                    }
                }
            }

            if($countAttr == 0) {
                $error = trans('labels.You Must Choose at least one in Attributes');
                return redirect()->back()->withInput($request->all())->with('error', $error);
            }
        
            $language_id = '1';
            $products_id = $this->products->insert($request);
            $result['data'] = array('products_id' => $products_id, 'language_id' => $language_id);
            $alertSetting = $this->myVaralter->newProductNotification($products_id);
            // if ($request->products_type == 1) {
            //     return redirect('/admin/products/attach/attribute/display/' . $products_id);
            // } else {
                return redirect('admin/products/images/display/' . $products_id);
            // }
            // return redirect('/admin/products/attach/attribute/display/' . $products_id);
        }
    }

    public function addinventory(Request $request)
    {
        $title = array('pageTitle' => Lang::get("labels.ProductInventory"));
        $id = $request->id;
        $result = $this->products->addinventory($id);
        
        $result['commonContent'] = $this->Setting->commonContent();
        return view("admin.products.inventory.add", $title)->with('result', $result);

    }

    public function ajax_min_max($id)
    {
        $title = array('pageTitle' => Lang::get("labels.ProductInventory"));
        $result = $this->products->ajax_min_max($id);
        return $result;

    }

    public function invoices($id)
    {
        $title = array('pageTitle' => Lang::get("labels.ProductInventory"));
        // $result = $this->products->ajax_min_max($id);
        // return $result;
        $invoices = DB::table('supplier_main')
            ->where('supplier_main.user_supplier_id', $id)
            ->select('supplier_main_id as id')
            ->get();
        return response()->json($invoices);

    }

    public function ajax_attr($id)
    {
        $title = array('pageTitle' => Lang::get("labels.ProductInventory"));
        $result = $this->products->ajax_attr($id);
        $result['commonContent'] = $this->Setting->commonContent();
        return view("admin.products.inventory.attribute_div")->with('result', $result);

    }

    public function addinventoryfromsidebar(Request $request)
    {
        $title = array('pageTitle' => Lang::get("labels.ProductInventory"));
        $result = $this->products->addinventoryfromsidebar();
        $result['commonContent'] = $this->Setting->commonContent();
        return view("admin.products.inventory.add1", $title)->with('result', $result);

    }

    public function addnewstock(Request $request)
    {

        $this->products->addnewstock($request);
        return redirect()->back()->withErrors([Lang::get("labels.inventoryaddedsuccessfully")]);

    }

    public function displaysupplier(Request $request)
    {
        $title = array('pageTitle' => Lang::get("labels.ProductSupplier"));
        $result = $this->products->displaysupplier();
        $result['commonContent'] = $this->Setting->commonContent();
        return view("admin.products.supplier.add", $title)->with('result', $result);
    }

    public function addnewsupplier(Request $request)
    {

        $this->products->addnewsupplier($request);
        return redirect()->back()->withErrors([Lang::get("labels.supplieraddedsuccessfully")]);

    }

    public function addminmax(Request $request)
    {
        //dd($request);
       $this->products->addminmax($request);
       return redirect()->back()->withErrors([Lang::get("labels.Min max level added successfully")]);

    }

    public function displayProductImages(Request $request)
    {

        $title = array('pageTitle' => Lang::get("labels.AddImages"));
        $products_id = $request->id;
        $result = $this->products->displayProductImages($request);
        $result['commonContent'] = $this->Setting->commonContent();
        return view("admin.products/images/index", $title)->with('result', $result)->with('products_id', $products_id);

    }

    public function addProductImages($products_id)
    {
        $title = array('pageTitle' => Lang::get("labels.AddImages"));
        $allimage = $this->images->getimages();
        $result = $this->products->addProductImages($products_id);
        $result['commonContent'] = $this->Setting->commonContent();
        return view('admin.products.images.add', $title)->with('result', $result)->with('products_id', $products_id)->with('allimage', $allimage);

    }

    public function insertProductImages(Request $request)
    {
        $product_id = $this->products->insertProductImages($request);
        return redirect()->back()->with('product_id', $product_id);
    }

    public function editProductImages($id)
    {

        $allimage = $this->images->getimages();
        $products_images = $this->products->editProductImages($id);
        $result['commonContent'] = $this->Setting->commonContent();
        $result['colors'] = DB::table('products_options_values')
        ->select('products_options_values_id as id', 'products_options_values_name as name')
        ->get();
        return view("admin/products/images/edit")->with('products_images', $products_images)->with('allimage', $allimage)->with('result', $result);

    }

    public function updateproductimage(Request $request)
    {

        $title = array('pageTitle' => Lang::get("labels.Manage Values"));
        $result = $this->products->updateproductimage($request);
        return redirect()->back();

    }

    public function deleteproductimagemodal(Request $request)
    {

        $products_id = $request->products_id;
        $id = $request->id;
        $result['data'] = array('products_id' => $products_id, 'id' => $id);
        $result['commonContent'] = $this->Setting->commonContent();
        return view("admin/products/images/modal/delete")->with('result', $result);

    }

    public function deleteproductimage(Request $request)
    {
        $this->products->deleteproductimage($request);
        return redirect()->back()->with('success', trans('labels.DeletedSuccessfully'));

    }

    public function addproductattribute(Request $request)
    {
        $title = array('pageTitle' => Lang::get("labels.AddAttributes"));
        $result = $this->products->addproductattribute($request);
        $result['commonContent'] = $this->Setting->commonContent();
        return view("admin.products.attribute.add", $title)->with('result', $result);
    }

    public function addnewdefaultattribute(Request $request)
    {
        $products_attributes = $this->products->addnewdefaultattribute($request);
        return ($products_attributes);
    }

    public function editdefaultattribute(Request $request)
    {
        $result = $this->products->editdefaultattribute($request);
        $result['commonContent'] = $this->Setting->commonContent();
        return view("admin/products/pop_up_forms/editdefaultattributeform")->with('result', $result);
    }

    public function updatedefaultattribute(Request $request)
    {
        $products_attributes = $this->products->updatedefaultattribute($request);
        return ($products_attributes);

    }

    public function deletedefaultattributemodal(Request $request)
    {

        $products_id = $request->products_id;
        $products_attributes_id = $request->products_attributes_id;
        $result['data'] = array('products_id' => $products_id, 'products_attributes_id' => $products_attributes_id);
        $result['commonContent'] = $this->Setting->commonContent();
        return view("admin/products/modals/deletedefaultattributemodal")->with('result', $result);

    }

    public function deletedefaultattribute(Request $request)
    {
        $products_attributes = $this->products->deletedefaultattribute($request);
        return ($products_attributes);
    }

    public function showoptions(Request $request)
    {
        $products_attributes = $this->products->showoptions($request);
        return ($products_attributes);
    }

    public function editoptionform(Request $request)
    {
        $result = $this->products->editoptionform($request);
        $result['commonContent'] = $this->Setting->commonContent();
        return view("admin/products/pop_up_forms/editproductattributeoptionform")->with('result', $result);

    }

    public function updateoption(Request $request)
    {
        $products_attributes = $this->products->updateoption($request);
        return ($products_attributes);
    }

    public function showdeletemodal(Request $request)
    {

        $products_id = $request->products_id;
        $products_attributes_id = $request->products_attributes_id;
        $result['data'] = array('products_id' => $products_id, 'products_attributes_id' => $products_attributes_id);
        $result['commonContent'] = $this->Setting->commonContent();
        return view("admin/products/modals/deleteproductattributemodal")->with('result', $result);

    }

    public function deleteoption(Request $request)
    {

        $products_attributes = $this->products->deleteoption($request);
        return ($products_attributes);

    }

    public function getOptionsValue(Request $request)
    {
        $value = $this->products->getOptionsValue($request);
        if (count($value) > 0) {
            foreach ($value as $value_data) {
                $value_name[] = "<option value='" . $value_data->products_options_values_id . "'>" . $value_data->options_values_name . "</option>";
            }
        } else {
            $value_name = "<option value=''>" . Lang::get("labels.ChooseValue") . "</option>";
        }
        print_r($value_name);
    }

    public function currentstock(Request $request)
    {

        $result = $this->products->currentstock($request);
        print_r(json_encode($result));

    }

}
