<?php

namespace Acme\classes\request;

use Acme\classes\renderer\IRendererInterface;

class GetRequestHandler extends RequestHandler
{
    public function processRequest(): void
    {
        if(isset($this->data['identifier'])) {
            $arrResults = $this->obj->getByPrimaryKey($this->data['identifier'], $this->fields);
            $result = $arrResults->getColumns();
        } else {
            $arrResults = $this->obj->getAll($this->fields);
            $result = [];
            foreach ($arrResults as $objResult) {
                $result[] = $objResult->getColumns();
            }
        }

        $this->responseData['result'] = $result;
    }

    public function sendResponse(IRendererInterface $renderer): string
    {
        header("Content-type: $this->acceptType; charset=UTF-8");
        return $renderer->renderData($this->responseData['result']);
    }
}