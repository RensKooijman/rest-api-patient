<?php

namespace Acme\classes\request;

use GuzzleHttp\Exception\GuzzleException;
use http\Exception\BadHeaderException;

class Request
{
    private array $headers;
    private string $contentType;
    private array $acceptType;
    private string $method;
    private array $data = [];

    public function __construct()
    {
        // get the method from the request; post, get, put, delete
        $this->method = $_SERVER['REQUEST_METHOD'];

        // get the headers from the request
        $this->headers = apache_request_headers();

        $this->setContentType();
        $this->setAcceptType();

        // TODO: check for valid query string!
        // Get resource (table), identifier (id), fields to show (table columns) in case of a get request
        parse_str($_SERVER['QUERY_STRING'], $arr);
        $this->data['resource'] = $arr['resource'];
        if (isset($arr['identifier'])) {
            $this->data['identifier'] = $arr['identifier'];
        }
        if (isset($arr['fields'])) {
            $this->data['fields'] = $arr['fields'];
        }

        // Get the data
        //  in case of post, put or delete request
        $content = file_get_contents('php://input');
        if ($this->contentType === "application/json") {
            $arr = json_decode($content, true);
        } elseif ($this->contentType === "x-www-form-urlencoded" || $this->contentType === "multipart/form-data" || $this->contentType === "text/plain") {
            parse_str($content, $arr);
        } else {
            throw new BadHeaderException(
                'Content-type should be x-www-form-urlencoded of application/json.'
            );
        }
        if(isset($this->data['identifier'])) {
            $arr['identifier'] = $this->data['identifier'];
        }
        $this->data['data'] = $arr;
        $this->validateQueryString($_SERVER['QUERY_STRING']);
    }

    private function setContentType(): void
    {
        if (!isset($this->headers['Content-Type'])) {
            $this->contentType = 'multipart/form-data';
        } else {
            $this->contentType = explode(";", $this->headers['Content-Type'])[0];
        }
    }

    private function setAcceptType(): void
    {
        if (!isset($this->headers['Accept']) || $this->headers['Accept'] === "*/*") {
            $this->acceptType = ['application/json'];
        } else {
            $this->acceptType = explode(",", $this->headers['Accept']);
        }
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getAcceptType(): string
    {
        return $this->acceptType[0];
    }

    public function validateQueryString(string $string): void
    {
        if(!is_string($string)){
            throw new BadHeaderException(
                'Invalid type input expected string else given'
            );
        }
        parse_str($_SERVER['QUERY_STRING'], $arr);
        if(!is_array($arr)){
            throw new BadHeaderException(
                'something went wrong converting query string to values and identifiers'
            ); 
        }
        switch($this->getMethod()){
            case 'GET';
                if(empty($arr['fields']) | empty($arr['resource'])){
                    throw new BadHeaderException(
                        'not all needed fields given. needed fields: "fields, identifier, resource"'
                    );  
                }
                break;
            case 'POST':
                if(empty($arr['identifier']) | empty($this->getData()) | empty($arr['resource'])){
                    throw new BadHeaderException(
                        'not all needed fields given. needed fields: "data (body), identifier, resource"'
                    );
                }
                break;
            case 'PUT':
                if(empty($arr['identifier']) | empty($this->getData()) | empty($arr['resource'])){
                    throw new BadHeaderException(
                        'not all needed fields given. needed fields: "data (body), identifier, resource"'
                    );
                }
                break;
            case 'DELETE':
                if(empty($arr['identifier']) |  empty($arr['resource'])){
                    throw new BadHeaderException(
                        'not all needed fields given. needed fields: " identifier, resource"'
                    );
                }
                break;
            default:
                throw new BadHeaderException(
                    'no method given or method is not implemented'
                );
                break;
        }
    }
}