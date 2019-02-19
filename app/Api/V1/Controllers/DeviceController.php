<?php

namespace App\Api\V1\Controllers;

use Illuminate\Http\Request;
use App\Dlcm;
use App\Plcm;
use App\EuctOrder;
use App\EuctBc;
use \DateTime;
use \DateTimeZone;
use \DB;

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

      if(empty($devices[$dtype])){
        // return not found if empty
        $errorcode = 404;
      } else {
        // check for other statuses as req by acap
        if(!empty($devices[$dtype][0]['ACTUAL_STATUS']) && !empty($devices['DLCM'][0]['DETAILS_STATUS'])){
          $errorcode = 400;
        }
      }

      return $this->respond_json($errorcode, 'OK', $devices);

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
        'DEVICE_TYPE' => ['required'],
        'REMARK' => ['required'],
        'REQ_STAFF_ID' => ['required']
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
        return $this->respond_json(409, 'Open order exist', $this->translateOrder($openorder));
      }

      // create new order number
      $ordernum = $this->getNextSequence('TReturn');

      // create the termination order for this device
      $nuorder = new EuctOrder;
      $nuorder->ORDER_NO = $ordernum;
      $nuorder->ORDER_TYPE = 'RETURN';
      $nuorder->DEVICE_TYPE = $devtype;
      $nuorder->REQ_STAFF_ID = $req->REQ_STAFF_ID;
      $nuorder->STATUS = 'AD';

      $remark = [
                  'R' => $req->REMARK,
                  'ORI_OWNER' => $thedevice->STAFF_PROJ_ID,
                  'ORI_NAME' => $thedevice->STAFF_PROJ_NAME,
                  'TAG_NO' => $thedevice->TAG_NO
                ];

      $nuorder->ORD_REMARK = json_encode($remark);
      $nuorder->DEVICE_ID = $devid;
      $nuorder->save();

      $this->logs($req->REQ_STAFF_ID, 'RETURN', ['ORDER_ID' => $nuorder->id, 'REMARK' => $remark]);

      // to do: alert the next person?

      return $this->respond_json(200, 'OK', $this->translateOrder($nuorder));

    }

    function BuyDeviceReq(Request $req){

      // first, validate the input
  		$input = app('request')->all();
  		$rules = [
  			'DEVICE_ID' => ['required'],
        'DEVICE_TYPE' => ['required'],
        'REMARK' => ['required'],
        'REQ_STAFF_ID' => ['required']
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
        return $this->respond_json(409, 'Open order exist', $this->translateOrder($openorder));
      }

      // create new order number
      $ordernum = $this->getNextSequence('TBuy');

      // create the termination order for this device
      $nuorder = new EuctOrder;
      $nuorder->ORDER_NO = $ordernum;
      $nuorder->ORDER_TYPE = 'BUY';
      $nuorder->DEVICE_TYPE = $devtype;
      $nuorder->REQ_STAFF_ID = $req->REQ_STAFF_ID;
      $nuorder->STATUS = 'AD';

      $remark = [
                  'R' => $req->REMARK
                ];

      $nuorder->ORD_REMARK = json_encode($remark);
      $nuorder->DEVICE_ID = $devid;
      $nuorder->save();

      $this->logs($req->REQ_STAFF_ID, 'BUY', ['ORDER_ID' => $nuorder->id, 'REMARK' => $remark]);

      // to do: alert the next person?

      return $this->respond_json(200, 'OK', $this->translateOrder($nuorder));

    }

    function DeviceReportLost(Request $req){
      $input = app('request')->all();
  		$rules = [
  			'DEVICE_ID' => ['required'],
        'DEVICE_TYPE' => ['required'],
        'REQ_STAFF_ID' => ['required'],
        'REMARK' => ['required'],
        'REPORT_NO' => ['required']
  		];

  		$validator = app('validator')->make($input, $rules);
  		if($validator->fails()){
  			return $this->respond_json(412, 'Invalid input', $input);
  		}

      $devid = $req->DEVICE_ID;
      $devtype= $req->DEVICE_TYPE;

      // check the device exist or not
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
        return $this->respond_json(409, 'Open order exist', $this->translateOrder($openorder));
      }

      // new order number
      $ordernum = $this->getNextSequence('TLost');

      // create the transfer order for this device
      $nuorder = new EuctOrder;
      $nuorder->ORDER_NO = $ordernum;
      $nuorder->ORDER_TYPE = 'LOST';
      $nuorder->DEVICE_TYPE = $devtype;
      $nuorder->REQ_STAFF_ID = $req->REQ_STAFF_ID;
      $nuorder->STATUS = 'AD';

      $remark = [
        'R' => $req->REMARK,
        'REPORT_NO' => $req->REPORT_NO
      ];

      $nuorder->ORD_REMARK = json_encode($remark);
      $nuorder->DEVICE_ID = $devid;
      $nuorder->save();

      $this->logs($req->REQ_STAFF_ID, 'LOST', ['ORDER_ID' => $nuorder->id, 'REMARK' => $remark]);

      // to do: alert the next person?

      return $this->respond_json(200, 'OK', $this->translateOrder($nuorder));
    }

    function DeviceChangeOwner(Request $req){
      $input = app('request')->all();
  		$rules = [
  			'DEVICE_ID' => ['required'],
        'DEVICE_TYPE' => ['required'],
        'N_STAFF_ID' => ['required'],
        'REMARK' => ['required']
  		];

  		$validator = app('validator')->make($input, $rules);
  		if($validator->fails()){
  			return $this->respond_json(412, 'Invalid input', $input);
  		}

      $devid = $req->DEVICE_ID;
      $devtype= $req->DEVICE_TYPE;
      $newowner = $req->N_STAFF_ID;

      // check the device exist or not
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
        return $this->respond_json(409, 'Open order exist', $this->translateOrder($openorder));
      }

      // new order number
      $ordernum = $this->getNextSequence('TTransfer');

      // create the transfer order for this device
      $nuorder = new EuctOrder;
      $nuorder->ORDER_NO = $ordernum;
      $nuorder->ORDER_TYPE = 'TRANSFER';
      $nuorder->DEVICE_TYPE = $devtype;
      $nuorder->REQ_STAFF_ID = $newowner;
      $nuorder->STATUS = 'AD';

      $remark = [
        'R' => $req->REMARK,
        'PREV_OWNER' => $thedevice->STAFF_PROJ_ID
      ];

      $nuorder->ORD_REMARK = json_encode($remark);
      $nuorder->DEVICE_ID = $devid;
      $nuorder->save();

      $this->logs($newowner, 'TRANSFER', ['ORDER_ID' => $nuorder->id, 'REMARK' => $remark]);

      // to do: alert the next person?

      return $this->respond_json(200, 'OK', $this->translateOrder($nuorder));

    }

    function listDeviceByBC(Request $req){
      $input = app('request')->all();
  		$rules = [
  			'BC_STAFF_ID' => ['required']
  		];

  		$validator = app('validator')->make($input, $rules);
  		if($validator->fails()){
  			return $this->respond_json(412, 'Invalid input', $input);
  		}

      $theretval = [];

      // get the list of cost center under this BC
      $cslist = EuctBc::where('BC_STAFF_ID', $req->BC_STAFF_ID)->get();

      foreach($cslist as $thecostcenter){
        // find the list of devices under this cost center

        $devices = [
          'COST_CENTER' => $thecostcenter->COST_CENTER,
          'DLCM' => $this->findDlcmDevice('COST_CENTER', $thecostcenter->COST_CENTER),
          'PLCM' => $this->findPlcmDevice('COST_CENTER', $thecostcenter->COST_CENTER)
        ];

        array_push($theretval, $devices);
      }


      return $this->respond_json(200, 'OK', $theretval);
    }

    function getDlcmDeviceStatus(){

      return [
        'DLCM1' => $this->getDeviceStatusforDlcm('1'),
        'DLCM2' => $this->getDeviceStatusforDlcm('2')
      ];
    }



    // function to be used internally

    function getDeviceStatusforDlcm($dlcm_no){
      $ret = [];
      // first get the distinct type
      $dtype = DB::table('DLCM_FULL')
        ->select('DESKTOP_TYPE')
        ->groupBy('DESKTOP_TYPE')
        ->where('DLCM', $dlcm_no)->get();

      foreach($dtype as $stype){
        $sret = ['TYPE' => $stype->DESKTOP_TYPE];
        // for each type, get the count
        $scount = DB::table('DLCM_FULL')
          ->select('STATUS', DB::raw('count(*) as total'))
          ->groupBy('STATUS')
          ->where('DLCM', $dlcm_no)
          ->where('DESKTOP_TYPE', $stype->DESKTOP_TYPE)->get();


        foreach($scount as $thecount){
          $sret[$thecount->STATUS] = $thecount->total;
        }

        // push it into the return array
        array_push($ret, $sret);

      }

      return $ret;

    }

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
          'STATUS' => $ddata->STATUS,
          'CONTACT_PERSON' => $ddata->CONTACT_PERSON,
          'LOCATION' => $ddata->LOCATION,
          'ACTUAL_STATUS' => $ddata->ACTUAL_STATUS,
          'DETAILS_STATUS' => $ddata->DETAILS_STATUS,
          'DLCM' => $ddata->DLCM,
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
