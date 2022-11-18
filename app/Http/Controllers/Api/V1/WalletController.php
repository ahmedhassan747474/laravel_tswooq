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
use App\Models\AppModels\WalletTransaction;
use App\Models\AppModels\Wallet;


class WalletController extends BaseController
{
   

    


    public function add_to_wallet(Request $request)
    {
        $validator = Validator::make($request->all(), [
           
            'amount' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 403);
        }

     
         $user = $this->getAuthenticatedUser();
       
        if($user == null) {
             return response()->json(['errors' => "الرجاء تسجيل الدخول"], 401);
        }

    
        $wallet = Wallet::where('user_id',$user->id)->first();
        
        if($wallet == null) {
            return response()->json(['errors' => "ليس لديك محفظة"], 404);
        }
        $wallet_id = $wallet->id;
        $wallet_transaction = WalletTransaction::create([
            'wallet_id'=>$wallet_id,
            'amount'=>$request->get("amount"),
            'transaction_type' => '1'
        ]);

      

        return response()->json(['message' => 'add to wallet'], 200);
    }


    

    public function get_information_wallet(Request $request)
    {
    

        $user = $this->getAuthenticatedUser();
       
        if($user == null) {
             return response()->json(['errors' => "الرجاء تسجيل الدخول"], 401);
        }

        $wallet = Wallet::where('user_id',$user->id)->first();
         
         if($wallet == null) {
              $wallet = Wallet::create([
                'user_id'=>$user->id
            ]);
         }
        
        if($wallet == null) {
            return response()->json(['errors' => "ليس لديك محفظة"], 404);
        }
        $wallet_id = $wallet->id;

        

        $wallet = WalletTransaction::where('wallet_id',$wallet_id)->get();
        $wallet_total = WalletTransaction::where('wallet_id',$wallet_id)->where('transaction_type','1')->sum('amount');
        $pull_wallet_total = WalletTransaction::where('wallet_id',$wallet_id)->where('transaction_type','2')->sum('amount');
        $rest_wallet_total = $wallet_total-$pull_wallet_total;
         
        return response()->json(['message' => 'Information wallet',"wallet_transaction"=>$wallet,"total"=>$wallet_total,"rest_wallet_total"=>$rest_wallet_total,"pull_wallet_total"=>$pull_wallet_total], 200);
    }


    public function pull_from_wallet(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 403);
        }

        $user = $this->getAuthenticatedUser();
       
        if($user == null) {
             return response()->json(['errors' => "الرجاء تسجيل الدخول"], 401);
        }

        $wallet = Wallet::where('user_id',$user->id)->first();
        
        if($wallet == null) {
            return response()->json(['errors' => "ليس لديك محفظة"], 404);
        }
        $wallet_id = $wallet->id;

        $wallet_transaction = WalletTransaction::create([
            'wallet_id'=>$wallet_id,
            'amount'=>$request->get("amount"),
            'transaction_type' => '2'
        ]);

      

        return response()->json(['message' => 'pull from wallet'], 200);
    }
     
}
