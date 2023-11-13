<?php

namespace Acme\classes\request;

use Acme\classes\renderer\IRendererInterface;

class PutRequestHandler extends RequestHandler
{
    public function processRequest(): void
    {
        $objResult = $this->obj->getByPrimaryKey($this->identifier);
        foreach ($this->data as $field => $value) {
            $objResult->setColumnValue($field, $value);
        }
        $objResult->update($this->identifier);
        $this->responseData['result'] = "1";
    }

    public function sendResponse(IRendererInterface $renderer): string
    {
        header("Content-type: $this->acceptType; charset=UTF-8");
        return $renderer->renderData($this->responseData);
    }
}