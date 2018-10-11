<?php

$api = app('Dingo\Api\Routing\Router');

$api->version('v1', function ($api) {
  $api->post('getuserinfo', 'App\Http\Controllers\LdapAuthController@getUserInfo');
  $api->post('dologin', 'App\Http\Controllers\LdapAuthController@doLogin');
  $api->post('authcon', 'App\Http\Controllers\LdapAuthController@authcon');

  // user related
  

});

// Route::post('authcon', 'LdapAuthController@authcon');
