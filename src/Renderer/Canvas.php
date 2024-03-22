<?php

declare(strict_types=1);

namespace EzDoorSign\Renderer;

class Canvas implements CanvasInterface
{
    public const CANVAS_WIDTH = 0x0128;

    public const CANVAS_HEIGHT = 128;

    protected AlignmentHorizontal $alignmentHorizontal = AlignmentHorizontal::CENTER;

    protected AlignmentVertical $alignmentVertical = AlignmentVertical::CENTER;

    protected bool $enableResize = true;

    protected ImageTransformerInterface $imageTransformer;

    #[\Override]
    public function width(): int
    {
        return self::CANVAS_WIDTH;
    }

    #[\Override]
    public function height(): int
    {
        return self::CANVAS_HEIGHT;
    }

    public function __construct(protected RendererInterface $renderer)
    {
        $this->imageTransformer = new ImageTransformer(
            $this,
            $this->renderer->render()->path(),
        );
    }

    public function setAlignmentHorizontal(AlignmentHorizontal $alignmentHorizontal): CanvasInterface
    {
        $this->alignmentHorizontal = $alignmentHorizontal;

        return $this;
    }

    public function setAlignmentVertical(AlignmentVertical $alignmentVertical): CanvasInterface
    {
        $this->alignmentVertical = $alignmentVertical;

        return $this;
    }

    /**
     * @return array{0: int, 1: int, 2: int, 3: int}
     */
    #[\Override]
    public function calculateBoundingBoxByAlignment(int $width, int $height): array
    {
        $aspectRatio = (int) ($width / $height);

        if ($height < $this->height()) {
            if ($width > $this->width()) {
                $aspectRatio = (int) ($height / $width);
                $resizedWidth = $this->width();
                $resizedHeight = $this->width() * $aspectRatio;
            } else {
                $resizedWidth = $width;
                $resizedHeight = $height;
            }
        } else {
            $resizedWidth = $this->height() * $aspectRatio;
            $resizedHeight = $this->height();
        }

        $x = match ($this->alignmentHorizontal) {
            AlignmentHorizontal::CENTER => (int) (($this->width() - $resizedWidth) / 2),
            AlignmentHorizontal::LEFT => 0,
            AlignmentHorizontal::RIGHT => $this->width() - $resizedWidth,
        };
        $y = match ($this->alignmentVertical) {
            AlignmentVertical::CENTER => (int) (($this->height() - $resizedHeight) / 2),
            AlignmentVertical::TOP => 0,
            AlignmentVertical::BOTTOM => $this->height() - $resizedHeight,
        };

        return [$resizedWidth, $resizedHeight, $x, $y];
    }

    #[\Override]
    public function transform(): CanvasInterface
    {
        $this->imageTransformer
            ->resizeOriginal()
            ->distributeFromOriginal()
            ->adaptiveColoringThreshold()
            ->transpose();

        return $this;
    }

    #[\Override]
    public function colorAt(int $x, int $y): array
    {
        return $this->imageTransformer->colorAt($x, $y);
    }
}
