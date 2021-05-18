<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Aacotroneo\Saml2\Http\Controllers\Saml2Controller;

class SRSaml2Controller extends Saml2Controller
{
	public function login(\Aacotroneo\Saml2\Saml2Auth $saml2Auth){
		// Determine redirect URL
		// for now, the home page 
		$loginRedirect = '/'; 
		$saml2Auth->login($loginRedirect);
	}
}
