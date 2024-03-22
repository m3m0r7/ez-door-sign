<?php

declare(strict_types=1);

namespace Tests\EzDoorSign\Unit\OS\MacOS\Command;

use PHPUnit\Framework\TestCase;
use Tests\EzDoorSign\Unit\OS\MacOS\CreateApplication;

/**
 * @internal
 *
 * @coversNothing
 */
class ControlNextCommandTest extends TestCase
{
    use CreateApplication;

    public function testControlNext(): void
    {
        $this->expectNotToPerformAssertions();
        $this->manipulator?->controlNext();

        sleep(10);
    }
}
