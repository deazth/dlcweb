<?php

namespace App\Api\V1\Controllers;

use Illuminate\Http\Request;
use App\Dlcm;
use App\Plcm;
use App\EuctOrder;
use \DateTime;
use \DateTimeZone;

class DeviceController extends Controller
{
    // functions to be called as Api

    function QueryDeviceApi(Request $req){
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

      $devices = [
        'DLCM' => $this->findDlcmDevice('STAFF_PROJ_ID', $username),
        'PLCM' => $this->findPlcmDevice('STAFF_PROJ_ID', $username)
      ];

      return $this->respond_json(200, 'OK', $devices);

    }

    function FindDeviceByKeyApi(Request $req){
      $errm = 'success';
      $errorcode = 200;

      // first, validate the input
  		$input = app('request')->all();
  		$rules = [
  			'SEARCH_KEY' => ['required'],
        'SEARCH_STR' => ['required'],
        'DEVICE_TYPE' => ['required']
  		];

  		$validator = app('validator')->make($input, $rules);
  		if($validator->fails()){
  			return $this->respond_json(412, 'Invalid input', $input);
  		}

  		$searchstr = $req->SEARCH_STR;
      $searchkey = $req->SEARCH_KEY;
      $dtype = $req->DEVICE_TYPE;

      if($dtype == 'DLCM' ){
        $devices = [
          'DLCM' => $this->findDlcmDevice($searchkey, $searchstr)
        ];
      } else {
        $devices = [
          'PLCM' => $this->findPlcmDevice($searchkey, $searchstr)
        ];
      }

      return $this->respond_json(200, 'OK', $devices);

    }


    function FindDeviceBySerialApi(Request $req){
      $errm = 'success';
      $errorcode = 200;

      // first, validate the input
  		$input = app('request')->all();
  		$rules = [
  			'SERIAL_NO' => ['required']
  		];

  		$validator = app('validator')->make($input, $rules);
  		if($validator->fails()){
  			return $this->respond_json(412, 'Invalid input', $input);
  		}

  		$devserial = $req->SERIAL_NO;

      $devices = [
        'DLCM' => $this->findDlcmDevice('SERIAL_NO', $devserial),
        'PLCM' => $this->findPlcmDevice('SERIAL_NO', $devserial)
      ];

      return $this->respond_json(200, 'OK', $devices);

    }

    function FindDeviceByStaffApi(Request $req){
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

  		$devserial = $req->STAFF_ID;

      $devices = [
        'DLCM' => $this->findDlcmDevice('STAFF_PROJ_ID', $devserial),
        'PLCM' => $this->findPlcmDevice('STAFF_PROJ_ID', $devserial)
      ];

      return $this->respond_json(200, 'OK', $devices);

    }

    function queryAllDeviceDlcm() {
      // $aplcm = Plcm::all();
      $adlcm = Dlcm::paginate(200);

/*
      $ret = [
        'DLCM' => $this->findDlcmDevice('ALL', '') ,
        'PLCM' => $this->findPlcmDevice('ALL', '')
      ];
*/

      return $this->respond_json(200, 'OK', $adlcm);
    }

    function queryAllDevicePlcm() {
      // $aplcm = Plcm::all();
      $adlcm = Plcm::paginate(200);

/*
      $ret = [
        'DLCM' => $this->findDlcmDevice('ALL', '') ,
        'PLCM' => $this->findPlcmDevice('ALL', '')
      ];
*/

      return $this->respond_json(200, 'OK', $adlcm);
    }

    function checkDeviceWarranty(Request $req){
      // first, validate the input
  		$input = app('request')->all();
  		$rules = [
  			'DEVICE_ID' => ['required'],
        'DEVICE_TYPE' => ['required']
  		];

  		$validator = app('validator')->make($input, $rules);
  		if($validator->fails()){
  			return $this->respond_json(412, 'Invalid input', $input);
  		}

  		$devid = $req->DEVICE_ID;
      $devtype= $req->DEVICE_TYPE;

      if($devtype == 'DLCM' ){
        $thedevice = Dlcm::findOrFail($devid);
      } else {
        $thedevice = Plcm::findOrFail($devid);
      }

      // get the COA
      $coadate = DateTime::createFromFormat('d/m/Y', $thedevice->COA_DATE);

      // compare it with current date to get the year difference
      $nowdate = new DateTime('NOW');
  		$nowdate->setTimezone(new DateTimeZone('+0800'));
      $yeardiff = $coadate->diff($nowdate)->format('%y');

      $warranty = "Y";

      if($yeardiff >= 3){
        $warranty = "N";
      }

      $retst = [
        'COA_DATE' => $thedevice->COA_DATE,
        'YEAR_DIFF' => $yeardiff,
        'IN_WARRANTY' => $warranty
      ];

      return $this->respond_json(200, 'OK', $retst);

    }

