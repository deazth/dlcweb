<?php
namespace App\Api\V1\Controllers;

use Dingo\Api\Routing\Helpers;
use Illuminate\Routing\Controller as BaseController;
use App\EuctSequence;
use App\EuctBc;
use App\EuctAdmin;
use App\EuctLog;

/**
 * Shared functions will be placed here
 */
class Controller extends BaseController
{
  use Helpers;

  function errorHandler($errno, $errstr) {
		return $this->respond_json($errno, $errstr);
	}

  function translateOrder($order){
    $orderremark = json_decode($order->ORD_REMARK);
    $order->ORD_REMARK = $orderremark;
    return $order;
  }

	function respond_json($retCode, $message, $data_arr = []){
		$curtime = date("Y-m-d h:i:sa");
		$retval = [
			'code' => $retCode,
			'msg'  => $message,
			'time' => $curtime,
			'data' => $data_arr
		];

		return $retval;

	}

  function getNextSequence($type){
    // set the default values
    $curnum = 1;
    $pref = substr($type, 0, 2);
    $len = 6;

    // find the sequence
    $datseq = EuctSequence::where('type', $type)->first();

    if($datseq){
      // seq exist
      $pref = $datseq->prefix;
      $curnum = $datseq->curnum;
      $len = $datseq->numlen;

      // increment current number
      $datseq->curnum = $curnum + 1;
      $datseq->save();
    } else {
      // sequence not exist yet. create it
      $datseq = new EuctSequence;
      $datseq->type = $type;
      $datseq->curnum = $curnum + 1;
      $datseq->numlen = $len;
      $datseq->prefix = $pref;

      $datseq->save();
    }

    return $pref . str_pad($curnum, $len, "0", STR_PAD_LEFT);

  }

  function findBC($costcenter){
		$eubc = EuctBc::where('COST_CENTER',$costcenter)->first();

		if($eubc){
			return $eubc->BC_STAFF_NAME;
		} else {
			return '';
		}

	}

	function getRole($username){
		$eucadmin = EuctAdmin::where('STAFF_ID', $username)->first();

		if($eucadmin){
			return $eucadmin->ROLE_TYPE;
		} else {
			return 'USER';
		}
	}

  function sendEmail(){
    set_error_handler(array($this, 'errorHandler'));
    mail('mohdamer.ahmad@tm.com.my', 'send from laravel', 'hai world!');
    return 'Done';
  }

  function logs($staffid, $action, $remark){
    $insertlog = new EuctLog;
    $insertlog->STAFF_ID = $staffid;
    $insertlog->ACTION = $action;
    $insertlog->DETAILS = json_encode($remark);

    $insertlog->save();
  }



}
