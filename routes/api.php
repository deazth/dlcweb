<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('login', function() {
	return 'lala';
});

// Authentication related APIs
Route::get('authcon', 'LdapAuthController@authcon');
Route::get('getuserinfo', 'LdapAuthController@getUserInfo');
Route::get('LoginApi', 'LdapAuthController@doLogin');

// User activities
Route::get('FindDeviceBySerial', 'DeviceController@FindDeviceBySerialApi');
