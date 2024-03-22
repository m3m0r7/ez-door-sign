# Packet Details

## How to read?

Example: Turn off the device command

```
+--+--- The magic packet
|  |
|  |  +--- The payload（Opcode）
|  |  |
|  |  |  +--+--- The payload（Operand）
|  |  |  |  |
|  |  |  |  |  +--- The check digit. Last 1 byte of sum payload bytes.
|  |  |  |  |  |
|  |  |  |  |  |  +--- EoP (End of Packet)
|  |  |  |  |  |  |
bb 00 07 01 00 08 7e
````

## Write to Device

### Refresh displays

```
bb 00 00 01 00 01 7e
```

### Previous Packet
```
bb 00 00 01 ff 00 7e
```


### Next Packet
```
bb 00 00 01 fe ff 7e
```


### Poweroff Packet
```
bb 00 07 01 00 08 7e
```

### Read bitmap

```
            +--- Slot number （0〜4）
            |
            |  +--- Canvas number（1st: 0x00, 2nd: 0x01）
            |  |
            |  |  +--+--- Row number (0x0000〜0x0127)
            |  |  |  |
bb 00 05 04 00 01 01 27 32 7e

```

### Render an image (書き込み後更新パケットを送る必要あり)

*You must send refresh packet after rendered an image.*

You must send 6 times command when rendering an image.

1. Send starting to render an image command.
2. Send specify rendering layer number (should be zero) command.
3. Send packet paired with row number and bit mapped canvas.
4. Send specify rendering layer number (should be one) command.
5. Send packet paired with row number and bit mapped canvas.
6. Send refresh command.

---

1. Start render an image command as following:
```
            +--- Slot number（0-4）
            |
bb 00 01 01 00 02 7e
```

An order to render color patterns:

| Color Name | 1st time | 2nd time |
|---|---|---|
| Red | 0x00 | 0xff |
| White | 0xff | 0x00 |
| Black | 0x00 | 0x00 |


Specify layer number command:
```
            +--- Slot number（0-4）
            |
            |  +--- Layer number (0, 1)
            |  |
bb 00 03 02 00 00 06 7e
```

Specify bit mapped canvas command:

*NOTE: The `0xff` is established by 8 bit, and if a bit is set then render a pixel. For example to use `0x07`,  the bit sequence is `0000 0111` which means that the left 5 bits are black and the right 3 bits are white.*
```
            +--+---  Row number (0x00〜0x0127)
            |  |
bb 00 04 12 00 00 ff ff ff ff ff ff ff ff ff ff ff ff ff ff ff ff 06 7e
                  |  |  |  |  |  |  |  |  |  |  |  |  |  |  |  |
                  +--------------------------------------------+--- Color
```
