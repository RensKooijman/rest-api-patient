<?php

namespace Acme\classes\renderer;

use Acme\classes\request\Request;

class RendererFactory
{
    private string $mimeType;

    public function __construct(Request $request)
    {
        $this->mimeType = $request->getAcceptType()[0];
    }

    /**
     * @return IRendererInterface   ie xml or json renderer
     */
    public function getRenderer(): IRendererInterface
    {
        return match ($this->mimeType) {
            'application/xml' => new XmlRenderer(),
            default => new JsonRenderer(),
        };
    }
}