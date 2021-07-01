<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use JWTAuth;
use App\User;

class BaseController extends Controller
{
    // === Return json response ===
    protected function respond($data, $status_code, $headers = [])
    {
        return response()->json($data, $status_code, $headers);
    }
    // === End function ===
    
    // === Generate validation error messages ===
    protected function getErrorMessage($validator)
    {
        $messages = $validator->messages();
        $errors = [];
        return $this->respond(['message' => $messages->all()[0], 'status_code' => 400], 400);
    }
    // === End function ===

    // === Validate user authorization ====
    protected function getAuthenticatedUser()
    {
        try
        {
            if (! $user =  Auth::guard('api')->user()) 
            {
                return false;//response()->json(['user_not_found'], 404);
            }
        } 
        catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e)
        {
            return response()->json(['token_expired'], $e->getStatusCode());
        }
        catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e) 
        {
            return response()->json(['token_invalid'], $e->getStatusCode());
        }
        catch (Tymon\JWTAuth\Exceptions\JWTException $e)
        {
            return response()->json(['token_absent'], $e->getStatusCode());
        }
        return $user;      
    }
    // === End function ===
}