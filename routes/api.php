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
/*
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
*/

$api = app('Dingo\Api\Routing\Router');

$api->version('v1', function ($api) {

    $api->get('/', ['uses' => 'App\Http\Controllers\BaseController@returnIndex']);

    // auth APIs
    $api->get('/authcon', ['uses' => 'App\Api\V1\Controllers\LdapAuthController@authcon']);
    $api->get('/getuserinfo', ['uses' => 'App\Api\V1\Controllers\LdapAuthController@getUserInfo']);
    $api->post('/getuserinfo', ['uses' => 'App\Api\V1\Controllers\LdapAuthController@getUserInfo']);
    $api->get('/LoginApi', ['uses' => 'App\Api\V1\Controllers\LdapAuthController@doLogin']);
    $api->post('/LoginApi', ['uses' => 'App\Api\V1\Controllers\LdapAuthController@doLogin']);

    // Device APIs
    $api->get('/FindDeviceBySerial', ['uses' => 'App\Api\V1\Controllers\DeviceController@FindDeviceBySerialApi']);
    $api->post('/FindDeviceBySerial', ['uses' => 'App\Api\V1\Controllers\DeviceController@FindDeviceBySerialApi']);
    $api->get('/QueryAllDeviceDlcmAPI', ['uses' => 'App\Api\V1\Controllers\DeviceController@queryAllDeviceDlcm']);
    $api->get('/QueryAllDevicePlcmAPI', ['uses' => 'App\Api\V1\Controllers\DeviceController@queryAllDevicePlcm']);

    // Store APIs
    $api->post('/UpdateStoreAPI', ['uses' => 'App\Api\V1\Controllers\StoreController@UpdateStoreAPI']);
    $api->post('/AddStoreAPI', ['uses' => 'App\Api\V1\Controllers\StoreController@AddStoreAPI']);
});
