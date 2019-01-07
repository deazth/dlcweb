<?php

namespace App\Api\V1\Controllers;

use Illuminate\Http\Request;
use App\EuctUser;
use App\EuctOrder;

class OrderController extends Controller
{
  /*
list of order statuses:
C = completed / closed
DC = Pending device collection
AB = Pending approval from BC


   */

    function updateUserReqForm(Request $req){

      // first, validate the input
      $input = app('request')->all();
      $rules = [
        'STAFF_ID' => ['required'],
        'STAFF_NAME' => ['required'],
        'COST_CENTER' => ['required'],
        'OFFICE_ADDR' => ['required'],
        'CONTACT_NO' => ['required']
      ];

      $validator = app('validator')->make($input, $rules);
      if($validator->fails()){
        return $this->respond_json(412, 'Invalid input', $input);
      }

      // find or create this user
      $cUser = EuctUser::firstOrCreate(['STAFF_ID' => $req->STAFF_ID]);
      $cUser->STAFF_ID = $req->STAFF_ID;
      $cUser->STAFF_NAME = $req->STAFF_NAME;
      $cUser->COST_CENTER = $req->COST_CENTER;
      $cUser->OFFICE_ADDR = $req->OFFICE_ADDR;
      $cUser->CONTACT_NO = $req->CONTACT_NO;
      $cUser->save();

      return $this->respond_json(200, 'User info saved', $cUser);
    }

    function QueryStaffOrderAPI(Request $req){

      // first, validate the input
      $input = app('request')->all();
      $rules = [
        'STAFF_ID' => ['required']
      ];

      $validator = app('validator')->make($input, $rules);
      if($validator->fails()){
        return $this->respond_json(412, 'Invalid input', $input);
      }

      $sorders = EuctOrder::where('REQ_STAFF_ID', $req->STAFF_ID)->get();

      // return the whole order?
      return $this->respond_json(200, 'List of orders', $sorders);

    }

    function QueryOrderAPI(Request $req){

      // first, validate the input
      $input = app('request')->all();
      $rules = [
        'ORDER_NO' => ['required']
      ];

      $validator = app('validator')->make($input, $rules);
      if($validator->fails()){
        return $this->respond_json(412, 'Invalid input', $input);
      }

      $sorders = EuctOrder::where('ORDER_NO', $req->ORDER_NO)->get();

      // return the whole order?
      return $this->respond_json(200, 'List of orders', $sorders);

    }

}
