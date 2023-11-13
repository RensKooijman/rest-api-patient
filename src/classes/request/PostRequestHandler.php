<?php

namespace Acme\classes\request;

use Acme\classes\renderer\IRendererInterface;
use Exception;

class PostRequestHandler extends RequestHandler
{

    /**
     * @throws Exception
     */
    public function processRequest(): void
    {
        foreach ($this->data as $field => $value) {
            $this->obj->setColumnValue($field, $value);
        }
        $this->responseData['result'] = (string)$this->obj->insert();
    }

    public function sendResponse(IRendererInterface $renderer): string
    {
        header("Content-type: $this->acceptType; charset=UTF-8");
        return $renderer->renderData($this->responseData);
    }
}