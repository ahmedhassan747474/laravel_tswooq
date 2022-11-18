<?php

namespace App\Models\AppModels;

use DB;
use Illuminate\Database\Eloquent\Model;
use Auth;
use Illuminate\Foundation\Auth\ThrottlesLogins;

class Wallet extends Model
{

    
    protected $table = 'wallet';

    protected $fillable = ['id','user_id'];

}
