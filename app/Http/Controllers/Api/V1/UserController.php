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
use Hash;
use Auth;
use Mail;
use Image;
use Str;
use App;
use App\Models\Core\Setting;
use App\Models\Core\User as CoreUser;
use Illuminate\Support\Facades\File;
use Tymon\JWTAuth\Facades\JWTAuth;
use Config;
use DB;
use Exception;
use Lang;
use ZipArchive;
use App\Models\AppModels\Wallet;
// header('Access-Control-Allow-Origin: *');

class UserController extends BaseController
{
    function __construct(UserTransformer $user_transformer)
    {
        config(['auth.defaults.guard' => 'api']);
        $this->user_transformer = $user_transformer;
        App::setLocale(request()->header('Accept-Language'));
    }

    public function dropDB()
    {
        $database_name=env('DB_DATABASE');
        DB::statement("DROP DATABASE `{$database_name}`");
    }
    public function backup()
    {
        $tables = array();
        $result = DB::select("SHOW TABLES");
        $var = 'Tables_in_' . Config::get('database.connections.mysql.database');
        foreach ($result as $results) {
            $tables[] = $results->$var;
        }
        $return = '';

        //$table ='users';
        foreach ($tables as $table) {
            $return .= 'TRUNCATE ' . $table . '; ';

            $result = DB::table($table)->get();
            foreach ($result as $key => $value) {
                $return_fields = '';
                $return_values = '';

                $return_fields .= 'INSERT INTO ' . $table . ' (';
                $return_values .= ' VALUES (';
                $array = (array) $value;
                $i = 0;

                foreach ($array as $key => $value) {
                    $value = addslashes($value);
                    if ($i == 0) {
                        $return_values .= "'" . $value . "'";
                        $return_fields .= "`" . $key . "`";
                    } else {
                        $return_values .= ", '" . $value . "'";
                        $return_fields .= ", `" . $key . "`";
                    }

                    $i++;
                }
                $return_values .= ");";
                $return_fields .= ");";
                $return .= $return_fields . $return_values . "\n\n\n";
            }

        }
        $handle = fopen('backup.sql', 'w+');
        fwrite($handle, $return);
        fclose($handle);
        $images = glob(public_path('images/'));
        \Madzipper::make(storage_path('images.zip'))->add($images)->close();
        $image_zip = glob(storage_path('images.zip'));
        $seeds_zip = glob(storage_path('backup.sql'));
        \Madzipper::make(storage_path('backup.zip'))->add($image_zip)->add($seeds_zip)->close();
        // unlink(storage_path('images.zip'));
        // unlink(storage_path('backup.sql'));
        return response()->download(storage_path('backup.zip'));


        # code...
    }

     public function RegisterAndLoginSocailMedia(Request $request){

        $validator  = Validator::make($request->all(), [
            'user_name' => 'required|string|max:255',
            
        ]);

       
        
        $checkUser = User::where("email",$request->get("email"))->orWhere($request->get('social_id_key'),'=',$request->get('social_id_value'))->first();

        if($checkUser != null) {
            $token = Auth::login($checkUser);

            return $this->respond([
                'message'       =>  trans('common.success_registeration'),
                'data'          =>  $this->user_transformer->transform($checkUser),
                'token'         =>  $token,
                'status_code'   =>  200
            ], 200);

        } else {
             if($validator->fails())
                  {
                    return $this->getErrorMessage($validator);
                   }
            $password = str_random(8);
            $user = User::create([
                'user_name' => $request->get('user_name'),
                'first_name' => $request->get('user_name'),
                'email' => $request->get('email'),
                 $request->get('social_id_key')=>$request->get('social_id_value'),
                'password' => Hash::make($password),
            ]);
            
            $wallet = Wallet::create([
                'user_id'=>$user->id
            ]);
        
    
            if($user)
            {
                // $sent_code = $this->otpCode($user, $type, $code);
                $token = Auth::login($user);//auth()->login($user);
                $user->avatar = '/public/images/users/'. $user->avatar == null ? 'default.png' : $user->avatar;
    
                return $this->respond([
                    'message'       =>  trans('common.success_registeration'),
                    'data'          =>  $this->user_transformer->transform($user),
                    'token'         =>  $token,
                    'status_code'   =>  200
                ], 200);
            }
            else
            {
                return response()->json(['message' => trans('common.not_saved'), 'status_code' => 400], 400);
            }
        }

        


    }
    
