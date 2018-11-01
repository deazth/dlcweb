<?php

namespace App\Api\V1\Controllers;

use Illuminate\Http\Request;
use App\Dlcm;
use App\Plcm;

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





    // function to be used internally

    function findDlcmDevice($fieldfilter, $searchno){
      $res = [];

      $sdata = Dlcm::where($fieldfilter, $searchno)->get();

      foreach($sdata as $ddata){
        $arrd = [
          'TAG_NO' => $ddata->TAG_NO,
          'SERIAL_NO' => $ddata->SERIAL_NO,
          'COST_CENTER' => $ddata->COST_CENTER,
          'STAFF_PROJ_ID' => $ddata->STAFF_PROJ_ID,
          'STAFF_PROJ_NAME' => $ddata->STAFF_PROJ_NAME,
          'CATEGORY' => $ddata->DESKTOP_TYPE,
          'MODEL' => $ddata->DESCRIPTION,
          'EXPIRY_DATE' => $ddata->EXPIRE_DATE,
          'STATUS' => $ddata->ACTUAL_STATUS,
        ];

        array_push($res, $arrd);

      }

      return $res;
    }

    function findPlcmDevice($fieldfilter, $searchno){
      $res = [];

      $sdata = Plcm::where($fieldfilter, $searchno)->get();

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
}
