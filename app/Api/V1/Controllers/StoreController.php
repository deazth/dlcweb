<?php

namespace App\Api\V1\Controllers;

use Illuminate\Http\Request;

class StoreController extends Controller
{
  function UpdateStoreAPI(Request $req){
    $errm = 'success';
    $errorcode = 200;

    // first, validate the input
    $input = app('request')->all();
    $rules = [
      'STAFF_ID' => ['required'],
      'TAG_NO' => ['required'],
      'MODEL' => ['required'],
      'BRAND' => ['required'],
      'SERIAL_NUMBER' => ['required'],
      'CATEGORY' => ['required']
    ];

    $validator = app('validator')->make($input, $rules);
    if($validator->fails()){
      return $this->respond_json(412, 'Invalid input', $input);
    }

    $staffid = $req->STAFF_ID;
    $tagno = $req->TAG_NO;
    $model = $req->MODEL;
    $brand = $req->BRAND;
    $serialno = $req->SERIAL_NUMBER;
    $categ = $req->CATEGORY;


    return $this->respond_json(200, 'OK', $devices);

  }
}