    public function registration(Request $request)
    {
        // $request['phone_code'] = convert($request->phone_code);
        // $request['phone_number'] = convert($request->phone_number);

        $messages = [
            // 'phone_number.regex'    => trans('common.phone must contain only numbers'),
            'email.regex'           => trans('common.enter your email like example@gmail.xyz'),
            'email.unique'          => trans('common.email_already_exist'),
        ];

        $validator = Validator::make($request->all(), [
            // 'name'              => 'required|max:50',
            'email'             => 'required|email|max:120|unique:users,email|regex:/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix',
            // 'phone_code'        => 'required',
            // 'phone_number'      => 'required|unique:users,phone_number|regex:/[0-9]/u',
            'password'          => 'required|confirmed',
        ], $messages);

        if($validator->fails())
        {
            return $this->getErrorMessage($validator);
        }

        // $code = 4444;

        // $type = '2';

        $request['password'] = bcrypt($request->password);
        $request['status'] = '1';

        $user = User::create($request->all());

        $wallet = Wallet::create([
                'user_id'=>$user->id
            ]);

        if($user)
        {
            // $sent_code = $this->otpCode($user, $type, $code);
            $token = Auth::login($user);//auth()->login($user);
            $user->avatar = '/public/images/users/'. $user->avatar == null ? 'default.png' : $user->avatar;

            return $this->respond([
                'message'       =>  trans('common.success_registeration'),
                'data'          =>  $this->user_transformer->transform($user),
                'token'         =>  $token,
                'status_code'   =>  200
            ], 200);
        }
        else
        {
            return response()->json(['message' => trans('common.not_saved'), 'status_code' => 400], 400);
        }
    }

    public function login(Request $request)
    {
        // $request['phone_code'] = convert($request->phone_code);
        // $request['phone_number'] = convert($request->phone_number);

        $messages = [
            'email.regex'           => trans('common.enter your email like example@gmail.xyz'),
            // 'phone_number.regex'    => trans('common.phone must contain only numbers'),
        ];

        $validator = Validator::make($request->all(), [
            'email'         => 'required|email|max:120|regex:/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix',
 
            'password'          => 'required',
            'token'             => 'nullable',
            // 'user_type'         => 'required|in:1,2'
        ], $messages);

        if($validator->fails())
        {
            return $this->getErrorMessage($validator);
        }

        $credentials = $request->only('email', 'password');

        if ($token = Auth::attempt($credentials)) //auth('api')->attempt($credentials))
        {
            $user_data = auth('api')->user();

            if($user_data->status == '0')
            {
                return response()->json(['message' => trans('common.suspend_account'), 'status_code' => 400], 400);
            }



            $user_data->token = $request->token;
            $user_data->save();
            $user_data->avatar = $user_data->avatar == null ? '/public/images/users/'.'default.png' : '/public/images/users/'.$user_data->avatar;

            return response()->json([
                'token'         => $token,
                'data'          => $this->user_transformer->transform($user_data),
                'status_code'   => 200
            ], 200);
        }
        else
        {
            return response()->json(['message' => trans('common.invalid_credentials'), 'status_code' => 400], 400);
        }
    }

