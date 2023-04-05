<?php
/**
 * Volcanus libraries for PHP 8.1~
 *
 * @copyright k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */

namespace Volcanus\QueryBuilder\Test\Adapter\Sqlite;

use PHPUnit\Framework\TestCase;
use Volcanus\QueryBuilder\QueryBuilder;
use Volcanus\QueryBuilder\Adapter\Sqlite\SqliteParameterBuilder;

use Volcanus\Database\Driver\Pdo\PdoDriver;
use Volcanus\Database\MetaData\SqliteMetaDataProcessor;

/**
 * Test for SqliteParameter
 *
 * @author k.holy74@gmail.com
 */
class SqliteParameterBuilderTest extends TestCase
{

    private static \PDO $pdo;

    private function getPdo(): \PDO
    {
        if (!isset(static::$pdo)) {
            static::$pdo = new \PDO('sqlite::memory:');
        }
        return static::$pdo;
    }

    public function testToText()
    {
        $builder = new SqliteParameterBuilder(
            new PdoDriver($this->getPdo(), new SqliteMetaDataProcessor())
        );
        $this->assertEquals("'Foo'", $builder->toText('Foo'));
        $this->assertEquals("'''Foo'''", $builder->toText("'Foo'"));
    }

    public function testToTextNull()
    {
        $builder = new SqliteParameterBuilder(
            new PdoDriver($this->getPdo(), new SqliteMetaDataProcessor())
        );
        $this->assertEquals('NULL', $builder->toText(null));
    }

    public function testToTextEmptyString()
    {
        $builder = new SqliteParameterBuilder(
            new PdoDriver($this->getPdo(), new SqliteMetaDataProcessor())
        );
        $this->assertEquals('NULL', $builder->toText(''));
    }

    public function testToInt()
    {
        $builder = new SqliteParameterBuilder(
            new PdoDriver($this->getPdo(), new SqliteMetaDataProcessor())
        );
        $this->assertEquals('1', $builder->toInt(1));
    }

    public function testToIntMin()
    {
        $builder = new SqliteParameterBuilder(
            new PdoDriver($this->getPdo(), new SqliteMetaDataProcessor())
        );
        $this->assertEquals('-2147483648', $builder->toInt(QueryBuilder::MIN));
    }

    public function testToIntMax()
    {
        $builder = new SqliteParameterBuilder(
            new PdoDriver($this->getPdo(), new SqliteMetaDataProcessor())
        );
        $this->assertEquals('2147483647', $builder->toInt(QueryBuilder::MAX));
    }

    public function testToIntNull()
    {
        $builder = new SqliteParameterBuilder(
            new PdoDriver($this->getPdo(), new SqliteMetaDataProcessor())
        );
        $this->assertEquals('NULL', $builder->toInt(null));
    }

    public function testToIntEmptyString()
    {
        $builder = new SqliteParameterBuilder(
            new PdoDriver($this->getPdo(), new SqliteMetaDataProcessor())
        );
        $this->assertEquals('NULL', $builder->toInt(''));
    }

    public function testToIntWithNullType()
    {
        $builder = new SqliteParameterBuilder(
            new PdoDriver($this->getPdo(), new SqliteMetaDataProcessor())
        );
        $this->assertEquals('1', $builder->toInt(1, null));
    }

    public function testToSmallInt()
    {
        $builder = new SqliteParameterBuilder(
            new PdoDriver($this->getPdo(), new SqliteMetaDataProcessor())
        );
        $this->assertEquals('1', $builder->toInt(1, 'smallint'));
        $this->assertEquals('1', $builder->toInt(1, 'int2'));
    }

    public function testToSmallIntMin()
    {
        $builder = new SqliteParameterBuilder(
            new PdoDriver($this->getPdo(), new SqliteMetaDataProcessor())
        );
        $this->assertEquals('-32768', $builder->toInt(QueryBuilder::MIN, 'smallint'));
        $this->assertEquals('-32768', $builder->toInt(QueryBuilder::MIN, 'int2'));
    }

    public function testToSmallIntMax()
    {
        $builder = new SqliteParameterBuilder(
            new PdoDriver($this->getPdo(), new SqliteMetaDataProcessor())
        );
        $this->assertEquals('32767', $builder->toInt(QueryBuilder::MAX, 'smallint'));
        $this->assertEquals('32767', $builder->toInt(QueryBuilder::MAX, 'int2'));
    }

