<?php

namespace Codebender\BoardBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Codebender\BoardBundle\Entity\Board;use Doctrine\Common\DataFixtures\AbstractFixture;

/* Load Board Data
 * 
 * Provides default Board Data for CodeBender.cc
 */
class LoadBoardData extends AbstractFixture implements FixtureInterface
{
    /**
     * Load data into Boards Table of Database
     *
     * This function is executed when the user runs the command: app/console doctrine:fixtures:load
     */
    public function load(ObjectManager $manager)
    {
        $arduinoUno = new Board();
        $arduinoUno->setName('Arduino Uno');
        $arduinoUno->setDescription('The Uno is the reference model for the Arduino platform. It has 14 digital input/output pins (of which 6 can be used as PWM outputs), 6 analog inputs, a 16 MHz ceramic resonator, a USB connection, a power jack, an ICSP header, and a reset button. It does not use the FTDI USB-to-serial driver chip. Instead, it features the Atmega16U2 (Atmega8U2 up to version R2) programmed as a USB-to-serial converter.');
        $arduinoUno->setUpload('{"protocol":"arduino","maximum_size":"32256","speed":"115200"}');
        $arduinoUno->setBootloader('{"low_fuses":"0xff","high_fuses":"0xde","extended_fuses":"0x05","path":"optiboot","file":"optiboot_atmega328.hex","unlock_bits":"0x3F","lock_bits":"0x0F"}');
        $arduinoUno->setBuild('{"mcu":"atmega328p","f_cpu":"16000000L","core":"arduino","variant":"standard"}');
        $manager->persist($arduinoUno);
        
        $arno = new Board();    
        $arno->setName('Arno');
        $arno->setDescription('Learning the basics of electronics and programming is challenging. Learning them while juggling piles of tiny parts, jumper wires, and breadboards is even more difficult. With the Arno, we\'ve gotten rid of the piles of parts. Everything you need to learn Arduino and complete interesting projects in already built in. The board comes fully assembled, ready to plug in with the included USB cable. Focus on learning how the circuits work and learning the Arduino language, then tackle the rest.');
        $arno->setUpload('{"protocol":"avr109","maximum_size":"28672","speed":"57600", "disable_flushing":"true"}');
        $arno->setBootloader('{"low_fuses":"0xff","high_fuses":"0xd8","extended_fuses":"0xcb","path":"caterina","file":"Caterina-Leonardo.hex","unlock_bits":"0x3F","lock_bits":"0x2F"}');
        $arno->setBuild('{"vid":"0x2341","pid":"0x8036","mcu":"atmega32u4","f_cpu":"16000000L","core":"arduino","variant":"leonardo"}');
        $manager->persist($arno);
        
        $arduinoDuAT = new Board();
        $arduinoDuAT->setName('Arduino Duemilanove w/ ATmega328');
        $arduinoDuAT->setDescription('Around March 1st, 2009, the Duemilanove started to ship with the ATmega328p instead of the ATmega168. The ATmega328 has 32 KB, (also with 2 KB used for the bootloader).');
        $arduinoDuAT->setUpload('{"protocol":"arduino","maximum_size":"30720","speed":"57600"}');
        $arduinoDuAT->setBootloader('{"low_fuses":"0xff","high_fuses":"0xda","extended_fuses":"0x05","path":"atmega","file":"ATmegaBOOT_168_atmega328.hex","unlock_bits":"0x3F","lock_bits":"0x0F"}');
        $arduinoDuAT->setBuild('{"mcu":"atmega328p","f_cpu":"16000000L","core":"arduino","variant":"standard"}');
        $manager->persist($arduinoDuAT);
        
        $arduinoDiec = new Board();
        $arduinoDiec->setName('Arduino Diecimila or Duemilanove w/ ATmega168');
        $arduinoDiec->setDescription('The Duemilanove automatically selects the appropriate power supply (USB or external power), eliminating the need for the power selection jumper found on previous boards. It also adds an easiest to cut trace for disabling the auto-reset, along with a solder jumper for re-enabling it.');
        $arduinoDiec->setUpload('{"protocol":"arduino","maximum_size":"14336","speed":"19200"}');
        $arduinoDiec->setBootloader('{"low_fuses":"0xff","high_fuses":"0xdd","extended_fuses":"0x00","path":"atmega","file":"ATmegaBOOT_168_diecimila.hex","unlock_bits":"0x3F","lock_bits":"0x0F"}');
        $arduinoDiec->setBuild('{"mcu":"atmega168","f_cpu":"16000000L","core":"arduino","variant":"standard"}');
        $manager->persist($arduinoDiec);
        
        $arduinoNano328 = new Board();
        $arduinoNano328->setName('Arduino Nano w/ ATmega328');
        $arduinoNano328->setDescription('The Arduino Nano is an all-in-one, compact design for use in breadboards. Version 3.0 has an ATmega328.');
        $arduinoNano328->setUpload('{"protocol":"arduino","maximum_size":"30720","speed":"57600"}');
        $arduinoNano328->setBootloader('{"low_fuses":"0xff","high_fuses":"0xda","extended_fuses":"0x05","path":"atmega","file":"ATmegaBOOT_168_atmega328.hex","unlock_bits":"0x3F","lock_bits":"0x0F"}');
        $arduinoNano328->setBuild('{"mcu":"atmega328p","f_cpu":"16000000L","core":"arduino","variant":"eightanaloginputs"}');
        $manager->persist($arduinoNano328);
        
        $arduinoNano168 = new Board();
        $arduinoNano168->setName('Arduino Nano w/ ATmega168');
        $arduinoNano168->setDescription('Older Arduino Nano with ATmega168 instead of the newer ATmega328.');
        $arduinoNano168->setUpload('{"protocol":"arduino","maximum_size":"14336","speed":"19200"}');
        $arduinoNano168->setBootloader('{"low_fuses":"0xff","high_fuses":"0xdd","extended_fuses":"0x00","path":"atmega","file":"ATmegaBOOT_168_diecimila.hex","unlock_bits":"0x3F","lock_bits":"0x0F"}');
        $arduinoNano168->setBuild('{"mcu":"atmega168","f_cpu":"16000000L","core":"arduino","variant":"eightanaloginputs"}');
        $manager->persist($arduinoNano168);

        $arduinoMega2560 = new Board();
        $arduinoMega2560->setName('Arduino Mega 2560 or Mega ADK');
        $arduinoMega2560->setDescription('The Mega 2560 is an update to the Arduino Mega, which it replaces. It features the Atmega2560, which has twice the memory, and uses the ATMega 8U2 for USB-to-serial communication.');
        $arduinoMega2560->setUpload('{"protocol":"wiring","maximum_size":"258048","speed":"115200"}');
        $arduinoMega2560->setBootloader('{"low_fuses":"0xff","high_fuses":"0xd8","extended_fuses":"0xfd","path":"stk500v2","file":"stk500boot_v2_mega2560.hex","unlock_bits":"0x3F","lock_bits":"0x0F"}');
        $arduinoMega2560->setBuild('{"mcu":"atmega2560","f_cpu":"16000000L","core":"arduino","variant":"mega"}');
        $manager->persist($arduinoMega2560);

        $arduinoMega1280 = new Board();
        $arduinoMega1280->setName('Arduino Mega (ATmega1280)');
        $arduinoMega1280->setDescription('A larger, more powerful Arduino board. Has extra digital pins, PWM pins, analog inputs, serial ports, etc. The original Arduino Mega has an ATmega1280 and an FTDI USB-to-serial chip.');
        $arduinoMega1280->setUpload('{"protocol":"arduino","maximum_size":"126976","speed":"57600"}');
        $arduinoMega1280->setBootloader('{"low_fuses":"0xff","high_fuses":"0xda","extended_fuses":"0xf5","path":"atmega","file":"ATmegaBOOT_168_atmega1280.hex","unlock_bits":"0x3F","lock_bits":"0x0F"}');
        $arduinoMega1280->setBuild('{"mcu":"atmega1280","f_cpu":"16000000L","core":"arduino","variant":"mega"}');
        $manager->persist($arduinoMega1280);

        $arduinoLeonardo = new Board();
        $arduinoLeonardo->setName('Arduino Leonardo');
        $arduinoLeonardo->setDescription('The Leonardo differs from all preceding boards in that the ATmega32u4 has built-in USB communication, eliminating the need for a secondary processor. This allows the Leonardo to appear to a connected computer as a mouse and keyboard, in addition to a virtual (CDC) serial / COM port.');
        $arduinoLeonardo->setUpload('{"protocol":"avr109","maximum_size":"28672","speed":"57600", "disable_flushing":"true"}');
        $arduinoLeonardo->setBootloader('{"low_fuses":"0xff","high_fuses":"0xd8","extended_fuses":"0xcb","path":"caterina","file":"Caterina-Leonardo.hex","unlock_bits":"0x3F","lock_bits":"0x2F"}');
        $arduinoLeonardo->setBuild('{"vid":"0x2341","pid":"0x8036","mcu":"atmega32u4","f_cpu":"16000000L","core":"arduino","variant":"leonardo"}');
        $manager->persist($arduinoLeonardo);

        $arduinoMini328 = new Board();
        $arduinoMini328->setName('Arduino Mini w/ ATmega328');
        $arduinoMini328->setDescription('The Mini is a compact Arduino board, intended for use on breadboards and when space is at a premium. This version has an ATmega328.');
        $arduinoMini328->setUpload('{"protocol":"stk500","maximum_size":"28672","speed":"115200"}');
        $arduinoMini328->setBootloader('{"low_fuses":"0xff","high_fuses":"0xd8","extended_fuses":"0x05","path":"optiboot","file":"optiboot_atmega328-Mini.hex","unlock_bits":"0x3F","lock_bits":"0x0F"}');
        $arduinoMini328->setBuild('{"mcu":"atmega328p","f_cpu":"16000000L","core":"arduino","variant":"eightanaloginputs"}');
        $manager->persist($arduinoMini328);

        $arduinoMini168 = new Board();
        $arduinoMini168->setName('Arduino Mini w/ ATmega168');
        $arduinoMini168->setDescription('Older Arduino Mini version with the ATmega168 microcontroller.');
        $arduinoMini168->setUpload('{"protocol":"arduino","maximum_size":"14336","speed":"19200"}');
        $arduinoMini168->setBootloader('{"low_fuses":"0xff","high_fuses":"0xdd","extended_fuses":"0x00","path":"atmega","file":"ATmegaBOOT_168_ng.hex","unlock_bits":"0x3F","lock_bits":"0x0F"}');
        $arduinoMini168->setBuild('{"mcu":"atmega168","f_cpu":"16000000L","core":"arduino","variant":"eightanaloginputs"}');
        $manager->persist($arduinoMini168);

        $arduinoEthernet = new Board();
        $arduinoEthernet->setName('Arduino Ethernet');
        $arduinoEthernet->setDescription('The Ethernet differs from other boards in that it does not have an onboard USB-to-serial driver chip, but has a Wiznet Ethernet interface. This is the same interface found on the Ethernet shield.');
        $arduinoEthernet->setUpload('{"protocol":"arduino","maximum_size":"32256","speed":"115200"}');
        $arduinoEthernet->setBootloader('{"low_fuses":"0xff","high_fuses":"0xde","extended_fuses":"0x05","path":"optiboot","file":"optiboot_atmega328.hex","unlock_bits":"0x3F","lock_bits":"0x0F"}');
        $arduinoEthernet->setBuild('{"mcu":"atmega328p","f_cpu":"16000000L","core":"arduino","variant":"standard"}');
        $manager->persist($arduinoEthernet);

        $arduinoFio = new Board();
        $arduinoFio->setName('Arduino Fio');
        $arduinoFio->setDescription('An Arduino intended for use as a wireless node. Has a header for an XBee radio, a connector for a LiPo battery, and a battery charging circuit.');
        $arduinoFio->setUpload('{"protocol":"arduino","maximum_size":"30720","speed":"57600"}');
        $arduinoFio->setBootloader('{"low_fuses":"0xff","high_fuses":"0xda","extended_fuses":"0x05","path":"atmega","file":"ATmegaBOOT_168_atmega328_pro_8MHz.hex","unlock_bits":"0x3F","lock_bits":"0x0F"}');
        $arduinoFio->setBuild('{"mcu":"atmega328p","f_cpu":"8000000L","core":"arduino","variant":"eightanaloginputs"}');
        $manager->persist($arduinoFio);

        $arduinoPro16Mhz328 = new Board();
        $arduinoPro16Mhz328->setName('Arduino Pro or Pro Mini (5V, 16 MHz) w/ ATmega328');
        $arduinoPro16Mhz328->setDescription('The Arduino Pro is intended for semi-permanent installation in objects or exhibitions. The board comes without pre-mounted headers, allowing the use of various types of connectors or direct soldering of wires. The pin layout is compatible with Arduino shields.  The Arduino Pro Mini pin layout is compatible with the Arduino Mini.');
        $arduinoPro16Mhz328->setUpload('{"protocol":"arduino","maximum_size":"30720","speed":"57600"}');
        $arduinoPro16Mhz328->setBootloader('{"low_fuses":"0xFF","high_fuses":"0xDA","extended_fuses":"0x05","path":"atmega","file":"ATmegaBOOT_168_atmega328.hex","unlock_bits":"0x3F","lock_bits":"0x0F"}');
        $arduinoPro16Mhz328->setBuild('{"mcu":"atmega328p","f_cpu":"16000000L","core":"arduino","variant":"standard"}');
        $manager->persist($arduinoPro16Mhz328);

        $arduinoPro16Mhz168 = new Board();
        $arduinoPro16Mhz168->setName('Arduino Pro or Pro Mini (5V, 16 MHz) w/ ATmega168');
        $arduinoPro16Mhz168->setDescription('The Arduino Pro is intended for semi-permanent installation in objects or exhibitions. The board comes without pre-mounted headers, allowing the use of various types of connectors or direct soldering of wires. The pin layout is compatible with Arduino shields.  The Arduino Pro Mini pin layout is compatible with the Arduino Mini.');
        $arduinoPro16Mhz168->setUpload('{"protocol":"arduino","maximum_size":"14336","speed":"19200"}');
        $arduinoPro16Mhz168->setBootloader('{"low_fuses":"0xff","high_fuses":"0xdd","extended_fuses":"0x00","path":"atmega","file":"ATmegaBOOT_168_diecimila.hex","unlock_bits":"0x3F","lock_bits":"0x0F"}');
        $arduinoPro16Mhz168->setBuild('{"mcu":"atmega168","f_cpu":"16000000L","core":"arduino","variant":"standard"}');
        $manager->persist($arduinoPro16Mhz168);

        $arduinoPro8Mhz328 = new Board();
        $arduinoPro8Mhz328->setName('Arduino Pro or Pro Mini (3.3V, 8 MHz) w/ ATmega328');
        $arduinoPro8Mhz328->setDescription('The Arduino Pro is intended for semi-permanent installation in objects or exhibitions. The board comes without pre-mounted headers, allowing the use of various types of connectors or direct soldering of wires. The pin layout is compatible with Arduino shields.  The Arduino Pro Mini pin layout is compatible with the Arduino Mini.');
        $arduinoPro8Mhz328->setUpload('{"protocol":"arduino","maximum_size":"30720","speed":"57600"}');
        $arduinoPro8Mhz328->setBootloader('{"low_fuses":"0xFF","high_fuses":"0xDA","extended_fuses":"0x05","path":"atmega","file":"ATmegaBOOT_168_atmega328_pro_8MHz.hex","unlock_bits":"0x3F","lock_bits":"0x0F"}');
        $arduinoPro8Mhz328->setBuild('{"mcu":"atmega328p","f_cpu":"8000000L","core":"arduino","variant":"standard"}');
        $manager->persist($arduinoPro8Mhz328);

        $arduinoPro8Mhz168 = new Board();
        $arduinoPro8Mhz168->setName('Arduino Pro or Pro Mini (3.3V, 8 MHz) w/ ATmega168');
        $arduinoPro8Mhz168->setDescription('The Arduino Pro is intended for semi-permanent installation in objects or exhibitions. The board comes without pre-mounted headers, allowing the use of various types of connectors or direct soldering of wires. The pin layout is compatible with Arduino shields.  The Arduino Pro Mini pin layout is compatible with the Arduino Mini.');
        $arduinoPro8Mhz168->setUpload('{"protocol":"arduino","maximum_size":"14336","speed":"19200"}');
        $arduinoPro8Mhz168->setBootloader('{"low_fuses":"0xc6","high_fuses":"0xdd","extended_fuses":"0x00","path":"atmega","file":"ATmegaBOOT_168_pro_8MHz.hex","unlock_bits":"0x3F","lock_bits":"0x0F"}');
        $arduinoPro8Mhz168->setBuild('{"mcu":"atmega168","f_cpu":"8000000L","core":"arduino","variant":"standard"}');
        $manager->persist($arduinoPro8Mhz168);

        $tinyDuino = new Board();
        $tinyDuino->setName('TinyDuino');
        $tinyDuino->setDescription('TinyDuino is a completely Arduino compatible platform smaller than the size of a quarter, yet with all the power and functionality of the Arduino Uno board - including Shield Support!');
        $tinyDuino->setUpload('{"protocol":"arduino","maximum_size":"30720","speed":"57600"}');
        $tinyDuino->setBootloader('{"low_fuses":"0xFF","high_fuses":"0xDA","extended_fuses":"0x05","path":"atmega","file":"ATmegaBOOT_168_atmega328_pro_8MHz.hex","unlock_bits":"0x3F","lock_bits":"0x0F"}');
        $tinyDuino->setBuild('{"mcu":"atmega328p","f_cpu":"8000000L","core":"arduino","variant":"standard"}');
        $manager->persist($tinyDuino);

        $arduinoMicro = new Board();
        $arduinoMicro->setName('Arduino Micro');
        $arduinoMicro->setDescription('The Arduino Micro is a microcontroller board based on the ATmega32u4 (datasheet), developed in conjunction with Adafruit. It contains everything needed to support the microcontroller; simply connect it to a computer with a micro USB cable to get started. It has a form factor that enables it to be easily placed on a breadboard.');
        $arduinoMicro->setUpload('{"protocol":"avr109","maximum_size":"28672","speed":"57600","disable_flushing":"true"}');
        $arduinoMicro->setBootloader('{"low_fuses":"0xff","high_fuses":"0xd8","extended_fuses":"0xcb","path":"caterina","file":"Caterina-Micro.hex","unlock_bits":"0x3F","lock_bits":"0x2F"}');
        $arduinoMicro->setBuild('{"mcu":"atmega32u4","f_cpu":"16000000L","vid":"0x2341","pid":"0x8037","core":"arduino","variant":"micro"}');
        $manager->persist($arduinoMicro);

        $arduinoEsplora = new Board();
        $arduinoEsplora->setName('Arduino Esplora (untested)');
        $arduinoEsplora->setDescription('Arduino Esplora is a microcontroller board derived from the Arduino Leonardo. The Esplora differs from all preceding Arduino boards in that it provides a number of built-in, ready-to-use setof onboard sensors for interaction. It has onboard sound and light outputs, and several input sensors, including a joystick, a slider, a temperature sensor, an accelerometer, a microphone, and a light sensor.');
        $arduinoEsplora->setUpload('{"protocol":"avr109","maximum_size":"28672","speed":"57600","disable_flushing":"true"}');
        $arduinoEsplora->setBootloader('{"low_fuses":"0xff","high_fuses":"0xd8","extended_fuses":"0xcb","path":"caterina","file":"Caterina-Esplora.hex","unlock_bits":"0x3F","lock_bits":"0x2F"}');
        $arduinoEsplora->setBuild('{"mcu":"atmega32u4","f_cpu":"16000000L","vid":"0x2341","pid":"0x803C","core":"arduino","variant":"leonardo"}');
        $manager->persist($arduinoEsplora);

        $lilyPadUSB = new Board();
        $lilyPadUSB->setName('LilyPad Arduino USB (untested)');
        $lilyPadUSB->setDescription('The LilyPad USB is replacing the classic ATMega328 with the new ATMega32U4. LilyPad is a wearable e-textile technology developed by Leah Buechley and cooperatively designed by Leah and SparkFun. Each LilyPad was creatively designed to have large connecting pads to allow them to be sewn into clothing. Various input, output, power, and sensor boards are available. They\'re even washable!');
        $lilyPadUSB->setUpload('{"protocol":"avr109","maximum_size":"28672","speed":"57600","disable_flushing":"true"}');
        $lilyPadUSB->setBootloader('{"low_fuses":"0xff","high_fuses":"0xd8","extended_fuses":"0xce","path":"caterina-LilyPadUSB","file":"Caterina-LilyPadUSB.hex","unlock_bits":"0x3F","lock_bits":"0x2F"}');
        $lilyPadUSB->setBuild('{"mcu":"atmega32u4","f_cpu":"8000000L","vid":"0x1B4F","pid":"0x9208","core":"arduino","variant":"leonardo"}');
        $manager->persist($lilyPadUSB);

        $lilyPad328 = new Board();
        $lilyPad328->setName('LilyPad Arduino w/ ATmega328 (untested)');
        $lilyPad328->setDescription('LilyPad is a wearable e-textile technology developed by Leah Buechley and cooperatively designed by Leah and SparkFun. Each LilyPad was creatively designed to have large connecting pads to allow them to be sewn into clothing. Various input, output, power, and sensor boards are available. They\'re even washable!');
        $lilyPad328->setUpload('{"protocol":"arduino","maximum_size":"30720","speed":"57600"}');
        $lilyPad328->setBootloader('{"low_fuses":"0xFF","high_fuses":"0xDA","extended_fuses":"0x05","path":"atmega","file":"ATmegaBOOT_168_atmega328_pro_8MHz.hex","unlock_bits":"0x3F","lock_bits":"0x0F"}');
        $lilyPad328->setBuild('{"mcu":"atmega328p","f_cpu":"8000000L","core":"arduino","variant":"standard"}');
        $manager->persist($lilyPad328);

        $lilyPad168 = new Board();
        $lilyPad168->setName('LilyPad Arduino w/ ATmega168 (untested)');
        $lilyPad168->setDescription('LilyPad is a wearable e-textile technology developed by Leah Buechley and cooperatively designed by Leah and SparkFun. Each LilyPad was creatively designed to have large connecting pads to allow them to be sewn into clothing. Various input, output, power, and sensor boards are available. They\'re even washable!');
        $lilyPad168->setUpload('{"protocol":"arduino","maximum_size":"14336","speed":"19200"}');
        $lilyPad168->setBootloader('{"low_fuses":"0xe2","high_fuses":"0xdd","extended_fuses":"0x00","path":"lilypad","file":"LilyPadBOOT_168.hex","unlock_bits":"0x3F","lock_bits":"0x0F"}');
        $lilyPad168->setBuild('{"mcu":"atmega168","f_cpu":"8000000L","core":"arduino","variant":"standard"}');
        $manager->persist($lilyPad168);

        $arduinoBT328 = new Board();
        $arduinoBT328->setName('Arduino BT w/ ATmega328 (untested)');
        $arduinoBT328->setDescription('The Arduino BT is a microcontroller board originally was based on the ATmega168, but now is supplied with the 328. It supports wireless serial communication over bluetooth (but is not compatible with Bluetooth headsets or other audio devices). It contains everything needed to support the microcontroller and can be programmed wirelessly over the Bluetooth connection.');
        $arduinoBT328->setUpload('{"protocol":"arduino","maximum_size":"28672","speed":"19200","disable_flushing":"true"}');
        $arduinoBT328->setBootloader('{"low_fuses":"0xff","high_fuses":"0xd8","extended_fuses":"0x05","path":"bt","file":"ATmegaBOOT_168_atmega328_bt.hex","unlock_bits":"0x3F","lock_bits":"0x0F"}');
        $arduinoBT328->setBuild('{"mcu":"atmega328p","f_cpu":"16000000L","core":"arduino","variant":"eightanaloginputs"}');
        $manager->persist($arduinoBT328);

        $arduinoBT168 = new Board();
        $arduinoBT168->setName('Arduino BT w/ ATmega168 (untested)');
        $arduinoBT168->setDescription('The Arduino BT is a microcontroller board originally was based on the ATmega168, but now is supplied with the 328. It supports wireless serial communication over bluetooth (but is not compatible with Bluetooth headsets or other audio devices). It contains everything needed to support the microcontroller and can be programmed wirelessly over the Bluetooth connection.');
        $arduinoBT168->setUpload('{"protocol":"arduino","maximum_size":"14336","speed":"19200","disable_flushing":"true"}');
        $arduinoBT168->setBootloader('{"low_fuses":"0xff","high_fuses":"0xdd","extended_fuses":"0x00","path":"bt","file":"ATmegaBOOT_168.hex","unlock_bits":"0x3F","lock_bits":"0x0F"}');
        $arduinoBT168->setBuild('{"mcu":"atmega168","f_cpu":"16000000L","core":"arduino","variant":"eightanaloginputs"}');
        $manager->persist($arduinoBT168);

        $arduinoNG = new Board();
        $arduinoNG->setName('Arduino NG or older w/ ATmega8');
        $arduinoNG->setDescription('The Arduino NG uses the FTDI FT232RL USB-to-Serial converter, which requires fewer external components that the FT232BM. It also has a built-in LED on pin 13 (which may interfere with SPI communication). Later NG\'s shipped with an ATmega168 instead of an ATmega8, though either chip can be used with any board. ');
        $arduinoNG->setUpload('{"protocol":"arduino","maximum_size":"7168","speed":"19200"}');
        $arduinoNG->setBootloader('{"low_fuses":"0xdf","high_fuses":"0xca","path":"atmega8","file":"ATmegaBOOT-prod-firmware-2009-11-07.hex","unlock_bits":"0x3F","lock_bits":"0x0F"}');
        $arduinoNG->setBuild('{"mcu":"atmega8","f_cpu":"16000000L","core":"arduino","variant":"standard"}');
        $manager->persist($arduinoNG);

        $rambo = new Board();
        $rambo->setName('RAMBO');
        $rambo->setDescription('The Mega 2560 is an update to the Arduino Mega, which it replaces. It features the Atmega2560, which has twice the memory, and uses the ATMega 8U2 for USB-to-serial communication.');
        $rambo->setUpload('{"protocol":"wiring","maximum_size":"258048","speed":"115200"}');
        $rambo->setBootloader('{"low_fuses":"0xff","high_fuses":"0xd8","extended_fuses":"0xfd","path":"stk500v2","file":"stk500boot_v2_mega2560.hex","unlock_bits":"0x3F","lock_bits":"0x0F"}');
        $rambo->setBuild('{"mcu":"atmega2560","f_cpu":"16000000L","core":"arduino","variant":"mega"}');
        $manager->persist($rambo);

        $arduinoRobotControl = new Board();
        $arduinoRobotControl->setName('Arduino Robot Control');
        $arduinoRobotControl->setDescription('The Arduino Robot is the first official Arduino on wheels. The robot has two processors, one on each of its two boards. The Control Board reads sensors and decides how to operate.');
        $arduinoRobotControl->setUpload('{"protocol":"avr109","maximum_size":"28672","speed":"57600","disable_flushing":"true"}');
        $arduinoRobotControl->setBootloader('{"low_fuses":"0xff","high_fuses":"0xd8","extended_fuses":"0xcb","path":"caterina-Arduino_Robot","file":"Caterina-Robot-Control.hex","unlock_bits":"0x3F","lock_bits":"0x2F"}');
        $arduinoRobotControl->setBuild('{"mcu":"atmega32u4","f_cpu":"16000000L","vid":"0x2341","pid":"0x8038","core":"robot","variant":"robot_control"}');
        $manager->persist($arduinoRobotControl);

        $arduinoRobotMotor = new Board();
        $arduinoRobotMotor->setName('Arduino Robot Motor');
        $arduinoRobotMotor->setDescription('The Arduino Robot is the first official Arduino on wheels. The robot has two processors, one on each of its two boards. The Motor Board controls the motors. ');
        $arduinoRobotMotor->setUpload('{"protocol":"avr109","maximum_size":"28672","speed":"57600","disable_flushing":"true"}');
        $arduinoRobotMotor->setBootloader('{"low_fuses":"0xff","high_fuses":"0xd8","extended_fuses":"0xcb","path":"caterina-Arduino_Robot","file":"Caterina-Robot-Motor.hex","unlock_bits":"0x3F","lock_bits":"0x2F"}');
        $arduinoRobotMotor->setBuild('{"mcu":"atmega32u4","f_cpu":"16000000L","vid":"0x2341","pid":"0x8039","core":"robot","variant":"robot_motor"}');
        $manager->persist($arduinoRobotMotor);

        $redBoard = new Board();
        $redBoard->setName('Sparkfun RedBoard');
        $redBoard->setDescription('The RedBoard combines the simplicity of the UNO\'s Optiboot bootloader (which is used in the Pro series), the stability of the FTDI (which we all missed after the Duemilanove was discontinued) and the R3 shield compatibility of the latest Arduino UNO R3. The RedBoard can be programmed over a USB Mini-B cable using the Arduino IDE: Just plug in the board, select "Arduino UNO" from the board menu and you\'re ready to upload code. RedBoard has all of the hardware peripherals you know and love.');
        $redBoard->setUpload('{"protocol":"arduino","maximum_size":"32256","speed":"115200"}');
        $redBoard->setBootloader('{"low_fuses":"0xff","high_fuses":"0xde","extended_fuses":"0x05","path":"optiboot","file":"optiboot_atmega328.hex","unlock_bits":"0x3F","lock_bits":"0x0F"}');
        $redBoard->setBuild('{"mcu":"atmega328p","f_cpu":"16000000L","core":"arduino","variant":"standard"}');
        $manager->persist($redBoard);
        
        $tinyLily = new Board();
        $tinyLily->setName('TinyLily Mini');
        $tinyLily->setDescription('For all your tiny e-textile (or just general electronics) needs, the TinyCircuits TinyLily family is an extremely compact and low cost way to add intelligence to your projects. Inspired by the popular Lilypad Arduino, the TinyLily family allows your projects to shrink considerably, allowing you greater creativity in what you can do – yet still has all the power of a standard LilyPad Arduino at 1/12th the size!');
        $tinyLily->setUpload('{"protocol":"arduino","maximum_size":"30720","speed":"57600"}');
        $tinyLily->setBootloader('{"low_fuses":"0xFF","high_fuses":"0xDA","extended_fuses":"0x05","path":"atmega","file":"ATmegaBOOT_168_atmega328_pro_8MHz.hex","unlock_bits":"0x3F","lock_bits":"0x0F"}');
        $tinyLily->setBuild('{"mcu":"atmega328p","f_cpu":"8000000L","core":"arduino","variant":"standard"}');
        $manager->persist($tinyLily);


        $personal = new Board();
        $personal->setName('Arduino Custom');
        $personal->setOwner($this->getReference('admin-user'));
        $personal->setDescription('Tester\'s custom board');
        $personal->setUpload('{"protocol":"arduino","maximum_size":"32256","speed":"115200"}');
        $personal->setBootloader('{"low_fuses":"0xff","high_fuses":"0xde","extended_fuses":"0x05","path":"optiboot","file":"optiboot_atmega328.hex","unlock_bits":"0x3F","lock_bits":"0x0F"}');
        $personal->setBuild('{"mcu":"atmega328p","f_cpu":"16000000L","core":"arduino","variant":"standard"}');
        $manager->persist($personal);

        $personalToDelete = new Board();
        $personalToDelete->setName('Arduino Custom To Delete');
        $personalToDelete->setOwner($this->getReference('admin-user'));
        $personalToDelete->setDescription('');
        $personalToDelete->setUpload('{"protocol":"arduino","maximum_size":"32256","speed":"115200"}');
        $personalToDelete->setBootloader('{"low_fuses":"0xff","high_fuses":"0xde","extended_fuses":"0x05","path":"optiboot","file":"optiboot_atmega328.hex","unlock_bits":"0x3F","lock_bits":"0x0F"}');
        $personalToDelete->setBuild('{"mcu":"atmega328p","f_cpu":"16000000L","core":"arduino","variant":"standard"}');
        $manager->persist($personal);

        // Commit all Boards to Database
        $manager->flush();
    }
}
