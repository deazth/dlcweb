<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Dingo\Api\Routing\Helpers;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests, Helpers;

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

  	function returnIndex(){
  		$response = [
  			'title' => env('API_NAME'),
  			'api-version' => env('API_VERSION'),
  			'lumen-version' => app()->version(),
  			'author' => env('API_AUTHOR'),
  			'created' => '2018-10-14',
  			'updated' => '2018-10-31',
  		];
  		return response()->json($response);
  	}
}
