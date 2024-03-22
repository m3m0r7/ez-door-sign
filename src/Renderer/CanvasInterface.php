<?php

declare(strict_types=1);

namespace EzDoorSign\Renderer;

interface CanvasInterface
{
    public function width(): int;

    public function height(): int;

    /**
     * @return array{0: int, 1: int, 2: int, 3: int}
     */
    public function calculateBoundingBoxByAlignment(int $width, int $height): array;

    public function transform(): CanvasInterface;

    /**
     * @return array{r: int, g: int, b: int, a: int}
     */
    public function colorAt(int $x, int $y): array;
}
