<?php

namespace Acme\classes\renderer;

use DOMDocument;

class XmlRenderer implements IRendererInterface
{
    /**
     * Turns data into xml document
     * @param array $data
     * @return string
     */
    public function renderData(array $data): string
    {
        $doc = new DOMDocument();
        $doc->appendChild($doc->createElement('content', $data));
        return $doc->saveXML();
    }
}