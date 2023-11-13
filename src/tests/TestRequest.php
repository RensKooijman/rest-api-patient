<?php

namespace Acme\tests;

require '../../vendor/autoload.php';

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;

abstract class TestRequest
{
    abstract function getRequest(Client $client, string $url, string $data): Request;
}