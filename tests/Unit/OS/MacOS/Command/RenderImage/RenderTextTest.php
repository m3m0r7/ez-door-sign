<?php

declare(strict_types=1);

namespace Tests\EzDoorSign\Unit\OS\MacOS\Command\RenderImage;

use EzDoorSign\Command\ColorPattern;
use EzDoorSign\Command\RenderImageCommand;
use EzDoorSign\Renderer\Canvas;
use PHPUnit\Framework\TestCase;
use Tests\EzDoorSign\Unit\OS\MacOS\CreateApplication;
use EzDoorSign\Renderer\TextRenderer;

/**
 * @internal
 *
 * @coversNothing
 */
class RenderTextTest extends TestCase
{
    use CreateApplication;

    #[\Override]
    public function tearDown(): void
    {
        // Wait refreshing a device
        sleep(10);

        parent::tearDown();
    }

    public function testRenderTextRed(): void
    {
        $this->expectNotToPerformAssertions();
        $this->manipulator?->renderImage(
            new Canvas(new TextRenderer('Hello World!', $this->fontPath ?? '')),
            static fn (RenderImageCommand $renderImage) => $renderImage
                ->setFillColor(ColorPattern::RED),
        );
    }

    public function testRenderTextBlack(): void
    {
        $this->expectNotToPerformAssertions();
        $this->manipulator?->renderImage(
            new Canvas(new TextRenderer('Hello World!', $this->fontPath ?? '')),
        );
    }

    public function testRenderTextWhite(): void
    {
        $this->expectNotToPerformAssertions();
        $this->manipulator?->renderImage(
            new Canvas(new TextRenderer('Hello World!', $this->fontPath ?? '')),
            static fn (RenderImageCommand $renderImage) => $renderImage
                ->setBackgroundColor(ColorPattern::BLACK)
                ->setFillColor(ColorPattern::WHITE),
        );
    }
}
