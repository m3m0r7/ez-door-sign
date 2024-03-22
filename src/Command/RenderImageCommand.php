<?php

declare(strict_types=1);

namespace EzDoorSign\Command;

use EzDoorSign\ManipulatorInterface;
use EzDoorSign\Renderer\CanvasInterface;
use Psr\Log\LoggerInterface;

class RenderImageCommand implements CommandInterface
{
    protected ColorPattern $backgroundColor = ColorPattern::WHITE;

    protected ColorPattern $fillColor = ColorPattern::BLACK;

    protected bool $enableCompositeBlackAndRed = true;

    protected bool $enableExpressBlue = false;

    /**
     * @var array{0: int, 1: int, 2: int}
     */
    protected array $redThreshold = [0xFF, 0x00, 0x00];

    /**
     * @var array{0: int, 1: int, 2: int}
     */
    protected array $blueThreshold = [0x00, 0x00, 0xFF];

    public function __construct(protected ManipulatorInterface $manipulator, protected CanvasInterface $canvas, protected int $slot = 0, protected ?LoggerInterface $logger = null) {}

    #[\Override]
    public function process(): void
    {
        $this->canvas->transform();

        $this->manipulator
            ->send([0x01, 0x01, 0x00]);

        for ($layerNumber = 0; $layerNumber <= 1; ++$layerNumber) {
            $this->logger?->info(sprintf('Start to write layer %d', $layerNumber));

            $this->manipulator
                ->send([0x03, 0x02, $this->slot, $layerNumber]);

            for ($i = 0; $i < $this->canvas->width(); ++$i) {
                $this->logger?->debug(sprintf('Writing %d/%d', $i + 1, $this->canvas->width()));

                $colors = $this
                    ->fillBy(
                        $this
                            ->backgroundColor
                            ->fillPatterns()[$layerNumber] ?? 0,
                    );

                for ($j = 0; $j < ($this->canvas->height() / 8); ++$j) {
                    $withinRedColor = $this->withinRedColor($j, $i);
                    $withinBlueColor = $this->withinBlueColor($j, $i);
                    $byte = $this->colorToByte($j, $i);

                    $fillColor = $this->fillColor;

                    if ($this->enableExpressBlue && $withinBlueColor) {
                        $fillColor = ColorPattern::BLUE;
                    } elseif ($this->enableCompositeBlackAndRed && $withinRedColor) {
                        $fillColor = ColorPattern::RED;
                    }

                    // colored a pixel
                    $colors[$j] = match ($fillColor) {
                        ColorPattern::BLACK => match ($layerNumber) {
                            0 => $byte,
                            1 => $colors[$j],
                        },
                        ColorPattern::WHITE => match ($layerNumber) {
                            0 => ~$byte & 0xFF,
                            1 => $colors[$j],
                        },
                        ColorPattern::RED => match ($layerNumber) {
                            0 => $byte,
                            1 => $this->redColorToByte($j, $i),
                        },
                        ColorPattern::BLUE => match ($layerNumber) {
                            0 => ~$this->blueColorToByte($j, $i),
                            1 => $byte & $this->blueColorToByte($j, $i),
                        },
                    };
                }

                $this->manipulator
                    ->send([
                        0x04,
                        0x12,
                        ($i >> 8) & 0xFF,
                        $i & 0xFF,
                        ...$colors,
                    ]);
            }
        }

        $this->manipulator->refresh();
    }

    /**
     * @return int[]
     */
    private function fillBy(int $byte): array
    {
        return array_fill(
            0,
            $this->canvas->height() / 8,
            $byte,
        );
    }

    public function setFillColor(ColorPattern $fillColor): self
    {
        $this->fillColor = $fillColor;

        return $this;
    }

    public function setBackgroundColor(ColorPattern $backgroundColor): self
    {
        $this->backgroundColor = $backgroundColor;

        return $this;
    }

    protected function colorToByte(int $lineNumber, int $y): int
    {
        $bitmap = [];
        for ($i = 0; $i < 8; ++$i) {
            $colorAt = $this->canvas->colorAt(($lineNumber * 8) + $i, $y);
            $bitmap[] = ((255 * 3 + 1) - array_sum($colorAt) > 0)
                ? 0
                : 1;
        }

        return (int) bindec(implode('', $bitmap));
    }

    protected function redColorToByte(int $lineNumber, int $y): int
    {
        $bitmap = [];
        for ($i = 0; $i < 8; ++$i) {
            $bitmap[] = $this->isRed($this->canvas->colorAt(($lineNumber * 8) + $i, $y))
                ? 1
                : 0;
        }

        return (int) bindec(implode('', $bitmap));
    }

    protected function blueColorToByte(int $lineNumber, int $y): int
    {
        $bitmap = [];
        for ($i = 0, $c = 1; $i < 8; ++$i) {
            $bitmap[] = $this->isBlue($this->canvas->colorAt(($lineNumber * 8) + $i, $y))
                ? $c
                : 0;

            $c = $c === 1 ? 0 : 1;
        }

        return (int) bindec(implode('', $bitmap));
    }

    protected function withinRedColor(int $lineNumber, int $y): bool
    {
        for ($i = 0; $i < 8; ++$i) {
            $colorAt = $this
                ->canvas
                ->colorAt(
                    ($lineNumber * 8) + $i,
                    $y,
                );
            if ($this->isRed($colorAt)) {
                return true;
            }
        }

        return false;
    }

    protected function withinBlueColor(int $lineNumber, int $y): bool
    {
        for ($i = 0; $i < 8; ++$i) {
            $colorAt = $this
                ->canvas
                ->colorAt(
                    ($lineNumber * 8) + $i,
                    $y,
                );
            if ($this->isBlue($colorAt)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param array{r: int, g: int, b: int, a: int} $color
     */
    protected function isRed(array $color): bool
    {
        [$redThreshold, $greenThreshold, $blueThreshold] = $this->redThreshold;
        ['r' => $red, 'g' => $green, 'b' => $blue] = $color;

        return $red >= $redThreshold && $green <= $greenThreshold && $blue <= $blueThreshold;
    }

    /**
     * @param array{r: int, g: int, b: int, a: int} $color
     */
    protected function isBlue(array $color): bool
    {
        [$redThreshold, $greenThreshold, $blueThreshold] = $this->blueThreshold;
        ['r' => $red, 'g' => $green, 'b' => $blue] = $color;

        return $red <= $redThreshold && $green <= $greenThreshold && $blue >= $blueThreshold;
    }

    public function setRedThreshold(int $red, int $green, int $blue): self
    {
        $this->redThreshold = [
            $red & 0xFF,
            $green & 0xFF,
            $blue & 0xFF,
        ];

        return $this;
    }

    public function setBlueThreshold(int $red, int $green, int $blue): self
    {
        $this->blueThreshold = [
            $red & 0xFF,
            $green & 0xFF,
            $blue & 0xFF,
        ];

        return $this;
    }

    public function enableCompositeBlackAndRed(bool $which): self
    {
        $this->enableCompositeBlackAndRed = $which;

        return $this;
    }

    public function enableExpressBlue(bool $which): self
    {
        $this->enableExpressBlue = $which;

        return $this;
    }
}
