<?php

namespace App\Api\V1\Controllers;

use Illuminate\Http\Request;
use App\EuctStore;

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

    // first, check the role of this staff
    $role = $this->getRole($staffid);

    if($role != 'SM'){
      return $this->respond_json(403, 'Not a store manager', ['Role' => $role]);
    }

    // search for the item in store
    $storeitem = EuctStore::where('TAG_NO', $tagno)
                ->where('SERIAL_NUMBER', $serialno)
                ->first();  // expect to only have 1

    if($storeitem){
      // device exist in store. check the status
      if($storeitem->STATUS == 'Received'){
        return $this->respond_json(417, 'Device already received', $storeitem);
      } else {
        // update the status of this device, along with the additional infos
        $storeitem->STATUS = 'Received';
        $storeitem->MODEL = $model;
        $storeitem->BRAND = $brand;
        $storeitem->CATEGORY = $categ;
        $storeitem->RECEIVED_BY = $staffid;
        $storeitem->save();
        $errm = 'Record updated';
      }
    } else {
      // not exist. create new?
      $storeitem = new EuctStore;
      $storeitem->STATUS = 'Received';
      $storeitem->MODEL = $model;
      $storeitem->BRAND = $brand;
      $storeitem->CATEGORY = $categ;
      $storeitem->RECEIVED_BY = $staffid;
      $storeitem->ADDED_BY = $staffid;
      $storeitem->TAG_NO = $tagno;
      $storeitem->SERIAL_NUMBER = $serialno;

      // default values
      $storeitem->EQUIP_TYPE = '';
      $storeitem->BATCH_NO = '';
      $storeitem->DELIVERY_DATE = '';

      $storeitem->save();
      $errm = 'New record created';
    }

    return $this->respond_json(200, $errm, $storeitem);

  }

  function AddStoreAPI(Request $request){
    $errm = 'success';
    // first, validate the input
    $input = app('request')->all();
    $rules = [
      'STAFF_ID' => ['required'],
      'TAG_NO' => ['required'],
      'EQUIP_TYPE' => ['required'],
      'MODEL' => ['required'],
      'SERIAL_NUMBER' => ['required'],
      'WARRANTY' => ['required']
    ];

    $validator = app('validator')->make($input, $rules);
    if($validator->fails()){
      return $this->respond_json(412, 'Invalid input', $input);
    }

    $staffid = $request->STAFF_ID;
    $tagno = $request->TAG_NO;
    $eqtype = $request->EQUIP_TYPE;
    $batchno = $request->MODEL;
    $serialno = $request->SERIAL_NUMBER;
    $deliverydate = $request->WARRANTY;

    // search for the item in store
    $storeitem = EuctStore::where('TAG_NO', $tagno)
                ->where('SERIAL_NUMBER', $serialno)
                ->first();  // expect to only have 1

    if($storeitem){
      // device exist in store
      return $this->respond_json(417, 'Device already exist', $storeitem);
    } else {
      // not exist. create new?
      $storeitem = new EuctStore;
      $storeitem->STATUS = 'New';
      $storeitem->EQUIP_TYPE = $eqtype;
      $storeitem->MODEL = $batchno;
      $storeitem->WARRANTY_DATE = $deliverydate;
      $storeitem->ADDED_BY = $staffid;
      $storeitem->TAG_NO = $tagno;
      $storeitem->SERIAL_NUMBER = $serialno;

      // default values

      $storeitem->BATCH_NO = '';
      $storeitem->BRAND = '';
      $storeitem->CATEGORY = '';
      $storeitem->RECEIVED_BY = '';

      $storeitem->save();
      $errm = 'New record created';
    }

    return $this->respond_json(200, $errm, $storeitem);

  }

  function ListStoreAPI(){
    $allstore = EuctStore::all();

    return $this->respond_json(200, 'OK', $allstore);

  }
}
