<?php

declare(strict_types=1);

namespace EzDoorSign;

use Libusb\Device\DeviceInterface;
use Libusb\Libusb;
use Monolog\Logger;
use Psr\Log\LoggerInterface;

class EzDoorSign
{
    public const VENDOR_ID = 0x1A86;

    public const PRODUCT_ID = 0x55D3;

    protected LoggerInterface $logger;

    public function __construct(protected Libusb $libusb, LoggerInterface $logger = null)
    {
        $this->logger = $logger ?? new Logger('EZDoorSign');
    }

    /**
     * @return ManipulatorInterface[]
     */
    public function devices(): array
    {
        return array_map(
            fn (DeviceInterface $device) => new Manipulator(
                $this,
                $device->setConfiguration(1)
                    ->setClaimInterface(1),
                $this->logger,
            ),
            $this->libusb
                ->devices(
                    static::VENDOR_ID,
                    static::PRODUCT_ID,
                ),
        );
    }
}