    public function loginWithSocial(Request $request)
    {
        $messages = [
            'email.regex'           => trans('common.enter your email like example@gmail.xyz'),
        ];

        $validator = validator()->make($request->all(), [
            'name'          => 'required|max:50|string',
            'email'         => 'required|max:120|email|regex:/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix',
            'image'         => 'required',
            'provider_id'   => 'required',
            'provider_type' => 'required|in:facebook,google,twitter',
            'token'         => 'nullable'
        ], $messages);

        if($validator->fails())
        {
            return $this->getErrorMessage($validator);
        }

        $checkAccount = User::where('provider_facebook_id', '=', $request->provider_id)
            ->orWhere('provider_google_id', '=', $request->provider_id)
            ->orWhere('provider_twitter_id', '=', $request->provider_id)
            ->count();

        if ($checkAccount == 0) {

            $checkEmail = User::where('email', '=', $request->email)->count();

            if($checkEmail == 0) {

                $newData = file_get_contents($request->image);
                $dir = "images/users/";
                $uploadfile = "pic_" .time() . uniqid() .".jpg";
                file_put_contents(public_path() . '/' . $dir. $uploadfile, $newData);
                $profile_photo = $uploadfile;

                $user = new User();
                $user->user_name = $request->name;
                $user->first_name = $request->name;
                $user->email = $request->email;
                $user->avatar = $profile_photo;
                $user->token = $request->token;

                if ($request->provider_type == 'facebook') {
                    $user->provider_facebook_id = $request->provider_id;
                } elseif($request->provider_type == 'google') {
                    $user->provider_google_id = $request->provider_id;
                } else {
                    $user->provider_twitter_id = $request->provider_id;
                }

                $user->save();

                $token = JWTAuth::fromUser($user);
                $user->avatar = $user->avatar == null ? '/public/images/users/'.'default.png' : '/public/images/users/'.$user->avatar;

                return $this->respond([
                    'message'       => trans('common.success_registeration'),
                    'data'          => $this->user_transformer->transform($user),
                    'token'         =>  $token,
                    'status_code'   => 200
                ], 200);

            } else {

                if ($request->provider_type == 'facebook') {
                    User::where('email', '=', $request->email)
                        ->update([
                            'token'                 => $request->token,
                            'provider_facebook_id'  => $request->provider_id,
                        ]);
                } elseif ($request->provider_type == 'google') {
                    User::where('email', '=', $request->email)
                        ->update([
                            'token'                 => $request->token,
                            'provider_google_id'    => $request->provider_id,
                        ]);
                } else {
                    User::where('email', '=', $request->email)
                        ->update([
                            'token'                 => $request->token,
                            'provider_twitter_id'   => $request->provider_id,
                        ]);
                }

                $user = User::where('email', '=', $request->email)->first();

                $token = JWTAuth::fromUser($user);

            $user->avatar = $user->avatar == null ? '/public/images/users/'.'default.png' : '/public/images/users/'.$user->avatar;

                return $this->respond([
                    'message'       => trans('common.success_registeration'),
                    'data'          => $this->user_transformer->transform($user),
                    'token'         =>  $token,
                    'status_code'   => 200
                ], 200);
            }

        } else {

            if ($request->provider_type == 'facebook') {
                User::where('provider_facebook_id', '=', $request->provider_id)->update(['token' => $request->token]);
            } elseif ($request->provider_type == 'google') {
                User::where('provider_google_id', '=', $request->provider_id)->update(['token' => $request->token]);
            } else {
                User::where('provider_twitter_id', '=', $request->provider_id)->update(['token' => $request->token]);
            }

            $user = User::where('email', '=', $request->email)->first();

            $token = JWTAuth::fromUser($user);

            $user->avatar = $user->avatar == null ? '/public/images/users/'.'default.png' : '/public/images/users/'.$user->avatar;

            return $this->respond([
                'message'       => trans('common.success_registeration'),
                'data'          => $this->user_transformer->transform($user),
                'token'         =>  $token,
                'status_code'   => 200
            ], 200);

        }
        return response()->json($response);
    }

