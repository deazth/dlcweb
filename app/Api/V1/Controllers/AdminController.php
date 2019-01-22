<?php

namespace App\Api\V1\Controllers;

use Illuminate\Http\Request;
use App\EuctOrder;
use App\Dlcm;
use App\Plcm;

class AdminController extends Controller
{
  function OrderAdminApprove(Request $req){
    // first, validate the input
    $input = app('request')->all();
    $rules = [
      'ORDER_ID' => ['required'],
      'A_STAFF_ID' => ['required']
    ];

    $validator = app('validator')->make($input, $rules);
    if($validator->fails()){
      return $this->respond_json(412, 'Invalid input', $input);
    }

    $theorder = EuctOrder::findOrFail($req->ORDER_ID);

    // reject completed orders
    if($theorder->STATUS == 'C'){
      return $this->respond_json(401, 'Order already closed', $theorder);
    }
    
    // move the status to next step:
    if($theorder->ORDER_TYPE == 'TRANSFER'){
      // get the detail of the new owner
      $ldapobj = new LdapAuthController;
      $ldapinfo = $ldapobj->fetchUser($theorder->REQ_STAFF_ID, 'id');
      if($ldapinfo['code'] == 200){
        $reqcs = $ldapinfo['data']['COST_CENTER'];
      } else {
        return $ldapinfo;
      }

      // find the main table
      if($theorder->DEVICE_TYPE == 'DLCM' ){
        $thedevice = Dlcm::findOrFail($theorder->DEVICE_ID);
      } else {
        $thedevice = Plcm::findOrFail($theorder->DEVICE_ID);
      }

      // update the main table (staff id and cost center)
      $thedevice->STAFF_PROJ_ID = $theorder->REQ_STAFF_ID;
      $thedevice->COST_CENTER = $reqcs;
      $thedevice->save();

      $theorder->STATUS = 'C';
    } else if ($theorder->ORDER_TYPE == 'BUY'){
      // move to pending payment
      $theorder->STATUS = 'PAY';
    } else if ($theorder->ORDER_TYPE == 'RETURN'){
      // move to pending device collection
      $theorder->STATUS = 'DC';
    }

    $theorder->save();

    // log
    $this->logs($req->A_STAFF_ID, 'APPROVE', ['ORDER_ID' => $theorder->id]);

    // to do: send alert?
    return $this->respond_json(200, 'Admin Approved', $theorder);
  }

  function OrderAdminReject(Request $req){
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

    // set the remark to 'rejected'
    $rem = json_decode($theorder->ORD_REMARK, TRUE);
    $ori_rem = $rem->R;
    $rem->R = 'Rejected';
    $theorder->ORD_REMARK = json_encode($rem);

    $theorder->STATUS = 'C';
    $theorder->save();

    $this->logs($req->A_STAFF_ID, 'REJECT', ['ORDER_ID' => $theorder->id, 'ORI_REMARK' => $ori_rem]);
    // to do: send alert?
    return $this->respond_json(200, 'Order rejected', $theorder);
  }

  function OrderPendingAD(Request $req){
    $pendingorder = EuctOrder::where('STATUS', 'AD')->get();

    return $this->respond_json(200, 'List of orders', $pendingorder);

  }

  // internal order processing functions
  function transferOwnership($order){




  }

}
