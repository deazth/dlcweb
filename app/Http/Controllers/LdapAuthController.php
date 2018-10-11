<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LdapAuthController extends BaseController
{

	/**
	*	authenticate the provided credential with ldap
	*/
	function doLogin(Request $req){

		set_error_handler(array($this, 'errorHandler'));
		$errorcode = 200;

		// first, validate the input
		$input = app('request')->all();
		$rules = [
			'username' => ['required'],
			'password' => ['required']
		];

		$validator = app('validator')->make($input, $rules);
		if($validator->fails()){
			return $this->respond_json(412, 'Invalid input', $input);
		}

		$username = $req->username;
		$password = $req->password;

		// do the ldap things
		$errm = 'success';

		$udn = "cn=$username,ou=users,o=data";
		$hostnameSSL = env('TMLDAP_HOSTNAME', 'ldaps://idssldap.tm.com.my:636');
		//	ldap_set_option(NULL, LDAP_OPT_DEBUG_LEVEL, 7);
		putenv('LDAPTLS_REQCERT=never');

		$con =  ldap_connect($hostnameSSL);
		if (is_resource($con)){
			if (ldap_set_option($con, LDAP_OPT_PROTOCOL_VERSION, 3)){
				ldap_set_option($con, LDAP_OPT_REFERRALS, 0);

				// try to mind / authenticate
				try{
				if (ldap_bind($con,$udn, $password)){
					$errm = 'success';
				} else {
					$errorcode = 401;
					$errm = 'Invalid credentials.';
				}} catch(Exception $e) {
					$errorcode = 500;
					$errm = $e->getMessage();
				}

			} else {
				$errorcode = 500;
				$errm = "TLS not supported. Unable to set LDAP protocol version to 3";
			}

			// clean up after done
			ldap_close($con);

		} else {
			$errorcode = 500;
			$errm = "Unable to connect to $hostnameSSL";
		}

		return $this->respond_json($errorcode, $errm);

	}


	/**
	*	get the information for the requested user
	*	to be used internally
	*/
	function fetchUser($username, $searchtype = 'id'){

		set_error_handler(array($this, 'errorHandler'));

		// do the ldap things
		$errm = 'success';
		$errorcode = 200;
	  $udn= 'cn=novabillviewerldapadmin, ou=serviceAccount, o=Telekom';
	  $password = 'nHQUbG9Z';
		$hostnameSSL = env('TMLDAP_HOSTNAME', 'ldaps://idssldap.tm.com.my:636');
		$retdata = [];
		//	ldap_set_option(NULL, LDAP_OPT_DEBUG_LEVEL, 7);
		putenv('LDAPTLS_REQCERT=never');

		$stype = 'cn';
		if(strcasecmp($searchtype,'name') == 0){
			$stype = 'sn';
		}

		$con =  ldap_connect($hostnameSSL);
		if (is_resource($con)){
			if (ldap_set_option($con, LDAP_OPT_PROTOCOL_VERSION, 3)){
				ldap_set_option($con, LDAP_OPT_REFERRALS, 0);

				// try to bind / authenticate
				try{
				if (ldap_bind($con,$udn, $password)){

					// perform the search
					$ldres = ldap_search($con, 'ou=users,o=data', "$stype=$username");
					$retdata = ldap_get_entries($con, $ldres);

					if($retdata['count'] == 0){
						$errorcode = 404;
						$errm = 'user not found';
					}

				} else {
					$errorcode = 403;
					$errm = 'Invalid admin credentials.';
				}} catch(Exception $e) {
					$errorcode = 500;
					$errm = $e->getMessage();
				}

			} else {
				$errorcode = 500;
				$errm = "TLS not supported. Unable to set LDAP protocol version to 3";
			}

			// clean up after done
			ldap_close($con);

		} else {
			$errorcode = 500;
			$errm = "Unable to connect to $hostnameSSL";
		}

		return $this->respond_json($errorcode, $errm, $retdata);
	}

	// to be called by API
	function getUserInfo(Request $req){
		// first, validate the input
		$input = app('request')->all();
		$rules = [
			'key' => ['required'],
			'type' => ['required']
		];

		$validator = app('validator')->make($input, $rules);
		if($validator->fails()){
			return $this->respond_json(412, 'Invalid input', $input);
		}

		return $this->fetchUser($req->key, $req->type);
	}


	function authcon(Request $req){

		$input = app('request')->all();
		return $input;
	}
}
