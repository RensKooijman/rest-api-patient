<?php

namespace Acme\classes\request;


use http\Exception\UnexpectedValueException;

class RequestHandlerFactory
{
    private Request $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * @return RequestHandler
     */
    public function getRequestHandler(): RequestHandler
    {
        $method = $this->request->getMethod();
        return match ($method) {
            'GET' => new GetRequestHandler($this->request),
            'POST' => new PostRequestHandler($this->request),
            'PUT' => new PutRequestHandler($this->request),
            'DELETE' => new DeleteRequestHandler($this->request),
            default => throw new UnexpectedValueException("No request handler available for method " . $method),
        };
    }
}