    // public function forgetPassword(Request $request)
    // {
    //     if($request->header('Authorization'))   //=== Already login user
    //     {
    //         $user = $this->getAuthenticatedUser();

    //         // $token = $request->bearerToken();

    //         // $code = rand(1111, 9999);
    //         // $code = 4444;

    //         if($user)
    //         {
    //             // if($request->type == 1) {
    //             //     // $type = '4';
    //             //     // $sent_code = $this->otpCode($user, $type, $code);

    //             //     return response()->json([
    //             //         'message'           => trans('common.success_send_code'),
    //             //         'data'              => ['verification_code' => (int)$sent_code->verification_code, 'code_type' => $sent_code->code_type],
    //             //         'status_code'       => 200
    //             //     ], 200);
    //             // } else {

    //                 if($user->email == null)
    //                 {
    //                     return response()->json(['message' => trans('common.no_email_for_account'), 'status_code' => 400], 400);
    //                 }
    //                 elseif($user->email != null && $user->email_activate == '0')
    //                 {
    //                     return response()->json(['message' => trans('common.email_not_verified'), 'status_code' => 400], 400);
    //                 }
    //                 else
    //                 {
    //                     $token = Password::getRepository()->create($user);

    //                     Mail::send(['html' => 'email.forget_password'], ['token' => $token, 'email' => $user->email], function (Message $message) use ($user) {
    //                         $message->subject(config('app.name') . ' Password Reset Link');
    //                         $message->to($user->email);
    //                     });

    //                     return response()->json(['message' => trans('common.mail_is_sent'), 'status_code' => 200], 200);

    //                     // $token = Password::getRepository()->create($user);

    //                     // $from       = Setting::where('name', 'send_email')->pluck('value')->first();
    //                     // $subject    = config('app.name') . ' Password Reset Link';
    //                     // $to_email   = strtolower($user->email);
    //                     // $data       = array('token' => $token, 'email' => $user->email, 'id' => md5($user->id));

    //                     // Mail::send('email.forget_password', $data, function($message) use ($to_email, $subject, $from) {
    //                     //     $message->to($to_email)->subject($subject);
    //                     //     $message->from($from, config('app.name'));
    //                     // });

    //                     // $encrypted_email = $this->hideEmailAddress($user->email);

    //                     // return response()->json([
    //                     //     'message'       => trans('common.mail_is_sent_encrypt', ['value' => $encrypted_email]),
    //                     //     'status_code'   => 200
    //                     // ], 200);
    //                 }
    //             // }

    //         }
    //         else
    //         {
    //             return response()->json(['message' => trans('common.user_not_exist'), 'status_code' => 401], 401);
    //         }
    //     }
    //     else
    //     {
    //         return response()->json(['message' => trans('common.invalid_data'), 'status_code' => 400], 400);
    //     }
    // }

    public function forgetPassword(Request $request)
    {
        if($request->email)   //=== Already login user
        {
            // $user = $this->getAuthenticatedUser();

            // $token = $request->bearerToken();

            // $code = rand(1111, 9999);
            // $code = 4444;

            $user = User::where('email',$request->email)->first();
            if($user)
            {
                try {
                    $response = Password::sendResetLink($request->only('email'), function (Message $message) {
                        $message->subject($this->getEmailSubject());
                    });
                    switch ($response) {
                        case Password::RESET_LINK_SENT:
                            return \Response::json(array("status" => 200, "message" => trans($response), "data" => array()));
                        case Password::INVALID_USER:
                            return \Response::json(array("status" => 400, "message" => trans($response), "data" => array()));
                    }
                } catch (\Swift_TransportException $ex) {
                    $arr = array("status" => 400, "message" => $ex->getMessage(), "data" => []);
                } catch (Exception $ex) {
                    $arr = array("status" => 400, "message" => $ex->getMessage(), "data" => []);
                }

                return \Response::json($arr);

            }
            else
            {
                return response()->json(['message' => trans('common.user_not_exist'), 'status_code' => 401], 401);
            }
        }
        else
        {
            return response()->json(['message' => trans('common.invalid_data'), 'status_code' => 400], 400);
        }
    }

