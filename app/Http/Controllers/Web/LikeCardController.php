<?php
namespace App\Http\Controllers\Web;

//validator is builtin class in laravel
use App\Models\Web\Currency;
use App\Models\Web\Index;
//for password encryption or hash protected
use App\Models\Web\Languages;

//for authenitcate login data
use App\Models\Web\Products;
use Auth;

//for requesting a value
use DB;
//for Carbon a value
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Lang;
use Session;
//email

class LikeCardController extends Controller
{
    public function __construct(Index $index,Languages $languages,Products $products,Currency $currency) {
        $this->index = $index;
        $this->languages = $languages;
        $this->products = $products;
        $this->currencies = $currency;
        $this->theme = new ThemeController();
    }

    public function index(){

        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => "https://taxes.like4app.com/online/products",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => array(
            'deviceId' => '***************************',
            'email' => 'jaber2800@hotmail.com',
            'password' => 'Jab2800er',
            'securityCode' => '***************************',
            'langId' => '1',
            'ids[]' => '693'
        ),
        CURLOPT_HTTPHEADER => array(
            "Content-Type: application/x-www-form-urlencoded"
        ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        dd($response);
        echo $response;


        $title = array('pageTitle' => Lang::get('website.Shop'));
        $result = array();

        $result['commonContent'] = $this->index->commonContent();
        $final_theme = $this->theme->theme();
        if (!empty($request->page)) {
            $page_number = $request->page;
        } else {
            $page_number = 0;
        }

        if (!empty($request->limit)) {
            $limit = $request->limit;
        } else {
            $limit = 15;
        }

        if (!empty($request->type)) {
            $type = $request->type;
        } else {
            $type = '';
        }

        //min_max_price
        if (!empty($request->price)) {
            $d = explode(";", $request->price);
            $min_price = $d[0];
            $max_price = $d[1];
        } else {
            $min_price = '';
            $max_price = '';
        }
        $exist_category = '1';
        $categories_status = 1;
        //category
        if (!empty($request->category) and $request->category != 'all') {
            if(!empty($request->brand) and $request->brand != 'all') {
                $brand = $this->products->getBrands($request);
                
                if(!empty($brand) and count($brand)>0){
                    $categories_id = $brand[0]->categories_id;
                    //for main
                    if ($brand[0]->parent_id > 0) {
                        $category_name = $brand[0]->categories_name;
                        $sub_category_name = '';
                        $category_slug = '';
                        $categories_status = $brand[0]->categories_status;
                    }
                }else{
                    $categories_id = '';
                    $category_name = '';
                    $sub_category_name = '';
                    $category_slug = '';
                    $categories_status = 0;
                }
            } else {
                $category = $this->products->getCategories($request);
                
                if(!empty($category) and count($category)>0){
                    $categories_id = $category[0]->categories_id;
                    //for main
                    if ($category[0]->parent_id == 0) {
                        $category_name = $category[0]->categories_name;
                        $sub_category_name = '';
                        $category_slug = '';
                        $categories_status = $category[0]->categories_status;
                    } else {
                        //for sub
                        $main_category = $this->products->getMainCategories($category[0]->parent_id);

                        $category_slug = $main_category[0]->categories_slug;
                        $category_name = $main_category[0]->categories_name;
                        $sub_category_name = $category[0]->categories_name;
                        $categories_status = $category[0]->categories_status;
                    }
                }else{
                    $categories_id = '';
                    $category_name = '';
                    $sub_category_name = '';
                    $category_slug = '';
                    $categories_status = 0;
                }
            }

        } else {
            $categories_id = '';
            $category_name = '';
            $sub_category_name = '';
            $category_slug = '';
            $categories_status = 1;
        }

        $result['category_name'] = $category_name;
        $result['category_slug'] = $category_slug;
        $result['sub_category_name'] = $sub_category_name;
        $result['categories_status'] = $categories_status;

        //search value
        if (!empty($request->search)) {
            $search = $request->search;
        } else {
            $search = '';
        }

        $filters = array();
        if (!empty($request->filters_applied) and $request->filters_applied == 1) {
            $index = 0;
            $options = array();
            $option_values = array();

            $option = $this->products->getOptions();

            foreach ($option as $key => $options_data) {
                $option_name = str_replace(' ', '_', $options_data->products_options_name);

                if (!empty($request->$option_name)) {
                    $index2 = 0;
                    $values = array();
                    foreach ($request->$option_name as $value) {
                        $value = $this->products->getOptionsValues($value);
                        $option_values[] = $value[0]->products_options_values_id;
                    }
                    $options[] = $options_data->products_options_id;
                }
            }

            $filters['options_count'] = count($options);

            $filters['options'] = implode($options, ',');
            $filters['option_value'] = implode($option_values, ',');

            $filters['filter_attribute']['options'] = $options;
            $filters['filter_attribute']['option_values'] = $option_values;

            $result['filter_attribute']['options'] = $options;
            $result['filter_attribute']['option_values'] = $option_values;
        }

        $data = array('page_number' => $page_number, 'type' => $type, 'limit' => $limit,
            'categories_id' => $categories_id, 'search' => $search,
            'filters' => $filters, 'limit' => $limit, 'min_price' => $min_price, 'max_price' => $max_price);

        $products = $this->products->products($data);        
        $result['products'] = $products;

        $data = array('limit' => $limit, 'categories_id' => $categories_id);
        $filters = $this->filters($data);
        $result['filters'] = $filters;

        $cart = '';
        $result['cartArray'] = $this->products->cartIdArray($cart);

        if ($limit > $result['products']['total_record']) {
            $result['limit'] = $result['products']['total_record'];
        } else {
            $result['limit'] = $limit;
        }

        //liked products
        $result['liked_products'] = $this->products->likedProducts();
        $result['categories'] = $this->products->categories();

        $result['min_price'] = $min_price;
        $result['max_price'] = $max_price;

        return view("web.like_card.show_index", ['title' => $title, 'final_theme' => $final_theme])->with('result', $result);
    }

    public function filters($data)
    {
        $response = $this->products->filters($data);
        return ($response);
    }
}