<?php

namespace Acme\tests;

require '../../vendor/autoload.php';

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;

class TestRequestPost extends TestRequest
{

    /**
     * @param Client $client
     * @param string $url
     * @param string $data
     * @return Request
     */
    function getRequest(Client $client, string $url, string $data): Request
    {
        return new Request('POST', $url, ['Accept' => 'application/json'], $data);
    }
}