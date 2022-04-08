<?php
/**
 * Volcanus libraries for PHP
 *
 * @copyright k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */

namespace Volcanus\QueryBuilder\Test;

use PHPUnit\Framework\TestCase;

/**
 * Test for AbstractParameter
 *
 * @author k.holy74@gmail.com
 */
class AbstractParameterBuilderTest extends TestCase
{

    public function testToText()
    {
        $builder = new ParameterBuilder();
        $this->assertEquals("'Foo'", $builder->toText('Foo'));
    }

    public function testToInt()
    {
        $builder = new ParameterBuilder();
        $this->assertEquals('1', $builder->toInt(1));
    }

    public function testToFloat()
    {
        $builder = new ParameterBuilder();
        $this->assertEquals('1.5', $builder->toFloat(1.5));
    }

    public function testToBool()
    {
        $builder = new ParameterBuilder();
        $this->assertEquals('1', $builder->toBool(1));
        $this->assertEquals('0', $builder->toBool(0));
        $this->assertEquals('1', $builder->toBool(true));
        $this->assertEquals('0', $builder->toBool(false));
    }

    public function testToDate()
    {
        $builder = new ParameterBuilder();
        $this->assertEquals("TO_DATE('2013-01-02')", $builder->ToDate('2013-01-02'));
    }

    public function testToTimestamp()
    {
        $builder = new ParameterBuilder();
        $this->assertEquals("TO_TIMESTAMP('2013-01-02 03:04:05')", $builder->toTimestamp('2013-01-02 03:04:05'));
    }

}
