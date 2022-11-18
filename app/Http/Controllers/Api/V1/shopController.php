<?php
namespace App\Http\Controllers\Api\V1;

use App\Group;
use App\Http\Controllers\Api\V1\BaseController;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\ShopCollection;
use App\Models\AppModels\Product;
use App\Models\Core\User;
use App\Models\Web\Currency;
use App\Models\Web\Index;
use App\Models\Web\Languages;
use App\Models\Web\News;
use App\Models\Web\Order;
use App\Models\Web\Products;
use App\Models\Web\package;
use App\Models\Web\PackgeRequest;
use App\Transformers\Api\V1\UserTransformer;
use Lang;
use DB;
use Carbon;
use Essam\TapPayment\Payment;
use Illuminate\Support\Facades\App;

class shopController extends BaseController
{

    function __construct(UserTransformer $user_transformer)
    {
        config(['auth.defaults.guard' => 'api']);
        $this->user_transformer = $user_transformer;
        App::setLocale(request()->header('Accept-Language'));
    }
    
	public Function getShops(){
        $users = User::with(['products','avatari'])->where('shop_name','!=',null)->get();
        // return response()->json($users);
        return new ShopCollection($users);
	}


	/// get 

	public Function Become_merchant_with_us(){      
		// $users = User::with('products')->where('shop_name','!=',null)->get();
        $packges = package::get();
         return response()->json(['data'=>$packges]);
	}

    public function checkout(Request $request){
        $user = $this->getAuthenticatedUser();
          
        if($user)
        {
            $package=package::find($request->package_id);
            if($package){

                $req= new PackgeRequest();
                $req->user_id = $user->id;
                $req->name_user = $user->first_name;
                $req->name = $package->name;
                $req->packge_id = $request->package_id;
                $req->price = $request->price;
                $req->month = $request->month;
                $req->commercialRegisterNumber = $request->commercialRegisterNumber;
                $req->subdomain = $request->subdomain;
                $req->bankAccount = $request->bankAccount;
                $req->taxNumber = $request->taxNumber;
                $req->address = $request->address;
                    //commercialRegisterNumber
                    //           subdomain
                    //           bankAccount
                    //           taxNumber
                    //           address
                $req->save();
                $TapPay = new Payment(['secret_api_Key'=> 'sk_test_XKokBfNWv6FIYuTMg5sLPjhJ']);

                $redirect = false; // return response as json , you can use it form mobile web view application

                $re= $TapPay->charge([
                        'amount' => $request->price,
                        'currency' => 'SAR',
                        'threeDSecure' => 'true',
                        'description' => 'test description',
                        'statement_descriptor' => 'sample',
                        'customer' => [
                        'first_name' => $user->first_name,
                        'email' => $user->email,
                        ],
                        'source' => [
                        'id' => 'src_card'
                        ],
                        'post' => [
                        'url' => null
                        ],
                        'redirect' => [
                        'url' => route('payment_success',$req->id)
                        ]
                ],$redirect);
                
                return response()->json($re);
            }else{
                return response()->json(['message'=>'Package Not found','data'=>[]],400);
            }
            
            
        }
        else
        {
            return response()->json(['message' => trans('common.user_not_exist'), 'status_code' => 401], 401);
        }

    }
}

?>