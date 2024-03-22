<?php

declare(strict_types=1);

namespace EzDoorSign\Command;

enum ColorPattern
{
    case BLACK;
    case WHITE;
    case RED;
    case BLUE;

    /**
     * @return array{0: int, 1: int}
     */
    public function fillPatterns(): array
    {
        return match ($this) {
            self::BLUE, self::BLACK => [0x00, 0x00],
            self::WHITE => [0xFF, 0x00],
            self::RED => [0x00, 0xFF],
        };
    }
}
