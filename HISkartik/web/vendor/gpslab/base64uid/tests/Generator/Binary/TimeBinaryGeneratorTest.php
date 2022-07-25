<?php

/**
 * GpsLab component.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/MIT
 */

namespace GpsLab\Component\Base64UID\Tests\Generator\Binary;

use GpsLab\Component\Base64UID\Generator\Binary\TimeBinaryGenerator;
use PHPUnit\Framework\TestCase;

class TimeBinaryGeneratorTest extends TestCase
{
    /**
     * @expectedException \GpsLab\Component\Base64UID\Exception\ProcessorArchitectureException
     */
    public function testProcessorArchitectureException()
    {
        if (PHP_INT_SIZE * 8 >= 64) {
            $this->markTestSkipped('This test is not reproducible on this processor architecture.');
        }

        $generator = new TimeBinaryGenerator();
    }

    /**
     * @expectedException \GpsLab\Component\Base64UID\Exception\ArgumentTypeException
     */
    public function testPrefixLengthNoInteger()
    {
        $generator = new TimeBinaryGenerator('9', 45);
    }

    /**
     * @expectedException \GpsLab\Component\Base64UID\Exception\ArgumentTypeException
     */
    public function testTimeLengthNoInteger()
    {
        $generator = new TimeBinaryGenerator(9, '45');
    }

    /**
     * @expectedException \GpsLab\Component\Base64UID\Exception\ArgumentTypeException
     */
    public function testTimeOffsetNoInteger()
    {
        $generator = new TimeBinaryGenerator(9, 45, '123');
    }

    /**
     * @expectedException \GpsLab\Component\Base64UID\Exception\ZeroArgumentException
     */
    public function testNegativePrefixLength()
    {
        $generator = new TimeBinaryGenerator(-1, 45);
    }

    /**
     * @expectedException \GpsLab\Component\Base64UID\Exception\ZeroArgumentException
     */
    public function testNegativeTimeLength()
    {
        $generator = new TimeBinaryGenerator(9, -1);
    }

    /**
     * @expectedException \GpsLab\Component\Base64UID\Exception\ZeroArgumentException
     */
    public function testNegativeTimeOffset()
    {
        $generator = new TimeBinaryGenerator(9, 45, -1);
    }

    /**
     * @expectedException \GpsLab\Component\Base64UID\Exception\ArgumentRangeException
     */
    public function testLowTimeLength()
    {
        $generator = new TimeBinaryGenerator(9, 0);
    }

    /**
     * @expectedException \GpsLab\Component\Base64UID\Exception\ArgumentRangeException
     */
    public function testBigPrefixLength()
    {
        $generator = new TimeBinaryGenerator(19, 45);
    }

    /**
     * @expectedException \GpsLab\Component\Base64UID\Exception\ArgumentRangeException
     */
    public function testLowTimeLengthForCurrentTime()
    {
        $min_time_length = strlen(decbin((int) floor(microtime(true) * 1000)));
        $generator = new TimeBinaryGenerator(0, $min_time_length - 1);
    }

    /**
     * @expectedException \GpsLab\Component\Base64UID\Exception\ArgumentRangeException
     */
    public function testGrateTimeOffsetForCurrentTime()
    {
        $now = (int) floor(microtime(true) * 1000);
        $generator = new TimeBinaryGenerator(0, 45, $now + 100);
    }

    /**
     * @expectedException \GpsLab\Component\Base64UID\Exception\ArgumentRangeException
     */
    public function testLowTimeLengthForCurrentTimeWithTimeOffset()
    {
        $now = (int) floor(microtime(true) * 1000);
        $offset = strtotime('2000-01-01 00:00:00') * 1000;
        $min_time_length = strlen(decbin($offset).decbin($now));

        $generator = new TimeBinaryGenerator(0, $min_time_length, $offset);
    }

    /**
     * @return array
     */
    public function getTimeAndPrefixLengths()
    {
        return array(
            array(0, 45),
            array(9, 45),
            array(18, 45),
            array(20, 43),
            array(16, 47),
        );
    }

    /**
     * @dataProvider getTimeAndPrefixLengths
     *
     * @param int $prefix_length
     * @param int $time_length
     */
    public function testGenerate($prefix_length, $time_length)
    {
        $generator = new TimeBinaryGenerator($prefix_length, $time_length);
        $id = $generator->generate();
        $this->assertInternalType('integer', $id);
    }

    public function testNotSame()
    {
        $generator = new TimeBinaryGenerator();
        self::assertNotSame($generator->generate(), $generator->generate());
    }
}
