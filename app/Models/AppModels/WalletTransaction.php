<?php

namespace App\Models\AppModels;

use DB;
use Illuminate\Database\Eloquent\Model;
use Auth;
use Illuminate\Foundation\Auth\ThrottlesLogins;
class WalletTransaction extends Model
{

  
   protected $table = 'wallet_transaction';

   protected $fillable = ['id','user_id','wallet_id','amount','transaction_type'];

}
