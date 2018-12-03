<?php

namespace App\Api\V1\Controllers;

use Illuminate\Http\Request;
use App\EuctBc;

class BcController extends Controller
{
  function createBc(Request $req){
    $errm = 'success';
    $errorcode = 200;

    // first, validate the input
    $input = app('request')->all();
    $rules = [
      'COST_CENTER' => ['required'],
      'BC_STAFF_ID' => ['required'],
      'BC_STAFF_NAME' => ['required'],
      'STATUS' => ['required']
    ];

    $validator = app('validator')->make($input, $rules);
    if($validator->fails()){
      return $this->respond_json(412, 'Invalid input', $input);
    }

    $euctbc = EuctBc::firstOrNew(['COST_CENTER' => $req->COST_CENTER]);
    $euctbc->BC_STAFF_ID = $req->BC_STAFF_ID;
    $euctbc->BC_STAFF_NAME = $req->BC_STAFF_NAME;
    $euctbc->STATUS = $req->STATUS;
    $euctbc->save();

    return $this->respond_json(200, 'OK', $euctbc);

  }

  function deleteBc(Request $req){
    $errm = 'OK';
    $errorcode = 200;

    // first, validate the input
    $input = app('request')->all();
    $rules = [
      'COST_CENTER' => ['required']
    ];

    $validator = app('validator')->make($input, $rules);
    if($validator->fails()){
      return $this->respond_json(412, 'Invalid input', $input);
    }

    $euctbc = EuctBc::where('COST_CENTER', $req->COST_CENTER)->first();
    if($euctbc){
      $euctbc->delete();
    } else {
      $errorcode = 404;
      $errm = 'Staff id not found';
    }

    return $this->respond_json($errorcode, $errm, []);

  }

  function listBc(){
    $allbc = EuctBc::all();

    return $this->respond_json(200, 'OK', $allbc);
  }
}
