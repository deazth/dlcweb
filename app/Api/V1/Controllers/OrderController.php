<?php

namespace App\Api\V1\Controllers;

use Illuminate\Http\Request;
use App\EuctUser;
use App\EuctOrder;
use Illuminate\Support\Facades\DB;

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
      $cUser = EuctUser::firstOrNew(['STAFF_ID' => $req->STAFF_ID]);
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

    function OrderPendingBC(Request $req){
      // first, validate the input
      $input = app('request')->all();
      $rules = [
        'BC_STAFF_ID' => ['required']
      ];

      $validator = app('validator')->make($input, $rules);
      if($validator->fails()){
        return $this->respond_json(412, 'Invalid input', $input);
      }

      $pendingorder = DB::table('euct_orders')
  			->select('euct_orders.*')
        ->join('euct_users', 'euct_orders.REQ_STAFF_ID', '=', 'euct_users.STAFF_ID')
        ->join('euct_bcs', 'euct_users.COST_CENTER', '=', 'euct_bcs.COST_CENTER')
        ->where([
          ['euct_bcs.BC_STAFF_ID', '=', $req->BC_STAFF_ID],
          ['euct_orders.STATUS', '=', 'AB']
        ])->get();

      return $this->respond_json(200, 'List of orders', $pendingorder);

    }

    function OrderApproveBC(Request $req){
      // first, validate the input
      $input = app('request')->all();
      $rules = [
        'ORDER_ID' => ['required']
      ];

      $validator = app('validator')->make($input, $rules);
      if($validator->fails()){
        return $this->respond_json(412, 'Invalid input', $input);
      }

      $theorder = EuctOrder::findOrFail($req->ORDER_ID);
      // move the status to next step: collection
      $theorder->STATUS = 'DC';
      $theorder->save();

      // to do: send alert?
      return $this->respond_json(200, 'BC Approved', $theorder);
    }

    function OrderCollectDevice(Request $req){
      // first, validate the input
      $input = app('request')->all();
      $rules = [
        'ORDER_ID' => ['required'],
        'IC_NO' => ['required'],
        'OPTIONAL_IC' => ['required']
      ];

      $validator = app('validator')->make($input, $rules);
      if($validator->fails()){
        return $this->respond_json(412, 'Invalid input', $input);
      }

      $theorder = EuctOrder::findOrFail($req->ORDER_ID);
      $staffid = $theorder->REQ_STAFF_ID;

      if($req->OPTIONAL_IC == 'Y'){
        $theorder->STATUS = 'C';
        $theorder->save();
      } else {
        // get the IC number
        $ldapobj = new LdapAuthController;
        $ldapinfo = $ldapobj->fetchUser($staffid, 'id');
        if($ldapinfo['code'] == 200){
          $ldapicno = $ldapinfo['data']['NIRC'];
        } else {
          return $ldapinfo;
        }

        if($ldapicno == $req->IC_NO){
          // move the status to next step: close
          $theorder->STATUS = 'C';
          $theorder->save();
        } else {
          // ic missmatch. reject
          return $this->respond_json(401, 'IC missmatch', $req->IC_NO);
        }
      }

      return $this->respond_json(200, 'Device Collected', $theorder);
    }

}
