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
class ControlPreviousCommandTest extends TestCase
{
    use CreateApplication;

    public function testControlPrevious(): void
    {
        $this->expectNotToPerformAssertions();
        $this->manipulator?->controlPrevious();

        sleep(10);
    }
}
