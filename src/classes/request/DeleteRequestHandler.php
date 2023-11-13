<?php

namespace Acme\classes\request;

use Acme\classes\renderer\IRendererInterface;

class DeleteRequestHandler extends RequestHandler
{
    public function processRequest(): void
    {
        $this->responseData['result'] = (string)$this->obj->delete($this->identifier);
    }

    public function sendResponse(IRendererInterface $renderer): string
    {
        header("Content-Type: $this->acceptType; charset=UTF-8");
        return $renderer->renderData($this->responseData);
    }
}