    public function testToSmallIntNull()
    {
        $builder = new SqliteParameterBuilder(
            new PdoDriver($this->getPdo(), new SqliteMetaDataProcessor())
        );
        $this->assertEquals('NULL', $builder->toInt(null, 'smallint'));
        $this->assertEquals('NULL', $builder->toInt(null, 'int2'));
    }

    public function testToSmallIntEmptyString()
    {
        $builder = new SqliteParameterBuilder(
            new PdoDriver($this->getPdo(), new SqliteMetaDataProcessor())
        );
        $this->assertEquals('NULL', $builder->toInt('', 'smallint'));
        $this->assertEquals('NULL', $builder->toInt('', 'int2'));
    }

    public function testToBigInt()
    {
        $builder = new SqliteParameterBuilder(
            new PdoDriver($this->getPdo(), new SqliteMetaDataProcessor())
        );
        $this->assertEquals('1', $builder->toInt(1, 'bigint'));
        $this->assertEquals('1', $builder->toInt(1, 'int8'));
    }

    public function testToBigIntMin()
    {
        $builder = new SqliteParameterBuilder(
            new PdoDriver($this->getPdo(), new SqliteMetaDataProcessor())
        );
        $this->assertEquals('-9223372036854775808', $builder->toInt(QueryBuilder::MIN, 'bigint'));
        $this->assertEquals('-9223372036854775808', $builder->toInt(QueryBuilder::MIN, 'int8'));
    }

    public function testToBigIntMax()
    {
        $builder = new SqliteParameterBuilder(
            new PdoDriver($this->getPdo(), new SqliteMetaDataProcessor())
        );
        $this->assertEquals('9223372036854775807', $builder->toInt(QueryBuilder::MAX, 'bigint'));
        $this->assertEquals('9223372036854775807', $builder->toInt(QueryBuilder::MAX, 'int8'));
    }

    public function testToBigIntNull()
    {
        $builder = new SqliteParameterBuilder(
            new PdoDriver($this->getPdo(), new SqliteMetaDataProcessor())
        );
        $this->assertEquals('NULL', $builder->toInt(null, 'bigint'));
        $this->assertEquals('NULL', $builder->toInt(null, 'int8'));
    }

    public function testToBigIntEmptyString()
    {
        $builder = new SqliteParameterBuilder(
            new PdoDriver($this->getPdo(), new SqliteMetaDataProcessor())
        );
        $this->assertEquals('NULL', $builder->toInt('', 'bigint'));
        $this->assertEquals('NULL', $builder->toInt('', 'int8'));
    }

    public function testToFloat()
    {
        $builder = new SqliteParameterBuilder(
            new PdoDriver($this->getPdo(), new SqliteMetaDataProcessor())
        );
        $this->assertEquals('1', $builder->toFloat(1));
    }

    public function testToFloatMin()
    {
        $builder = new SqliteParameterBuilder(
            new PdoDriver($this->getPdo(), new SqliteMetaDataProcessor())
        );
        $this->assertEquals('-9223372036854775808', $builder->toFloat(QueryBuilder::MIN));
    }

    public function testToFloatMax()
    {
        $builder = new SqliteParameterBuilder(
            new PdoDriver($this->getPdo(), new SqliteMetaDataProcessor())
        );
        $this->assertEquals('9223372036854775807', $builder->toFloat(QueryBuilder::MAX));
    }

    public function testToFloatNull()
    {
        $builder = new SqliteParameterBuilder(
            new PdoDriver($this->getPdo(), new SqliteMetaDataProcessor())
        );
        $this->assertEquals('NULL', $builder->toFloat(null));
    }

    public function testToFloatEmptyString()
    {
        $builder = new SqliteParameterBuilder(
            new PdoDriver($this->getPdo(), new SqliteMetaDataProcessor())
        );
        $this->assertEquals('NULL', $builder->toFloat(''));
    }

    public function testToFloatWithNullType()
    {
        $builder = new SqliteParameterBuilder(
            new PdoDriver($this->getPdo(), new SqliteMetaDataProcessor())
        );
        $this->assertEquals('1', $builder->toFloat(1, null));
    }

    public function testToDateString()
    {
        $builder = new SqliteParameterBuilder(
            new PdoDriver($this->getPdo(), new SqliteMetaDataProcessor())
        );
        $this->assertEquals("date('2013-01-02')", $builder->ToDate('2013-01-02'));
    }

    public function testToDateArrayOfString()
    {
        $builder = new SqliteParameterBuilder(
            new PdoDriver($this->getPdo(), new SqliteMetaDataProcessor())
        );
        $this->assertEquals("date('2013-01-02')", $builder->ToDate(['2013', '01', '02']));
        $this->assertEquals("date('2013-01-02')", $builder->ToDate(['2013', '1', '2']));
    }

