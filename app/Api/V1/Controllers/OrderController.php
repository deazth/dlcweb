<?php

namespace App\Api\V1\Controllers;

use Illuminate\Http\Request;
use App\EuctUser;
use App\EuctOrder;
use App\Dlcm;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
  /*
list of order statuses:
C = completed / closed
DC = Pending device collection
AD = Pending approval from admin
PAY = pending payment

List of order types
RETURN
BUY
TRANSFER
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





    function OrderCollectDevice(Request $req){
      // first, validate the input
      $input = app('request')->all();
      $rules = [
        'ORDER_ID' => ['required'],
        'IC_NO' => ['required'],
        'OPTIONAL_IC' => ['required'],
        'C_STAFF_ID' => ['required']
      ];

      $validator = app('validator')->make($input, $rules);
      if($validator->fails()){
        return $this->respond_json(412, 'Invalid input', $input);
      }

      $theorder = EuctOrder::findOrFail($req->ORDER_ID);
      $staffid = $theorder->REQ_STAFF_ID;

      if($theorder->STATUS != 'DC'){
        return $this->respond_json(401, 'Order status not ready for collection', $theorder);
      }

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

          // update the main table
          $thelcm = Dlcm::find($theorder->DEVICE_ID);
          $theldm->STATUS = 'INACTIVE';
          $thelcm->DETAILS_STATUS = 'RETURN';
          $thelcm->ACTUAL_STATUS = 'COLLECTED';
          $thelcm->COLLECTION_DATE = date('d/m/Y');
          $thelcm->COLLECTION_BY = $req->C_STAFF_ID;
          $thelcm->save();


        } else {
          // ic missmatch. reject
          return $this->respond_json(401, 'IC missmatch', $req->IC_NO);
        }
      }

      // log
      $this->logs($req->C_STAFF_ID, 'COLLECT DEVICE', ['ORDER_ID' => $theorder->id]);

      return $this->respond_json(200, 'Device Collected', $theorder);
    }

}
