<?php

namespace App\Http\Controllers;

use Dingo\Api\Routing\Helpers;
use Illuminate\Routing\Controller;

class BaseController extends Controller
{
	use Helpers;

	function errorHandler($errno, $errstr) {
		return $this->respond_json($errno, $errstr);
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

}