    public function testToDateForArrayOfInt()
    {
        $builder = new SqliteParameterBuilder(
            new PdoDriver($this->getPdo(), new SqliteMetaDataProcessor())
        );
        $this->assertEquals("date('2013-01-02')", $builder->ToDate([2013, 1, 2]));
    }

    public function testToDateEmptyArray()
    {
        $builder = new SqliteParameterBuilder(
            new PdoDriver($this->getPdo(), new SqliteMetaDataProcessor())
        );
        $this->assertEquals('NULL', $builder->ToDate([]));
    }

    public function testToDateForDateTime()
    {
        $builder = new SqliteParameterBuilder(
            new PdoDriver($this->getPdo(), new SqliteMetaDataProcessor())
        );
        $this->assertEquals("date('2013-01-02')", $builder->ToDate(new \DateTime('2013-01-02')));
    }

    public function testToDateForDateTimeImmutable()
    {
        $builder = new SqliteParameterBuilder(
            new PdoDriver($this->getPdo(), new SqliteMetaDataProcessor())
        );
        $this->assertEquals("date('2013-01-02')", $builder->ToDate(new \DateTimeImmutable('2013-01-02')));
    }

    public function testToDateForUnixTimestamp()
    {
        $builder = new SqliteParameterBuilder(
            new PdoDriver($this->getPdo(), new SqliteMetaDataProcessor())
        );
        $this->assertEquals("date('2013-01-02')", $builder->ToDate(mktime(0, 0, 0, 1, 2, 2013)));
    }

    public function testToDateMin()
    {
        $builder = new SqliteParameterBuilder(
            new PdoDriver($this->getPdo(), new SqliteMetaDataProcessor())
        );
        $this->assertEquals("date('0000-01-01')", $builder->ToDate(QueryBuilder::MIN));
    }

    public function testToDateMax()
    {
        $builder = new SqliteParameterBuilder(
            new PdoDriver($this->getPdo(), new SqliteMetaDataProcessor())
        );
        $this->assertEquals("date('9999-12-31')", $builder->ToDate(QueryBuilder::MAX));
    }

    public function testToDateNow()
    {
        $builder = new SqliteParameterBuilder(
            new PdoDriver($this->getPdo(), new SqliteMetaDataProcessor())
        );
        $this->assertEquals("date('now')", $builder->ToDate(QueryBuilder::NOW));
    }

    public function testToDateNull()
    {
        $builder = new SqliteParameterBuilder(
            new PdoDriver($this->getPdo(), new SqliteMetaDataProcessor())
        );
        $this->assertEquals('NULL', $builder->ToDate(null));
    }

    public function testToDateEmptyString()
    {
        $builder = new SqliteParameterBuilder(
            new PdoDriver($this->getPdo(), new SqliteMetaDataProcessor())
        );
        $this->assertEquals('NULL', $builder->ToDate(''));
    }

    public function testToDateRaiseExceptionWhenInvalidValue()
    {
        $this->expectException(\InvalidArgumentException::class);
        $builder = new SqliteParameterBuilder(
            new PdoDriver($this->getPdo(), new SqliteMetaDataProcessor())
        );
        $builder->ToDate(new \stdClass());
    }

    public function testToTimestampString()
    {
        $builder = new SqliteParameterBuilder(
            new PdoDriver($this->getPdo(), new SqliteMetaDataProcessor())
        );
        $this->assertEquals("datetime('2013-01-02 03:04:05')", $builder->toTimestamp('2013-01-02 03:04:05'));
    }

    public function testToTimestampArrayOfString()
    {
        $builder = new SqliteParameterBuilder(
            new PdoDriver($this->getPdo(), new SqliteMetaDataProcessor())
        );
        $this->assertEquals("datetime('2013-01-02 03:04:05')", $builder->toTimestamp([
            '2013',
            '01',
            '02',
            '03',
            '04',
            '05',
        ]));
        $this->assertEquals("datetime('2013-01-02 03:04:05')", $builder->toTimestamp([
            '2013',
            '1',
            '2',
            '3',
            '4',
            '5',
        ]));
    }

    public function testToTimestampArrayOfInt()
    {
        $builder = new SqliteParameterBuilder(
            new PdoDriver($this->getPdo(), new SqliteMetaDataProcessor())
        );
        $this->assertEquals("datetime('2013-01-02 03:04:05')", $builder->toTimestamp([2013, 1, 2, 3, 4, 5]));
    }

