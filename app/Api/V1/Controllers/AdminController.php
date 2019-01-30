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
      return $this->respond_json(401, 'Order already closed', $this->translateOrder($theorder));
    }

    if($theorder->STATUS != 'AD'){
      return $this->respond_json(401, 'Order already approved', $this->translateOrder($theorder));
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
    } else if ($theorder->ORDER_TYPE == 'LOST'){
      // update the main table
      if($theorder->DEVICE_TYPE == 'DLCM' ){
        $thedevice = Dlcm::findOrFail($theorder->DEVICE_ID);
      } else {
        $thedevice = Plcm::findOrFail($theorder->DEVICE_ID);
      }

      // update the main table (staff id and cost center)
      $thedevice->STATUS = 'LOST';
      $thedevice->DETAILS_STATUS = 'LOST';
      $rem = json_decode($theorder->ORD_REMARK, TRUE);
      $thedevice->ACTUAL_STATUS = $rem['REPORT_NO'];
      $thedevice->save();

      $theorder->STATUS = 'C';
    }

    $theorder->save();

    // log
    $this->logs($req->A_STAFF_ID, 'APPROVE', ['ORDER_ID' => $theorder->id]);

    // to do: send alert?
    return $this->respond_json(200, 'Admin Approved', $this->translateOrder($theorder));
  }

  function OrderAdminReject(Request $req){
    // first, validate the input
    $input = app('request')->all();
    $rules = [
      'ORDER_ID' => ['required'],
      'A_STAFF_ID' => ['required'],
      'REMARK' => ['required']
    ];

    $validator = app('validator')->make($input, $rules);
    if($validator->fails()){
      return $this->respond_json(412, 'Invalid input', $input);
    }

    $theorder = EuctOrder::findOrFail($req->ORDER_ID);

    if($theorder->STATUS == 'C'){
      return $this->respond_json(401, 'Order already closed', $this->translateOrder($theorder));
    }

    // set the remark to 'rejected'
    $rem = json_decode($theorder->ORD_REMARK, TRUE);
    $rem['REJECT_REMARK'] = $req->REMARK;
    $theorder->ORD_REMARK = json_encode($rem);

    $theorder->STATUS = 'C';
    $theorder->save();

    $this->logs($req->A_STAFF_ID, 'REJECT', ['ORDER_ID' => $theorder->id, 'REJECT_REMARK' => $req->REMARK]);
    // to do: send alert?
    return $this->respond_json(200, 'Order rejected', $this->translateOrder($theorder));
  }

  function OrderPendingAD(Request $req){
    $pendingorder = EuctOrder::where('STATUS', 'AD')->get();

    $newarrayorder = [];
    foreach($pendingorder as $oneorder){
      array_push($newarrayorder, $this->translateOrder($oneorder));
    }


    return $this->respond_json(200, 'List of orders', $newarrayorder);

  }

  function OrderPendingPAY(Request $req){
    $pendingorder = EuctOrder::where('STATUS', 'PAY')->get();

    $newarrayorder = [];
    foreach($pendingorder as $oneorder){
      array_push($newarrayorder, $this->translateOrder($oneorder));
    }


    return $this->respond_json(200, 'List of orders', $newarrayorder);

  }

  function OrderReceivePayment(Request $req){
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

    // find the order
    $theorder = EuctOrder::findOrFail($req->ORDER_ID);

    // check if the order has been approved
    if($theorder->STATUS == 'C'){
      return $this->respond_json(401, 'Order already closed', $this->translateOrder($theorder));
    }
    if($theorder->STATUS != 'PAY'){
      return $this->respond_json(401, 'Order not yet approved', $this->translateOrder($theorder));
    }

    // then find the device
    if($theorder->DEVICE_TYPE == 'DLCM' ){
      $thedevice = Dlcm::findOrFail($theorder->DEVICE_ID);
    } else {
      $thedevice = Plcm::findOrFail($theorder->DEVICE_ID);
    }

    // update the status in the device
    $thedevice->STATUS = 'INACTIVE';
    $thedevice->DETAILS_STATUS = 'PURCHASE';
    $thedevice->ACTUAL_STATUS = 'PAYMENT COMPLETED';
    $thedevice->save();

    // update the order
    $theorder->STATUS = 'C';
    $orderremark = json_decode($theorder->ORD_REMARK, TRUE);
    $orderremark['RCVD_PAY'] = $req->A_STAFF_ID;
    $theorder->ORD_REMARK = json_encode($orderremark);
    $theorder->save();

    $this->logs($req->A_STAFF_ID, 'RECEIVE PAY', ['ORDER_ID' => $theorder->id]);

    return $this->respond_json(200, 'Payment received', $this->translateOrder($theorder));

  }

}
