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
class RefreshCommandTest extends TestCase
{
    use CreateApplication;

    public function testRefresh(): void
    {
        $this->expectNotToPerformAssertions();
        $this->manipulator?->refresh();

        sleep(10);
    }
}
