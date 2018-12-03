<?php

namespace App\Api\V1\Controllers;

use Illuminate\Http\Request;
use App\EuctAdmin;

class RoleController extends Controller
{
  function createRole(Request $req){
    $errm = 'success';
    $errorcode = 200;

    // first, validate the input
    $input = app('request')->all();
    $rules = [
      'STAFF_ID' => ['required'],
      'STAFF_NAME' => ['required'],
      'ROLE_TYPE' => ['required'],
      'REMARK' => ['required']
    ];

    $validator = app('validator')->make($input, $rules);
    if($validator->fails()){
      return $this->respond_json(412, 'Invalid input', $input);
    }

    $euctadmin = EuctAdmin::firstOrNew(['STAFF_ID' => $req->STAFF_ID]);
    $euctadmin->STAFF_NAME = $req->STAFF_NAME;
    $euctadmin->ROLE_TYPE = $req->ROLE_TYPE;
    $euctadmin->REMARK = $req->REMARK;
    $euctadmin->save();

    return $this->respond_json(200, 'OK', $euctadmin);

  }

  function deleteRole(Request $req){
    $errm = 'OK';
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

    $euctadmin = EuctAdmin::where('STAFF_ID', $req->STAFF_ID)->first();
    if($euctadmin){
      $euctadmin->delete();
    } else {
      $errorcode = 404;
      $errm = 'Staff id not found';
    }

    return $this->respond_json($errorcode, $errm, []);

  }

  function listRole(){
    $allrole = EuctAdmin::all();
    return $this->respond_json(200, 'OK', $allrole);
  }
}
