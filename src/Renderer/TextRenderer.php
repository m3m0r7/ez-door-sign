<?php

declare(strict_types=1);

namespace EzDoorSign\Renderer;

use EzDoorSign\Exception\FailedToRenderImage;
use EzDoorSign\ImageFile;
use EzDoorSign\ImageFileInterface;

class TextRenderer implements RendererInterface
{
    public function __construct(protected string $text, protected string $font, protected int $fontSize = 30) {}

    #[\Override]
    public function render(): ImageFileInterface
    {
        $imagick = new \Imagick();
        $temporaryFile = new ImageFile(
            tmpfile() ?: throw new FailedToRenderImage('A temporary file cannot create'),
        );

        $color = new \ImagickPixel();
        $color->setColor('#000000');

        $drawer = new \ImagickDraw();

        $drawer->setFontSize($this->fontSize);
        $drawer->setFillColor($color);
        $drawer->setTextAntialias(false);

        $drawer->setFont($this->font);

        $metrics = $imagick
            ->queryFontMetrics($drawer, $this->text);

        $imagick->newImage(
            (int) $metrics['textWidth'],
            (int) $metrics['textHeight'],
            '#FFFFFF'
        );

        $imagick
            ->annotateImage(
                $drawer,
                0,
                0,
                0,
                $this->text,
            );

        $imagick->writeImage($temporaryFile->path());

        return $temporaryFile;
    }
}
