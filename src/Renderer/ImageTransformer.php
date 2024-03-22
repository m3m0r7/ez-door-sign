<?php

declare(strict_types=1);

namespace EzDoorSign\Renderer;

class ImageTransformer implements ImageTransformerInterface
{
    protected \Imagick $original;

    protected \Imagick $dist;

    public function __construct(protected CanvasInterface $canvas, protected string $path)
    {
        $this->dist = new \Imagick();
        $this->dist->newImage(
            $this->canvas->width(),
            $this->canvas->height(),
            '#FFFFFF00'
        );

        $this->original = new \Imagick($this->path);
    }

    #[\Override]
    public function resizeOriginal(): ImageTransformerInterface
    {
        ['width' => $width, 'height' => $height] = $this
            ->original
            ->getImageGeometry();

        [$resizedWidth, $resizedHeight] = $this
            ->canvas
            ->calculateBoundingBoxByAlignment($width, $height);

        $this->original
            ->resizeImage(
                $resizedWidth,
                $resizedHeight,
                0,
                0,
            );

        return $this;
    }

    #[\Override]
    public function distributeFromOriginal(): ImageTransformerInterface
    {
        ['width' => $width, 'height' => $height] = $this
            ->original
            ->getImageGeometry();

        [$resizedWidth, $resizedHeight, $x, $y] = $this
            ->canvas
            ->calculateBoundingBoxByAlignment($width, $height);

        $drawer = new \ImagickDraw();
        $drawer->composite(
            \Imagick::COMPOSITE_COPY,
            $x,
            $y,
            $resizedWidth,
            $resizedHeight,
            $this->original,
        );

        $this->dist->drawImage($drawer);

        return $this;
    }

    #[\Override]
    public function adaptiveColoringThreshold(): ImageTransformerInterface
    {
        $this->dist->whiteThresholdImage('#CCCCCC');
        $this->dist->adaptiveThresholdImage(
            $this->canvas->width(),
            $this->canvas->height(),
            -12500,
        );

        return $this;
    }

    #[\Override]
    public function transpose(): ImageTransformerInterface
    {
        $this->dist->transposeImage();

        return $this;
    }

    #[\Override]
    public function colorAt(int $x, int $y): array
    {
        return $this
            ->dist
            ->getImagePixelColor(
                $x,
                $y,
            )->getColor();
    }
}