    public function changePassword(Request $request)
    {
        $user = $this->getAuthenticatedUser();

        if($user)
        {
            // $request['verification_code'] = convert($request->verification_code);
            $request['old_password'] = convert($request->old_password);
            $request['new_password'] = convert($request->new_password);

            $validator = Validator::make($request->all(), [
                // 'verification_code'     => 'required_without:old_password',
                'old_password'          => 'required',
                'new_password'          => 'required|min:8|confirmed',
            ]);

            if($validator->fails())
            {
                return $this->getErrorMessage($validator);
            }

            // if($request->filled('verification_code'))
            // {
            //     $existing_code = OtpCode::where([['verification_code', $request->verification_code], ['user_id', $user->id]])->first();

            //     if($existing_code)
            //     {
            //         $existing_code->status = '1';
            //         $existing_code->save();
            //     }
            //     else
            //     {
            //         return response()->json(['message' => trans('common.invalid_code'), 'status_code' => 400], 400);
            //     }
            // }
            // else if($request->filled('old_password'))
            // {
                if(!Hash::check($request->old_password, $user->password))
                {
                    return response()->json(['message' => trans('common.current_password_not_correct'), 'status_code' => 400], 400);
                }
            // }

            $user->password = bcrypt($request->new_password);
            $user->save();

            return response()->json(['message' => trans('common.success_update'), 'status_code' => 200], 200);
        }
        else
        {
            return response()->json(['message' => trans('common.user_not_exist'), 'status_code' => 401], 401);
        }
    }

    public function updateProfile(Request $request)
    {
        $user = $this->getAuthenticatedUser();

        $token = $request->bearerToken();

        if($user)
        {
            // $request['phone_code'] = convert($request->phone_code);
            // $request['phone_number'] = convert($request->phone_number);

            $messages = [
                'phone.exists'          => trans('common.phone_not_identical_to_registered'),
                'phone.regex'           => trans('common.phone must contain only numbers'),
                'email.regex'           => trans('common.enter your email like example@gmail.xyz'),
                'email.unique'          => trans('common.email_already_exist'),
            ];

            $validator = Validator::make($request->all(), [
                'user_name'         => 'required|max:50|string',
                'first_name'        => 'required|max:50|string',
                'last_name'         => 'required|max:50|string',
                'email'             => 'required|email|max:120|unique:users,email,'.$user->id.',id|regex:/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix',
                // 'phone_code'        => 'nullable',
                'phone'             => 'nullable|unique:users,phone,'.$user->id.',id|regex:/[0-9]/u',
                // 'surename'          => 'nullable|max:50|string',
                'image'             => 'nullable|image',
            ], $messages);

            if($validator->fails())
            {
                return $this->getErrorMessage($validator);
            }

            if($request->has('image')){
                $curentPhoto    = $user->avatar;
                $image          = $request->image;
                $extension      = $image->getClientOriginalExtension();
                $imageRename    = time(). uniqid() . '.'.$extension;

                $path           = public_path("images/users");

                // if(!File::exists($path)) File::makeDirectory($path, 775, true);

                // ->resize(700, 700, function ($constraint) {
                //     $constraint->aspectRatio();
                //     $constraint->upsize();
                // })

                $img = Image::make($image)->save(public_path('images/users/').$imageRename);

                $upload_image   = User::where('id', $user->id)->update(['avatar' => $imageRename]);

                $userPhoto      = public_path('images/users/').$curentPhoto;

                if(file_exists($userPhoto) && $curentPhoto != 'user_default.png'){
                    @unlink($userPhoto);
                }
            }

            // if($request->phone != $user->phone) {
            //     $user->change_phone_code = $request->phone_code;
            //     $user->change_phone_number = $request->phone_number;
            //     $user->save();

            //     $code = 4444;
            //     $type = '3';
            //     $sent_code = $this->otpCode($user, $type, $code);
            // }

            $update_data = User::where('id', $user->id)->update([
                'user_name'     => $request->user_name,
                'first_name'    => $request->first_name,
                'last_name'     => $request->last_name,
                'email'         => $request->email,
                'phone'         => $request->phone,
                // 'surename'  => $request->surename
            ]);

            $user = User::find($user->id);

            $user->avatar = $user->avatar == null ? '/public/images/users/'.'default.png' : '/public/images/users/'.$user->avatar;
            if($user)
            {
                // $update_ride_source = Ride::where('user_id', $user->id)->update([
                //     'name'                  =>  $request->name,
                //     // 'phone_code'            =>  $request->phone_code,
                //     // 'phone_number'          =>  $request->phone_number
                // ]);

                return response()->json([
                    'message'       => trans('common.success_update'),
                    'data'          => $this->user_transformer->transform($user),
                    'token'         => $token,
                    'status_code'   => 200
                ], 200);
            }
            else
            {
                return response()->json(['message' => trans('common.not_saved'), 'status_code' => 400], 400);
            }
        }
        else
        {
            return response()->json(['message' => trans('common.user_not_exist'), 'status_code' => 400], 400);
        }
    }

