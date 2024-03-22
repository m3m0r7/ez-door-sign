<?php

declare(strict_types=1);

namespace EzDoorSign\Renderer;

interface ImageTransformerInterface
{
    public function resizeOriginal(): ImageTransformerInterface;

    public function distributeFromOriginal(): ImageTransformerInterface;

    public function adaptiveColoringThreshold(): ImageTransformerInterface;

    public function transpose(): ImageTransformerInterface;

    /**
     * @return array{r: int, g: int, b: int, a: int}
     */
    public function colorAt(int $x, int $y): array;
}