    public function testToTimestampEmptyArray()
    {
        $builder = new SqliteParameterBuilder(
            new PdoDriver($this->getPdo(), new SqliteMetaDataProcessor())
        );
        $this->assertEquals('NULL', $builder->toTimestamp([]));
    }

    public function testToTimestampForDateTime()
    {
        $builder = new SqliteParameterBuilder(
            new PdoDriver($this->getPdo(), new SqliteMetaDataProcessor())
        );
        $this->assertEquals("datetime('2013-01-02 03:04:05')", $builder->toTimestamp(new \DateTime('2013-01-02 03:04:05')));
    }

    public function testToTimestampForDateTimeImmutable()
    {
        $builder = new SqliteParameterBuilder(
            new PdoDriver($this->getPdo(), new SqliteMetaDataProcessor())
        );
        $this->assertEquals("datetime('2013-01-02 03:04:05')", $builder->toTimestamp(new \DateTimeImmutable('2013-01-02 03:04:05')));
    }

    public function testToTimestampForUnixTimestamp()
    {
        $builder = new SqliteParameterBuilder(
            new PdoDriver($this->getPdo(), new SqliteMetaDataProcessor())
        );
        $this->assertEquals("datetime('2013-01-02 03:04:05')", $builder->toTimestamp(mktime(3, 4, 5, 1, 2, 2013)));
    }

    public function testToTimestampMin()
    {
        $builder = new SqliteParameterBuilder(
            new PdoDriver($this->getPdo(), new SqliteMetaDataProcessor())
        );
        $this->assertEquals("datetime('0000-01-01 00:00:00')", $builder->toTimestamp(QueryBuilder::MIN));
    }

    public function testToTimestampMax()
    {
        $builder = new SqliteParameterBuilder(
            new PdoDriver($this->getPdo(), new SqliteMetaDataProcessor())
        );
        $this->assertEquals("datetime('9999-12-31 23:59:59')", $builder->toTimestamp(QueryBuilder::MAX));
    }

    public function testToTimestampNow()
    {
        $builder = new SqliteParameterBuilder(
            new PdoDriver($this->getPdo(), new SqliteMetaDataProcessor())
        );
        $this->assertEquals("datetime('now')", $builder->toTimestamp(QueryBuilder::NOW));
    }

    public function testToTimestampNull()
    {
        $builder = new SqliteParameterBuilder(
            new PdoDriver($this->getPdo(), new SqliteMetaDataProcessor())
        );
        $this->assertEquals('NULL', $builder->toTimestamp(null));
    }

    public function testToTimestampEmptyString()
    {
        $builder = new SqliteParameterBuilder(
            new PdoDriver($this->getPdo(), new SqliteMetaDataProcessor())
        );
        $this->assertEquals('NULL', $builder->toTimestamp(''));
    }

    public function testToTimestampRaiseExceptionWhenInvalidValue()
    {
        $this->expectException(\InvalidArgumentException::class);
        $builder = new SqliteParameterBuilder(
            new PdoDriver($this->getPdo(), new SqliteMetaDataProcessor())
        );
        $builder->toTimestamp(new \stdClass());
    }

    public function testToBool()
    {
        $builder = new SqliteParameterBuilder(
            new PdoDriver($this->getPdo(), new SqliteMetaDataProcessor())
        );
        $this->assertEquals('1', $builder->toBool(1));
        $this->assertEquals('0', $builder->toBool(0));
        $this->assertEquals('1', $builder->toBool(true));
        $this->assertEquals('0', $builder->toBool(false));
    }

    public function testToBoolMin()
    {
        $builder = new SqliteParameterBuilder(
            new PdoDriver($this->getPdo(), new SqliteMetaDataProcessor())
        );
        $this->assertEquals('0', $builder->toBool(QueryBuilder::MIN));
    }

    public function testToBoolMax()
    {
        $builder = new SqliteParameterBuilder(
            new PdoDriver($this->getPdo(), new SqliteMetaDataProcessor())
        );
        $this->assertEquals('1', $builder->toBool(QueryBuilder::MAX));
    }

    public function testToBoolNull()
    {
        $builder = new SqliteParameterBuilder(
            new PdoDriver($this->getPdo(), new SqliteMetaDataProcessor())
        );
        $this->assertEquals('NULL', $builder->toBool(null));
    }

    public function testToBoolEmptyString()
    {
        $builder = new SqliteParameterBuilder(
            new PdoDriver($this->getPdo(), new SqliteMetaDataProcessor())
        );
        $this->assertEquals('NULL', $builder->toBool(''));
    }

}
