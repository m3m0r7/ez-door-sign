<?php

declare(strict_types=1);

namespace Tests\EzDoorSign\Unit\OS\MacOS;

use EzDoorSign\EzDoorSign;
use EzDoorSign\ManipulatorInterface;
use Libusb\Connector\Libusb1_0;
use Libusb\Libusb;
use Libusb\LibusbHandle;
use Libusb\Loader\AutoLoader;

trait CreateApplication
{
    private ?ManipulatorInterface $manipulator = null;

    private ?string $fontPath = '/System/Library/Fonts/Supplemental/Arial Unicode.ttf';

    private ?string $imagePath = __DIR__ . '/../../../example.jpg';

    public function setUp(): void
    {
        $libusb = new Libusb(
            new LibusbHandle(
                new Libusb1_0(
                    new AutoLoader(
                        '1.0',
                        // for mac
                        is_dir('/opt/homebrew/Cellar/libusb')
                            ? glob('/opt/homebrew/Cellar/libusb/1.0.*') ?: []
                            : []
                    ),
                )
            ),
        );

        $ezDoorSign = new EzDoorSign($libusb);

        // @var ManipulatorInterface $manipulator
        [$this->manipulator] = $ezDoorSign->devices();
    }
}
