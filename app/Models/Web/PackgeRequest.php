<?php
namespace App\Models\Web;
use http\Env\Request;
use Illuminate\Database\Eloquent\Model;
use DB;
use App\Models\Core\Categories;
use App\Models\Web\Cart;
use Illuminate\Support\Facades\Lang;
use Session;
class PackgeRequest extends Model{
  protected $table = 'packge_requests';
  protected $guarded  = [];

  public function user(){
    return $this->belongsTo('App\Models\Core\User','user_id');
  }
  
  public function packge(){
    return $this->belongsTo('App\Models\Web\package','packge_id');
  }
}






