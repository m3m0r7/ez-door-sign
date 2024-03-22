<?php

declare(strict_types=1);

namespace EzDoorSign;

class ImageFilePath implements ImageFileInterface
{
    public function __construct(protected string $path) {}

    #[\Override]
    public function path(): string
    {
        return $this->path;
    }
}
