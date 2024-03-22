<?php

declare(strict_types=1);

namespace EzDoorSign\Command;

use EzDoorSign\ManipulatorInterface;

class ControlPreviousCommand implements CommandInterface
{
    public function __construct(protected ManipulatorInterface $manipulator) {}

    #[\Override]
    public function process(): void
    {
        $this->manipulator->send([0x00, 0x01, 0xFF]);
    }
}