    function returnDeviceReq(Request $req){

      // first, validate the input
  		$input = app('request')->all();
  		$rules = [
  			'DEVICE_ID' => ['required'],
        'DEVICE_TYPE' => ['required']
  		];

  		$validator = app('validator')->make($input, $rules);
  		if($validator->fails()){
  			return $this->respond_json(412, 'Invalid input', $input);
  		}

  		$devid = $req->DEVICE_ID;
      $devtype= $req->DEVICE_TYPE;

      if($devtype == 'DLCM' ){
        $thedevice = Dlcm::findOrFail($devid);
      } else {
        $thedevice = Plcm::findOrFail($devid);
      }

      // check any open order for this device
      $openorder = EuctOrder::where([
        ['DEVICE_TYPE', '=', $devtype],
        ['DEVICE_ID', '=', $devid],
        ['STATUS', '!=', 'C']
      ])->first();


      if(empty($openorder)){

      } else {
        // got open order. deny
        return $this->respond_json(409, 'Open order exist', $openorder);
      }

      // create new order number
      $ordernum = $this->getNextSequence('TReturn');

      // create the termination order for this device
      $nuorder = new EuctOrder;
      $nuorder->ORDER_NO = $ordernum;
      $nuorder->ORDER_TYPE = 'RETURN';
      $nuorder->DEVICE_TYPE = $devtype;
      $nuorder->REQ_STAFF_ID = $thedevice->STAFF_PROJ_ID;
      $nuorder->STATUS = 'AB';
      $nuorder->ORD_REMARK = '';
      $nuorder->DEVICE_ID = $devid;
      $nuorder->save();

      // to do: alert the next person?

      return $this->respond_json(200, 'OK', $nuorder);

    }



    // function to be used internally

    function findDlcmDevice($fieldfilter, $searchno){
      $res = [];

      if(strcasecmp($fieldfilter, 'ALL') == 0){
        // $sdata = Dlcm::all();
      } else {
        $sdata = Dlcm::where($fieldfilter, $searchno)->get();
      }


      foreach($sdata as $ddata){
        $arrd = [
          'id' => $ddata->id,
          'TAG_NO' => $ddata->TAG_NO,
          'SERIAL_NO' => $ddata->SERIAL_NO,
          'COST_CENTER' => $ddata->COST_CENTER,
          'STAFF_PROJ_ID' => $ddata->STAFF_PROJ_ID,
          'STAFF_PROJ_NAME' => $ddata->STAFF_PROJ_NAME,
          'CATEGORY' => $ddata->DESKTOP_TYPE,
          'MODEL' => $ddata->DESCRIPTION,
          'EXPIRY_DATE' => $ddata->EXPIRE_DATE,
          'STATUS' => $ddata->ACTUAL_STATUS,
          'EMAIL' => $ddata->END_USER_EMAIL,
        ];

        array_push($res, $arrd);

      }

      return $res;
    }

    function findPlcmDevice($fieldfilter, $searchno){
      $res = [];

      if(strcasecmp($fieldfilter, 'ALL') == 0){
        $sdata = Plcm::all();
      } else {
        $sdata = Plcm::where($fieldfilter, $searchno)->get();
      }

      foreach($sdata as $ddata){
        $arrd = [
          'TAG_NO' => $ddata->TAG_NO,
          'SERIAL_NO' => $ddata->SERIAL_NO,
          'COST_CENTER' => $ddata->COST_CENTER,
          'STAFF_PROJ_ID' => $ddata->STAFF_PROJ_ID,
          'STAFF_PROJ_NAME' => $ddata->STAFF_PROJ_NAME,
          'CATEGORY' => $ddata->PRT_CAT,
          'MODEL' => $ddata->MODEL,
          'EXPIRY_DATE' => $ddata->RETIRED_DATE,
          'STATUS' => $ddata->BILLSTATUS,
        ];

        array_push($res, $arrd);

      }

      return $res;
    }

    function reqTerminateDlcm($did){


    }

    function reqTerminatePlcm($did){

    }


}
