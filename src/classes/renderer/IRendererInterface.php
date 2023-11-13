<?php

namespace Acme\classes\renderer;

interface IRendererInterface
{
    public function renderData(array $data): string;
}