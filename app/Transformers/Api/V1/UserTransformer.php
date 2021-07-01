<?php

namespace App\Transformers\Api\V1;

use App\Transformers\Api\V1\BaseTransformer as Transformer;

class UserTransformer extends Transformer
{
    public function transform($data) : array
    {
        ini_set( 'serialize_precision', -1 );
        
        return [
            'id' 				    => $data->id,
            'name' 				    => $data->user_name,
            'first_name'            => $data->first_name,
            'last_name'             => $data->last_name,
            'gender'                => $data->gender,
            'default_address_id'    => $data->default_address_id,
            'country_code'          => $data->country_code,
            'phone'                 => $data->phone,
            'email'                 => $data->email,
            'email_activate'        => $data->email_activate,
            'active_email_token'    => $data->active_email_token,
            'avatar' 			    => $data->avatar,
            'status' 			    => $data->status,
            'status'		        => $data->status,
            'is_seen'               => $data->is_seen,
            'phone_verified'        => $data->phone_verified,
            'remember_token'        => $data->remember_token,
            'auth_id_tiwilo'	    => $data->auth_id_tiwilo,
            'date_of_birth'         => $data->dob,
            'parent_admin_id'       => $data->parent_admin_id,
            'shop_name'             => $data->shop_name,
            'token_fcm'             => $data->token,
            'provider_google_id'    => $data->provider_google_id,
            'provider_facebook_id'  => $data->provider_facebook_id,
            'provider_twitter_id'   => $data->provider_twitter_id,
            'created_at'            => $data->created_at,
            'updated_at'            => $data->updated_at
        ];
    }

}