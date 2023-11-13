<?php

namespace Acme\classes\renderer;

class JsonRenderer implements IRendererInterface
{
    /**
     * Turns data into json document
     * @param array $data
     * @return string
     */
    public function renderData(array $data): string
    {
        return json_encode($data);
    }
}