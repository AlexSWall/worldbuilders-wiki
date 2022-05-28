<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Controllers\Controller;
use App\Helpers\FrontEndDataUtilities;

use Slim\Http\Response;
use Slim\Http\ServerRequest as Request;

class AdministrationController extends Controller
{
	public function index( Request $_request, Response $response )
	{
		return FrontEndDataUtilities::getEntryPointResponse( $this->view, $response, 'administration' );
	}
}
