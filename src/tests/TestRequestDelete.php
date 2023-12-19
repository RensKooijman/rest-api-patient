<?php

namespace Acme\tests;

require(__DIR__ . '/../../vendor/autoload.php');

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;

class TestRequestDelete extends TestRequest
{

    /**
     * @param Client $client
     * @param string $url
     * @param string $data
     * @return Request
     */
    function getRequest(Client $client, string $url, string $data): Request
    {
        return new Request('DELETE', $url, ['Accept' => 'application/json'], $data);
    }
}