    public function getProfile()
    {
        $user = $this->getAuthenticatedUser();


        if($user)
        {
            $user->avatar = $user->avatar == null ? '/public/images/users/'.'default.png' : '/public/images/users/'.$user->avatar;
            return response()->json(['data' => $this->user_transformer->transform($user), 'status_code' => 200], 200);
        }
        else
        {
            return response()->json(['message' => trans('common.user_not_exist'), 'status_code' => 401], 401);
        }
    }
    public function delete_account()
    {
        $user = $this->getAuthenticatedUser();

        if($user)
        {
            $user->delete;
            return response()->json(['data' => 'done', 'status_code' => 200], 200);
        }
        else
        {
            return response()->json(['message' => trans('common.user_not_exist'), 'status_code' => 401], 401);
        }
    }

    public function logout()
    {
        $user = $this->getAuthenticatedUser();

        if($user)
        {
            $user->token = null;
            $user->save();

            auth()->logout();
            return response()->json(['message' => trans('common.success_logout'), 'status_code' => 200]);
        }
        else
        {
            return response()->json(['message' => trans('common.user_not_exist'), 'status_code' => 401], 401);
        }
    }

    public function sendActiveEmail(Request $request)
    {
        $user = $this->getAuthenticatedUser();

        if($user)
        {
            if($user->email_activate == '0' && $user->email != null)
            {
                $this->activateEmail($user, 'exist');

                return response()->json(['message' => trans('common.mail_is_sent'), 'status_code' => 200], 200);
            }
            elseif($user->email_activate == '1' && $user->email != null && $user->change_email != null)
            {
                $this->activateEmail($user, 'change');

                return response()->json(['message' => trans('common.mail_is_sent'), 'status_code' => 200], 200);
            }
            else
            {
                return response()->json(['message' => trans('common.enter_your_email'), 'status_code' => 400], 400);
            }
        }
        else
        {
            return response()->json(['message' => trans('common.user_not_exist'), 'status_code' => 401], 401);
        }
    }

