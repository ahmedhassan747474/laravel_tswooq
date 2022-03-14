<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Access\Response;
use Illuminate\Support\Facades\Gate;

function convert($string) 
{
    $arabic = ['٠','١','٢','٣','٤','٥','٦','٧','٨','٩',','];
    $num = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9', '.'];
    $englishNumbersOnly = str_replace($arabic, $num, $string);

    return $englishNumbersOnly;
}

function checkAttribute($attributes, $attrId, $type)
{
	foreach ($attributes as $attribute) {
		if($type == 'edit'){
			if($attribute->attribute_id == $attrId){
				return 'checked';
			}
		} else {
			if($attribute->id == $attrId){
				return 'checked';
			}
		}
	}
}

function checkPermission($page)
{
	$user = auth()->guard('admin')->user();
	if($user->type == '2') {
		if($user->adminPermission->permission->$page == 0) {
			return abort(403);
		}
	}
}

function checkGate($gate)
{
	$user = auth()->guard('admin')->user();
	if($user->type == '2') {
		if($user->adminPermission->$gate == 0) {
			return abort(403);
		}
	}
}
function getOrders($email)
{
         $curl = curl_init();
        curl_setopt_array($curl, array(
          CURLOPT_URL => "https://taxes.like4app.com/online/orders",
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => array(
                'deviceId' => '5b3e7f9bfb09c2a60d835794282f589d2fc4bfa89cc093c574ee76126dbc0b86',
                'email' => 'Jaber2800@hotmail.com',
                'password' => 'c0bf116b36be1ec7d90bf6a520c1c350',
                'securityCode' => '9a328e9f300dfd45f54e48c12df75363',
                'langId' => '1',
            ),
          CURLOPT_HTTPHEADER => array(
            // "Content-Type: application/x-www-form-urlencoded"
          ),
        ));
        
        $response = curl_exec($curl);
        
        curl_close($curl);
        return json_decode($response);

}
function deatailsOrder($orderId){
    
$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => "https://taxes.like4app.com/online/orders/details",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "POST",
  CURLOPT_POSTFIELDS => array(
   'deviceId' => '5b3e7f9bfb09c2a60d835794282f589d2fc4bfa89cc093c574ee76126dbc0b86',
        'email' => 'Jaber2800@hotmail.com',
        'password' => 'c0bf116b36be1ec7d90bf6a520c1c350',
        'securityCode' => '9a328e9f300dfd45f54e48c12df75363',
    'langId' => '1',
    'orderId' => $orderId
  ),
  CURLOPT_HTTPHEADER => array(
    // "Content-Type: application/x-www-form-urlencoded"
  ),
));

$response = curl_exec($curl);

curl_close($curl);
  return json_decode($response);
}
function createOrders($product_id)
{
     $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => "https://taxes.like4app.com/online/create_order",
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => "POST",
    CURLOPT_POSTFIELDS => array(
        'deviceId' => '5b3e7f9bfb09c2a60d835794282f589d2fc4bfa89cc093c574ee76126dbc0b86',
        'email' => 'Jaber2800@hotmail.com',
        'password' => 'c0bf116b36be1ec7d90bf6a520c1c350',
        'securityCode' => '9a328e9f300dfd45f54e48c12df75363',
        'langId' => '1',
       'productId' => $product_id,
        'referenceId' => 'Merchant_12467'
    ),
  CURLOPT_HTTPHEADER => array(
    // "Content-Type: application/x-www-form-urlencoded"
  ),
));

    $response = curl_exec($curl);
    curl_close($curl);
    
    return json_decode($response);

}

function checkPermit($page)
{
	$user = auth()->guard('admin')->user();
	if($user->type == '2') {
		$condition = Auth::guard('admin')->user()->adminPermission->permission->$page == 0 ? false : true;
	} else {
		$condition = true;
	}
	
	return $condition;
}

function setMenu($path)
{
	return Request::is('admin/' . $path . '*') ? 'sidebar-group-active open' :  '';
}

function setShown($path)
{
	return Request::is('admin/' . $path . '*') ? 'is-shown' :  '';
}

function setActive($path)
{
	return Request::is('admin/' . $path . '*') ? 'active' :  '';
}

function setActiveParameter($path)
{
	return request()->fullUrl() == $path ? 'active' :  '';
}