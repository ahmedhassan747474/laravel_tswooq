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

    public function index(Request $request){
		// dd($request->all());
        // $curl = curl_init();

        // curl_setopt_array($curl, array(
        //     CURLOPT_URL => "https://taxes.like4app.com/online/check_balance/",
        //     CURLOPT_RETURNTRANSFER => true,
        //     CURLOPT_ENCODING => "",
        //     CURLOPT_MAXREDIRS => 10,
        //     CURLOPT_TIMEOUT => 0,
        //     CURLOPT_FOLLOWLOCATION => true,
        //     CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        //     CURLOPT_CUSTOMREQUEST => "POST",
        //     CURLOPT_POSTFIELDS => array(
        //         'deviceId' => '5b3e7f9bfb09c2a60d835794282f589d2fc4bfa89cc093c574ee76126dbc0b86',
        //         'email' => 'Jaber2800@hotmail.com',
        //         'password' => '24c15fa2d4b862880536374e53f1c4fe',
        //         'securityCode' => '9a328e9f300dfd45f54e48c12df75363',
        //         'langId' => '1',
        //         // 'ids[]' => '693'
        //     ),
        //     CURLOPT_HTTPHEADER => array(
        //         // "Content-Type: application/x-www-form-urlencoded",
        //         // "Cookie: __cfduid=d4da87b29456a5d8b9d1b8acf7e5f8f991620294720;like4card=6391a8a977658112567a5bc3549a4750f2f17ace"
        //     ),
        // ));

        // $response = curl_exec($curl);

        // curl_close($curl);
        // dd($response);
        // echo $response;

		$language_id = Session::get('language_id') ? Session::get('language_id') : 1;

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://taxes.like4app.com/online/categories",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => array(
                'deviceId' => '5b3e7f9bfb09c2a60d835794282f589d2fc4bfa89cc093c574ee76126dbc0b86',
                'email' => 'Jaber2800@hotmail.com',
                'password' => '24c15fa2d4b862880536374e53f1c4fe',
                'securityCode' => '9a328e9f300dfd45f54e48c12df75363',
                'langId' => $language_id,
                // 'ids[]' => '693'
            ),
            CURLOPT_HTTPHEADER => array(
                // "Content-Type: application/x-www-form-urlencoded"
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        // dd($response);
        // echo $response;
        $categories = json_decode($response);
        // return response()->json($response);
        // dd($categories->data);

        $result = array();

        
        $result['categories'] = $categories;
        // dd($result['categories']->response);
        $category_id = $request->category_id;
        $curl2 = curl_init();

        curl_setopt_array($curl2, array(
            CURLOPT_URL => "https://taxes.like4app.com/online/products",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => array(
                'deviceId' => '5b3e7f9bfb09c2a60d835794282f589d2fc4bfa89cc093c574ee76126dbc0b86',
                'email' => 'Jaber2800@hotmail.com',
                'password' => '24c15fa2d4b862880536374e53f1c4fe',
                'securityCode' => '9a328e9f300dfd45f54e48c12df75363',
                'langId' => $language_id,
				'categoryId' => $category_id ? $category_id : $categories->data[0]->childs[0]->id,
               //  'ids[]' => '362'
            ),
            CURLOPT_HTTPHEADER => array(
                // "Content-Type: application/x-www-form-urlencoded"
            ),
        ));

        $response2 = curl_exec($curl2);

        curl_close($curl2);

        $response2 = json_decode($response2);
        $result['products'] = $response2;
        // dd($response2);
        $title = array('pageTitle' => Lang::get('website.Shop'));

        $result['commonContent'] = $this->index->commonContent();
        $final_theme = $this->theme->theme();

        return view("web.like_card.show_index", ['title' => $title, 'final_theme' => $final_theme])
        ->with('result', $result);
    }

    public function index_one(){

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
                'deviceId' => '5b3e7f9bfb09c2a60d835794282f589d2fc4bfa89cc093c574ee76126dbc0b86',
                'email' => 'Jaber2800@hotmail.com',
                'password' => '24c15fa2d4b862880536374e53f1c4fe',
                'securityCode' => '9a328e9f300dfd45f54e48c12df75363',
                'langId' => '1',
				    'categoryId' => '362',
               //  'ids[]' => '362'
            ),
            CURLOPT_HTTPHEADER => array(
                // "Content-Type: application/x-www-form-urlencoded"
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        // dd($response);
        // echo $response;
        $response = json_decode($response);
        return response()->json($response);
    }
 public function index_two(){

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://taxes.like4app.com/online/categories",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => array(
                'deviceId' => '5b3e7f9bfb09c2a60d835794282f589d2fc4bfa89cc093c574ee76126dbc0b86',
                'email' => 'Jaber2800@hotmail.com',
                'password' => '24c15fa2d4b862880536374e53f1c4fe',
                'securityCode' => '9a328e9f300dfd45f54e48c12df75363',
                'langId' => '1',
                // 'ids[]' => '693'
            ),
            CURLOPT_HTTPHEADER => array(
                // "Content-Type: application/x-www-form-urlencoded"
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        // dd($response);
        // echo $response;
        $response = json_decode($response);
        return response()->json($response);
    }

    public function index_balance(){
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://taxes.like4app.com/online/check_balance/",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => array(
                'deviceId' => '5b3e7f9bfb09c2a60d835794282f589d2fc4bfa89cc093c574ee76126dbc0b86',
                'email' => 'Jaber2800@hotmail.com',
                'password' => '24c15fa2d4b862880536374e53f1c4fe',
                'securityCode' => '9a328e9f300dfd45f54e48c12df75363',
                'langId' => '1',
                // 'ids[]' => '693'
            ),
            CURLOPT_HTTPHEADER => array(
                // "Content-Type: application/x-www-form-urlencoded",
                // "Cookie: __cfduid=d4da87b29456a5d8b9d1b8acf7e5f8f991620294720;like4card=6391a8a977658112567a5bc3549a4750f2f17ace"
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        $response = json_decode($response);
        
        return response()->json($response);
    }

    public function filters($data)
    {
        $response = $this->products->filters($data);
        return ($response);
    }
}