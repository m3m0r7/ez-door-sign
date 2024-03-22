<?php

declare(strict_types=1);

namespace EzDoorSign\Command;

use EzDoorSign\ManipulatorInterface;

class PowerOffCommand implements CommandInterface
{
    public function __construct(protected ManipulatorInterface $manipulator) {}

    #[\Override]
    public function process(): void
    {
        $this->manipulator->send([0x07, 0x01, 0x00]);
    }
}
