<?php
namespace Acme;

use Acme\classes\renderer\RendererFactory;
use Acme\classes\request\Request;
use Acme\classes\request\RequestHandlerFactory;

require_once "../vendor/autoload.php";

// Read request
$request = new Request();

// Get the best handler for the request (post, get, put, delete)
$requestHandler = (new RequestHandlerFactory($request))->getRequestHandler();
$requestHandler->processRequest();

// Get a suitable renderer to send the data in the right form (json, xml)
$rendererHandler = (new RendererFactory($request))->getRenderer();

// Send response
echo $requestHandler->sendResponse($rendererHandler);