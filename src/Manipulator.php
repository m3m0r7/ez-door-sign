<?php

declare(strict_types=1);

namespace EzDoorSign;

use EzDoorSign\Command\ControlNextCommand;
use EzDoorSign\Command\ControlPreviousCommand;
use EzDoorSign\Command\PowerOffCommand;
use EzDoorSign\Command\RefreshCommand;
use EzDoorSign\Command\RenderImageCommand;
use EzDoorSign\Exception\FailedToReceive;
use EzDoorSign\Renderer\CanvasInterface;
use EzDoorSign\Util\Formatter;
use Libusb\Connector\Transfer\BulkTransferEndpointManipulatorInterface;
use Libusb\Device\DeviceInterface;
use Libusb\Stream\Packet;
use Psr\Log\LoggerInterface;

class Manipulator implements ManipulatorInterface
{
    protected int $waitTime = 50000;

    protected BulkTransferEndpointManipulatorInterface $bulkTransferManipulator;

    public function __construct(protected EzDoorSign $ezDoorSign, protected DeviceInterface $device, protected LoggerInterface $logger)
    {
        $this->bulkTransferManipulator = $this->device
            ->bulkTransferEndpoints();
    }

    #[\Override]
    public function refresh(): ManipulatorInterface
    {
        $this->logger->info('Start to refresh command');

        (new RefreshCommand($this))
            ->process();

        $this->logger->info('Finished to refresh command');

        return $this;
    }

    #[\Override]
    public function controlNext(): ManipulatorInterface
    {
        $this->logger->info('Start to control next command');

        (new ControlNextCommand($this))
            ->process();

        $this->logger->info('Finished to control next command');

        return $this;
    }

    #[\Override]
    public function controlPrevious(): ManipulatorInterface
    {
        $this->logger->info('Start to control previous command');

        (new ControlPreviousCommand($this))
            ->process();

        $this->logger->info('Finished to control previous command');

        return $this;
    }

    #[\Override]
    public function powerOff(): ManipulatorInterface
    {
        $this->logger->info('Start to power off command');

        (new PowerOffCommand($this))
            ->process();

        $this->logger->info('Start to power off command');

        return $this;
    }

    #[\Override]
    public function renderImage(CanvasInterface $image, ?callable $callback = null, int $slot = 0): ManipulatorInterface
    {
        $this->logger->info('Start to render an image command');

        $render = new RenderImageCommand($this, $image, $slot, $this->logger);
        $callback ??= static fn (RenderImageCommand $renderImage): RenderImageCommand => $renderImage;

        $callback($render)
            ->process();

        $this->logger->info('Finished to render an image command');

        return $this;
    }

    /**
     * @param int[] $payload
     */
    #[\Override]
    public function send(array $payload): void
    {
        $this->bulkTransferManipulator
            ->send((string) new Packet($packet = [
                0xBB,
                0x00,
                ...$payload,

                // Check digit
                array_sum($payload) & 0xFF,
                0x7E,
            ]));

        $this->logger->debug(sprintf('Sent packet [%s]', Formatter::toHexFromNumberArray($packet)));

        // Wait for processing
        usleep($this->waitTime);
    }

    #[\Override]
    public function receive(int $size): string
    {
        $packet = current(
            $this->bulkTransferManipulator
                ->receive($size) !== [] ? $this->bulkTransferManipulator
                ->receive($size) : throw new FailedToReceive(
                    'Failed to receive packets from the device',
                ),
        );

        $this->logger->debug(sprintf('Received packet [%s]', Formatter::toHexFromString($packet)));

        return $packet;
    }
}
