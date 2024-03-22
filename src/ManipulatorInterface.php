<?php

declare(strict_types=1);

namespace EzDoorSign;

use EzDoorSign\Renderer\CanvasInterface;

interface ManipulatorInterface
{
    public function refresh(): ManipulatorInterface;

    public function controlNext(): ManipulatorInterface;

    public function controlPrevious(): ManipulatorInterface;

    public function powerOff(): ManipulatorInterface;

    public function renderImage(CanvasInterface $image, ?callable $callback = null, int $slot = 0): ManipulatorInterface;

    /**
     * @param int[] $payload
     */
    public function send(array $payload): void;

    public function receive(int $size): string;
}
