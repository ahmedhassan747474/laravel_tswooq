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