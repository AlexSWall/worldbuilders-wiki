<?php

namespace App\Controllers\Auth;

use App\Controllers\Controller;
use App\Helpers\FrontEndDataUtils;

class AdministrationController extends Controller
{
	public function index($request, $response)
	{
		return FrontEndDataUtils::getEntryPointResponse(	$this->view, $response, 'administration' );
	}
}