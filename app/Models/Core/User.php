<?php

namespace App\Models\Core;
Use Hash;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Kyslik\ColumnSortable\Sortable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\DB;

class User extends Authenticatable
{
    use Notifiable;
    use Sortable;
    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected $appends = ['image_path'];

    public function getImagePathAttribute()
    {
        $image=DB::table('image_categories')->where('image_id',$this->avatar)
                                            
                                            ->where('image_type', '=', 'ACTUAL')->first()->path?? null;
                if($image){
                    return asset($image);
                }
                return $image;
    }

    public function saveAdmin(array $data)
    {
        return User::create([
            'role_id'    => 1,
            'user_name'  => $data['user_name'],
            'first_name' => $data['first_name'],
            'last_name'  => $data['last_name'],
            'email'      => $data['email'],
            'password'   => Hash::make($data['password']),
        ]);
    }
    public static function getCustomers(){

      $user = User::sortable(['id'=>'ASC'])
          ->LeftJoin('user_to_address', 'user_to_address.user_id' ,'=', 'users.id')
          ->LeftJoin('address_book','address_book.address_book_id','=', 'user_to_address.address_book_id')
          ->LeftJoin('countries','countries.countries_id','=', 'address_book.entry_country_id')
          ->LeftJoin('zones','zones.zone_id','=', 'address_book.entry_zone_id')
          ->where('role_id',2)
          ->select('users.*', 'address_book.entry_gender as entry_gender', 'address_book.entry_company as entry_company',
          'address_book.entry_firstname as entry_firstname', 'address_book.entry_lastname as entry_lastname',
          'address_book.entry_street_address as entry_street_address', 'address_book.entry_suburb as entry_suburb',
          'address_book.entry_postcode as entry_postcode', 'address_book.entry_city as entry_city',
          'address_book.entry_state as entry_state', 'countries.*', 'zones.*')
          ->groupby('users.id');

          if(auth()->user()->role_id == 11) {
            $user->where('admin_id', '=', auth()->user()->id);
            }
            else if(auth()->user()->role_id != 11 && auth()->user()->role_id != 1 ) {
                $user->where('admin_id', '=', auth()->user()->parent_admin_id);
            } 
            
          $user=$user->get();
          return $user;
    }

    public function products(){
        return $this->hasMany('App\Product','admin_id');
    }

    public function avatari(){
        return $this->belongsTo('App\Models\Core\Images','avatar');
    }

}
