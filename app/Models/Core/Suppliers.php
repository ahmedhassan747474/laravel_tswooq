<?php

namespace App\Models\Core;

use http\Env\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use PhpParser\Node\Stmt\Return_;
use Kyslik\ColumnSortable\Sortable;
use App\Models\Core\User;
use App\Models\Core\Setting;
use App\Http\Controllers\AdminControllers\SiteSettingController;

class Suppliers extends Model
{
    //
    use Sortable;
    protected $table = 'customers';
    public function address_book(){

        return $this->belongsTo('App\address_book');
    }

    public function customer_info(){
        return $this->belongsTo('App\Customer_info');
    }

    public function countryrelation(){
        return $this->belongsTo('App\Country');
    }

    public function zone(){
        return $this->belongsTo('App\Zone');
    }

    public function images(){
        return $this->belongsTo('App\Images');
    }

    public $sortableAs = ['entry_street_address','entry_firstname','entry_company'];
    public $sortable = ['id', 'gender', 'first_name','last_name','dob','email','phone','status','created_at','updated_at','entry_street_address'];

    public function getter(){

        $suppliers = DB::table('user_supplier')
            ->select('user_supplier.*')
            ->orderBy('id', 'desc')
            ->get();

        return $suppliers;
    }

    public function paginator(){
        $suppliers = DB::table('user_supplier')
            ->select('user_supplier.*')
            ->orderBy('id', 'desc')
            ->paginate(10);

        return $suppliers;
    }

    public function insert($request){
    
        $supplier_id = DB::table('user_supplier')->insertGetId([
            'name'          => $request->name,
            'phone'         => $request->phone,
            'description'   => $request->description,
            'status'        => $request->isActive,
            'created_at'    => date('Y-m-d H:i:s'),
        ]);
        return $supplier_id;
    }

    public function  country(){
        $countries = DB::table('countries')->get();
        return $countries;
    }

    public function countries(){
        $countries = DB::table('countries')->get();
        return $countries;
    }

    public function edit($id){
      $suppliers = DB::table('user_supplier')->where('id','=', $id)->first();
      return $suppliers;
    }

    public function updaterecord($supplier_id,$user_data){
      DB::table('user_supplier')->where('id', '=', $supplier_id)->update($user_data);
    }

    public function destroyrecord($supplier_id){
      DB::table('user_supplier')->where('id','=', $supplier_id)->delete();
    }
}
