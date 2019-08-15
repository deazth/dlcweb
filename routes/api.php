<?php

use Illuminate\Http\Request;


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
    $api->post('/FindDeviceByKey', ['uses' => 'App\Api\V1\Controllers\DeviceController@FindDeviceByKeyApi']);
    $api->post('/FindDeviceByStaff', ['uses' => 'App\Api\V1\Controllers\DeviceController@FindDeviceByStaffApi']);
    $api->get('/QueryAllDeviceDlcmAPI', ['uses' => 'App\Api\V1\Controllers\DeviceController@queryAllDeviceDlcm']);
    $api->get('/QueryAllDevicePlcmAPI', ['uses' => 'App\Api\V1\Controllers\DeviceController@queryAllDevicePlcm']);
    $api->POST('/CheckDeviceWarranty', ['uses' => 'App\Api\V1\Controllers\DeviceController@checkDeviceWarranty']);
    $api->POST('/ReturnDeviceReq', ['uses' => 'App\Api\V1\Controllers\DeviceController@returnDeviceReq']);
    $api->POST('/ListDeviceByBC', ['uses' => 'App\Api\V1\Controllers\DeviceController@listDeviceByBC']);
    $api->POST('/DeviceChangeOwner', ['uses' => 'App\Api\V1\Controllers\DeviceController@DeviceChangeOwner']);
    $api->POST('/BuyDeviceReq', ['uses' => 'App\Api\V1\Controllers\DeviceController@BuyDeviceReq']);
    $api->POST('/DeviceReportLost', ['uses' => 'App\Api\V1\Controllers\DeviceController@DeviceReportLost']);

    $api->GET('/GetDeviceStatus', ['uses' => 'App\Api\V1\Controllers\DeviceController@getDlcmDeviceStatus']);


    // Store APIs
    $api->post('/UpdateStoreAPI', ['uses' => 'App\Api\V1\Controllers\StoreController@UpdateStoreAPI']);
    $api->post('/AddStoreAPI', ['uses' => 'App\Api\V1\Controllers\StoreController@AddStoreAPI']);
    $api->get('/ListStoreAPI', ['uses' => 'App\Api\V1\Controllers\StoreController@ListStoreAPI']);


    // admin Role
    $api->post('/RoleCreate', ['uses' => 'App\Api\V1\Controllers\RoleController@createRole']);
    $api->post('/RoleEdit', ['uses' => 'App\Api\V1\Controllers\RoleController@createRole']);
    $api->post('/RoleDelete', ['uses' => 'App\Api\V1\Controllers\RoleController@deleteRole']);
    $api->get('/RoleList', ['uses' => 'App\Api\V1\Controllers\RoleController@listRole']);

    $api->post('/OrderAdminApprove', ['uses' => 'App\Api\V1\Controllers\AdminController@OrderAdminApprove']);
    $api->post('/OrderAdminReject', ['uses' => 'App\Api\V1\Controllers\AdminController@OrderAdminReject']);
    $api->get('/OrderPendingAD', ['uses' => 'App\Api\V1\Controllers\AdminController@OrderPendingAD']);
    $api->get('/OrderPendingPAY', ['uses' => 'App\Api\V1\Controllers\AdminController@OrderPendingPAY']);
    $api->get('/OrderPendingDC', ['uses' => 'App\Api\V1\Controllers\AdminController@OrderPendingDC']);
    $api->post('/OrderReceivePayment', ['uses' => 'App\Api\V1\Controllers\AdminController@OrderReceivePayment']);

    $api->post('/DeviceDirectInactive', ['uses' => 'App\Api\V1\Controllers\DeviceController@directInactiveDevice']);

    // BC management
    $api->post('/BcCreate', ['uses' => 'App\Api\V1\Controllers\BcController@createBc']);
    $api->post('/BcEdit', ['uses' => 'App\Api\V1\Controllers\BcController@createBc']);
    $api->post('/BcDelete', ['uses' => 'App\Api\V1\Controllers\BcController@deleteBc']);
    $api->get('/BcList', ['uses' => 'App\Api\V1\Controllers\BcController@listBc']);
    $api->post('/BcGetCC', ['uses' => 'App\Api\V1\Controllers\BcController@listBcCostCenter']);

    // Order APIs
    $api->post('/OrderUpdateUserForm', ['uses' => 'App\Api\V1\Controllers\OrderController@updateUserReqForm']);
    $api->post('/OrderGetUserForm', ['uses' => 'App\Api\V1\Controllers\OrderController@getUserReqForm']);
    $api->post('/QueryStaffOrderAPI', ['uses' => 'App\Api\V1\Controllers\OrderController@QueryStaffOrderAPI']);
    $api->post('/QueryOrderAPI', ['uses' => 'App\Api\V1\Controllers\OrderController@QueryOrderAPI']);
    $api->post('/OrderCollectDevice', ['uses' => 'App\Api\V1\Controllers\OrderController@OrderCollectDevice']);
    $api->post('/OrderAddAttachment', ['uses' => 'App\Api\V1\Controllers\OrderController@addAttachment']);
    $api->get('/OrderGetAttachment', ['uses' => 'App\Api\V1\Controllers\OrderController@getAttachment']);
    $api->get('/OrderGetSummary', ['uses' => 'App\Api\V1\Controllers\AdminController@getSummary']);

});
