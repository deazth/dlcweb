<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DeviceController extends extends BaseController
{
    // functions to be called as Api

    function QueryDeviceApi(Request req){
      $errm = 'success';
      $errorcode = 200;

      // first, validate the input
  		$input = app('request')->all();
  		$rules = [
  			'STAFF_ID' => ['required'],
  			'PASSWORD' => ['required']
  		];

  		$validator = app('validator')->make($input, $rules);
  		if($validator->fails()){
  			return $this->respond_json(412, 'Invalid input', $input);
  		}

  		$username = $req->STAFF_ID;
  		$password = $req->PASSWORD;

    }

    function SubmitOrderApi(Request req){

    }





    // function to be used internally
}
