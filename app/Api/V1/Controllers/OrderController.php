<?php

namespace App\Api\V1\Controllers;

use Illuminate\Http\Request;

class OrderController extends Controller
{

    function SubmitOrderApi(Request $req){
      $errm = 'success';
      $errorcode = 200;

      // first, validate the input
      $input = app('request')->all();
      $rules = [
        'STAFF_ID' => ['required'],
        'TAG_NO' => ['required'],
        'CATEGORY' => ['required']
      ];

      $validator = app('validator')->make($input, $rules);
      if($validator->fails()){
        return $this->respond_json(412, 'Invalid input', $input);
      }

      $username = $req->STAFF_ID;

    }

    function QueryOrderAPI(Request $req){
      $errm = 'success';
      $errorcode = 200;

      // first, validate the input
      $input = app('request')->all();
      $rules = [
        'STAFF_ID' => ['required']
      ];

      $validator = app('validator')->make($input, $rules);
      if($validator->fails()){
        return $this->respond_json(412, 'Invalid input', $input);
      }

      $username = $req->STAFF_ID;

    }

    function BCUpdateOwnerAPI(Request $req){
      $errm = 'success';
      $errorcode = 200;

      // first, validate the input
      $input = app('request')->all();
      $rules = [
        'STAFF_ID' => ['required'],
        'TAG_NO' => ['required'],
        'SERIAL_NO' => ['required']
      ];

      $validator = app('validator')->make($input, $rules);
      if($validator->fails()){
        return $this->respond_json(412, 'Invalid input', $input);
      }

      $username = $req->STAFF_ID;

    }

    function DLCMTerminateApprovalAPI(Request $req){
      $errm = 'success';
      $errorcode = 200;

      // first, validate the input
      $input = app('request')->all();
      $rules = [
        'ORDER_NO' => ['required'],
        'TAG_NO' => ['required'],
        'ACTION' => ['required'],
        'REMARKS' => ['required']
      ];

      $validator = app('validator')->make($input, $rules);
      if($validator->fails()){
        return $this->respond_json(412, 'Invalid input', $input);
      }

      $username = $req->STAFF_ID;

    }
}
