<?php

declare(strict_types=1);

namespace Tests\EzDoorSign\Unit\OS\MacOS\Command\RenderImage;

use EzDoorSign\Command\ColorPattern;
use EzDoorSign\Command\RenderImageCommand;
use EzDoorSign\Renderer\Canvas;
use PHPUnit\Framework\TestCase;
use Tests\EzDoorSign\Unit\OS\MacOS\CreateApplication;
use EzDoorSign\Renderer\ImageRenderer;

/**
 * @internal
 *
 * @coversNothing
 */
class RenderImageTest extends TestCase
{
    use CreateApplication;

    #[\Override]
    public function tearDown(): void
    {
        // Wait refreshing a device
        usleep(50000 * (0x0128 + 3));
        sleep(10);

        parent::tearDown();
    }

    public function testRenderImageRed(): void
    {
        $this->expectNotToPerformAssertions();
        $this->manipulator?->renderImage(
            new Canvas(new ImageRenderer(__DIR__ . '/tests/example.jpg')),
            static fn (RenderImageCommand $renderImage) => $renderImage
                ->setFillColor(ColorPattern::RED),
        );
    }

    public function testRenderImageBlack(): void
    {
        $this->expectNotToPerformAssertions();
        $this->manipulator?->renderImage(
            new Canvas(new ImageRenderer(__DIR__ . '/tests/example.jpg')),
        );
    }

    public function testRenderImageWhite(): void
    {
        $this->expectNotToPerformAssertions();
        $this->manipulator?->renderImage(
            new Canvas(new ImageRenderer(__DIR__ . '/tests/example.jpg')),
            static fn (RenderImageCommand $renderImage) => $renderImage
                ->setBackgroundColor(ColorPattern::BLACK)
                ->setFillColor(ColorPattern::WHITE),
        );
    }
}
