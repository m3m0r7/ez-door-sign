<?php

declare(strict_types=1);

namespace EzDoorSign\Renderer;

use EzDoorSign\ImageFileInterface;

interface RendererInterface
{
    public function render(): ImageFileInterface;
}
