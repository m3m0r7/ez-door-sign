<?php

declare(strict_types=1);

namespace EzDoorSign\Renderer;

use EzDoorSign\ImageFileInterface;
use EzDoorSign\ImageFilePath;

class ImageRenderer implements RendererInterface
{
    public function __construct(protected string $path) {}

    #[\Override]
    public function render(): ImageFileInterface
    {
        return new ImageFilePath($this->path);
    }
}
