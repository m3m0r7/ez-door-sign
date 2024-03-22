<?php

declare(strict_types=1);

namespace EzDoorSign;

class ImageFile implements ImageFileInterface
{
    /**
     * @param resource $resource
     */
    public function __construct(protected mixed $resource) {}

    #[\Override]
    public function path(): string
    {
        return stream_get_meta_data($this->resource)['uri'];
    }
}
