<?php

declare(strict_types=1);

namespace EzDoorSign\Util;

class Formatter
{
    /**
     * @param int[] $array
     */
    public static function toHexFromNumberArray(array $array, string $delimiter = ' '): string
    {
        return implode(
            $delimiter,
            array_map(
                static fn(int $number) => sprintf('%02X', $number),
                $array,
            ),
        );
    }

    public static function toHexFromString(string $string, string $delimiter = ''): string
    {
        return self::toHexFromNumberArray(
            unpack('C*', $string) ?: []
        );
    }
}
