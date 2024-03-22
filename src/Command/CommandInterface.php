<?php

declare(strict_types=1);

namespace EzDoorSign\Command;

interface CommandInterface
{
    public function process(): void;
}
