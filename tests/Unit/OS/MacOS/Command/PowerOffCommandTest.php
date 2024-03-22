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
class PowerOffCommandTest extends TestCase
{
    use CreateApplication;

    public function testPowerOff(): void
    {
        $this->expectNotToPerformAssertions();
        $this->manipulator?->powerOff();

        sleep(10);
    }
}