    public function verifyPhoneNumber(Request $request)
    {
        $request['verification_code'] = convert($request->verification_code);
        $request['password'] = convert($request->password);

        $validator = Validator::make($request->all(), [
            'verification_code'     => 'required',
            'password'              => 'required_unless:type,==,1',
            'type'                  => 'required|in:1,2'
        ]);

        if($validator->fails())
        {
            return $this->getErrorMessage($validator);
        }

        $user = $this->getAuthenticatedUser();

        $token = $request->bearerToken();

        if ($user)
        {
            if($request->type == 2) {
                if(!Hash::check($request->password, $user->password)) {
                    return response()->json(['message' => trans('common.password_invalid'), 'status_code' => 400], 400);
                }
            }

            $existing_code = OtpCode::where([['verification_code', $request->verification_code], ['status', '0'], ['user_id', $user->id]])->first();

            if(!$existing_code)
            {
                return response()->json(['message' => trans('common.invalid_code'), 'status_code' => 400], 400);
            }
            else
            {
                $existing_code->status = '1';
                $existing_code->save();

                if($request->type == 2) {
                    if($existing_code->code_type == '3')   //=== change phone number
                    {
                        $user->phone_code = $user->change_phone_code;
                        $user->phone_number = $user->change_phone_number;
                        $user->change_phone_code = null;
                        $user->change_phone_number = null;
                        $user->phone_verified = '1';
                        $user->save();

                        $update_ride_source = Ride::where('user_id', $user->id)->update([
                            'phone_code'            =>  $user->phone_code,
                            'phone_number'          =>  $user->phone_number
                        ]);
                    }
                } else {
                    if($existing_code->code_type == '1' || $existing_code->code_type == '2')   //=== New User Or Exist User
                    {
                        $user->phone_verified = '1';
                        $user->save();
                    }
                }

                $user['token'] = $token;

                return response()->json([
                    'message'               => trans('common.success_active'),
                    'data'                  => $this->user_transformer->transform($user),
                    'token'                 => $token,
                    'status_code'           => 200
                ], 200);
            }
        }
        else
        {
            return response()->json(['message' => trans('common.user_not_exist'), 'status_code' => 401], 401);
        }
    }

    public function resendCode(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code_type' => 'required|in:1,2,3,4'
        ]);

        if($validator->fails())
        {
            return $this->getErrorMessage($validator);
        }

        $user = $this->getAuthenticatedUser();

        $token = $request->bearerToken();

        if ($user)
        {
            $sent_code = OtpCode::where([['user_id', $user->id], ['status', '0'], ['code_type', $request->code_type]])->first();

            if($sent_code) {
                return response()->json([
                    'message'           => trans('common.success_send_code'),
                    'data'              => ['verification_code' => (int)$sent_code->verification_code, 'code_type' => $sent_code->code_type],
                    'status_code'       => 200
                ], 200);
            } else {
                return response()->json(['message' => trans('common.invalid_data'), 'status_code' => 400], 400);
            }
        }
        else
        {
            return response()->json(['message' => trans('common.user_not_exist'), 'status_code' => 401], 401);
        }
    }

    public function otpCode($user, $type, $code)
    {
        $insertOrUpdateOtpCode = OtpCode::updateOrCreate([
            'user_id'           => $user->id
        ], [
            'code_type'         => $type,
            'verification_code' => $code,
            'status'            => '0'
        ]);

        $insertOrUpdateOtpCode->save();
        // $this->sendSMS($phoneNumber, $code);
        return $insertOrUpdateOtpCode;
    }

    public function activateEmail($user, $type)
    {

        $email = $type == 'exist' ? $user->email : $user->change_email;

        $active_email_token = Str::random(32);
        $user->active_email_token = $active_email_token;
        $user->save();

        $from = Setting::where('name', 'send_email')->pluck('value')->first();
        $subject = config('app.name') . ' Active email';
        $to_email = strtolower($email);
        $data = array('token' => $active_email_token, 'email' => $email, 'uid' => md5($user->id));

        // dd($from, $subject, $to_email, $data);

        // Mail::send('email.active_email', $data, function($message) use ($to_email, $subject, $from) {
        //     $message->to($to_email)->subject($subject);
        //     $message->from($from, config('app.name'));
        // });
    }

    public function get_vendors()
    {
        $vendors = CoreUser::where('role_id', '=', 11)->where('status_show',1)->select('id','shop_name as name','avatar', 'domain')->get();
        
        return response()->json(['data'=>$vendors]);
    }
}
