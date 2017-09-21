<?php
/**
 * Volcanus libraries for PHP
 *
 * @copyright 2011-2013 k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */

namespace Volcanus\QueryBuilder\Test\Adapter\Sqlite;

use Volcanus\QueryBuilder\Adapter\Sqlite\SqliteQueryBuilder;
use Volcanus\QueryBuilder\Adapter\Sqlite\SqliteExpressionBuilder;
use Volcanus\QueryBuilder\Adapter\Sqlite\SqliteParameterBuilder;

use Volcanus\Database\Driver\Pdo\PdoDriver;
use Volcanus\Database\MetaData\SqliteMetaDataProcessor;

/**
 * Test for SqliteQueryBuilder
 *
 * @author k.holy74@gmail.com
 */
class SqliteQueryBuilderTest extends \PHPUnit_Framework_TestCase
{

    /** @var \PDO */
    private static $pdo;

    public function getPdo()
    {
        if (!isset(static::$pdo)) {
            static::$pdo = new \PDO('sqlite::memory:');
        }
        return static::$pdo;
    }

    public function testParameterTypeOfText()
    {
        $builder = new SqliteQueryBuilder(
            new SqliteExpressionBuilder(),
            new SqliteParameterBuilder(new PdoDriver($this->getPdo(), new SqliteMetaDataProcessor()))
        );
        $this->assertEquals('text', $builder->parameterType('character'));
        $this->assertEquals('text', $builder->parameterType('varchar'));
        $this->assertEquals('text', $builder->parameterType('varying character'));
        $this->assertEquals('text', $builder->parameterType('nchar'));
        $this->assertEquals('text', $builder->parameterType('native character'));
        $this->assertEquals('text', $builder->parameterType('nvarchar'));
        $this->assertEquals('text', $builder->parameterType('text'));
        $this->assertEquals('text', $builder->parameterType('clob'));
    }

    public function testParameterTypeOfInt()
    {
        $builder = new SqliteQueryBuilder(
            new SqliteExpressionBuilder(),
            new SqliteParameterBuilder(new PdoDriver($this->getPdo(), new SqliteMetaDataProcessor()))
        );
        $this->assertEquals('int', $builder->parameterType('int'));
        $this->assertEquals('int', $builder->parameterType('integer'));
        $this->assertEquals('int', $builder->parameterType('tinyint'));
        $this->assertEquals('int', $builder->parameterType('smallint'));
        $this->assertEquals('int', $builder->parameterType('mediumint'));
        $this->assertEquals('int', $builder->parameterType('bigint'));
        $this->assertEquals('int', $builder->parameterType('int2'));
        $this->assertEquals('int', $builder->parameterType('int8'));
    }

    public function testParameterTypeOfFloat()
    {
        $builder = new SqliteQueryBuilder(
            new SqliteExpressionBuilder(),
            new SqliteParameterBuilder(new PdoDriver($this->getPdo(), new SqliteMetaDataProcessor()))
        );
        $this->assertEquals('float', $builder->parameterType('real'));
        $this->assertEquals('float', $builder->parameterType('double'));
        $this->assertEquals('float', $builder->parameterType('double precision'));
        $this->assertEquals('float', $builder->parameterType('float'));
    }

    public function testParameterTypeOfBool()
    {
        $builder = new SqliteQueryBuilder(
            new SqliteExpressionBuilder(),
            new SqliteParameterBuilder(new PdoDriver($this->getPdo(), new SqliteMetaDataProcessor()))
        );
        $this->assertEquals('bool', $builder->parameterType('boolean'));
    }

    public function testParameterTypeOfDate()
    {
        $builder = new SqliteQueryBuilder(
            new SqliteExpressionBuilder(),
            new SqliteParameterBuilder(new PdoDriver($this->getPdo(), new SqliteMetaDataProcessor()))
        );
        $this->assertEquals('date', $builder->parameterType('date'));
    }

    public function testParameterTypeOfTimestamp()
    {
        $builder = new SqliteQueryBuilder(
            new SqliteExpressionBuilder(),
            new SqliteParameterBuilder(new PdoDriver($this->getPdo(), new SqliteMetaDataProcessor()))
        );
        $this->assertEquals('timestamp', $builder->parameterType('datetime'));
    }

    public function testParameterTypeReturnFalseWhenUnsupportedType()
    {
        $builder = new SqliteQueryBuilder(
            new SqliteExpressionBuilder(),
            new SqliteParameterBuilder(new PdoDriver($this->getPdo(), new SqliteMetaDataProcessor()))
        );
        $this->assertFalse($builder->parameterType('unsupported-type'));
    }

    public function testParameterCallParameterBuilderToText()
    {
        $builder = new SqliteQueryBuilder(
            new SqliteExpressionBuilder(),
            new SqliteParameterBuilder(new PdoDriver($this->getPdo(), new SqliteMetaDataProcessor()))
        );
        $this->assertEquals("'Foo'", $builder->parameter('Foo', 'text'));
    }

    public function testParameterCallParameterBuilderToInt()
    {
        $builder = new SqliteQueryBuilder(
            new SqliteExpressionBuilder(),
            new SqliteParameterBuilder(new PdoDriver($this->getPdo(), new SqliteMetaDataProcessor()))
        );
        $this->assertEquals('1', $builder->parameter(1, 'int'));
    }

    public function testParameterCallParameterBuilderToFloat()
    {
        $builder = new SqliteQueryBuilder(
            new SqliteExpressionBuilder(),
            new SqliteParameterBuilder(new PdoDriver($this->getPdo(), new SqliteMetaDataProcessor()))
        );
        $this->assertEquals('0.1', $builder->parameter(0.1, 'float'));
    }

    public function testParameterCallParameterBuilderToBool()
    {
        $builder = new SqliteQueryBuilder(
            new SqliteExpressionBuilder(),
            new SqliteParameterBuilder(new PdoDriver($this->getPdo(), new SqliteMetaDataProcessor()))
        );
        $this->assertEquals('1', $builder->parameter(true, 'bool'));
    }

    public function testParameterCallParameterBuilderToDate()
    {
        $builder = new SqliteQueryBuilder(
            new SqliteExpressionBuilder(),
            new SqliteParameterBuilder(new PdoDriver($this->getPdo(), new SqliteMetaDataProcessor()))
        );
        $this->assertEquals(
            "date('2013-01-02')",
            $builder->parameter('2013-01-02', 'date')
        );
    }

    public function testParameterCallParameterBuilderToTimestamp()
    {
        $builder = new SqliteQueryBuilder(
            new SqliteExpressionBuilder(),
            new SqliteParameterBuilder(new PdoDriver($this->getPdo(), new SqliteMetaDataProcessor()))
        );
        $this->assertEquals(
            "datetime('2013-01-02 00:00:00')",
            $builder->parameter('2013-01-02 00:00:00', 'timestamp')
        );
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testParameterRaiseExceptionWhenUnsupportedType()
    {
        $builder = new SqliteQueryBuilder(
            new SqliteExpressionBuilder(),
            new SqliteParameterBuilder(new PdoDriver($this->getPdo(), new SqliteMetaDataProcessor()))
        );
        $builder->parameter('Foo', 'unsupported-type');
    }

    public function testLimitOffset()
    {
        $builder = new SqliteQueryBuilder(
            new SqliteExpressionBuilder(),
            new SqliteParameterBuilder(new PdoDriver($this->getPdo(), new SqliteMetaDataProcessor()))
        );
        $this->assertEquals(
            'SELECT * FROM test LIMIT 20 OFFSET 20',
            $builder->limitOffset('SELECT * FROM test', 20, 20)
        );
    }

    public function testLimitOffsetWithoutOffset()
    {
        $builder = new SqliteQueryBuilder(
            new SqliteExpressionBuilder(),
            new SqliteParameterBuilder(new PdoDriver($this->getPdo(), new SqliteMetaDataProcessor()))
        );
        $this->assertEquals(
            'SELECT * FROM test LIMIT 20',
            $builder->limitOffset('SELECT * FROM test', 20)
        );
    }

    public function testLimitOffsetWithoutLimit()
    {
        $builder = new SqliteQueryBuilder(
            new SqliteExpressionBuilder(),
            new SqliteParameterBuilder(new PdoDriver($this->getPdo(), new SqliteMetaDataProcessor()))
        );
        $this->assertEquals(
            'SELECT * FROM test LIMIT 18446744073709551615 OFFSET 20',
            $builder->limitOffset('SELECT * FROM test', null, 20)
        );
    }

    public function testCount()
    {
        $builder = new SqliteQueryBuilder(
            new SqliteExpressionBuilder(),
            new SqliteParameterBuilder(new PdoDriver($this->getPdo(), new SqliteMetaDataProcessor()))
        );
        $this->assertEquals(
            'SELECT COUNT(*) FROM (SELECT * FROM test) AS __SUBQUERY',
            $builder->count('SELECT * FROM test')
        );
    }

    public function testExpressionAsText()
    {
        $builder = new SqliteQueryBuilder(
            new SqliteExpressionBuilder(),
            new SqliteParameterBuilder(new PdoDriver($this->getPdo(), new SqliteMetaDataProcessor()))
        );
        $this->assertEquals(
            'name',
            $builder->expression('name', 'text')
        );
    }

    public function testExpressionAsTextWithAlias()
    {
        $builder = new SqliteQueryBuilder(
            new SqliteExpressionBuilder(),
            new SqliteParameterBuilder(new PdoDriver($this->getPdo(), new SqliteMetaDataProcessor()))
        );
        $this->assertEquals(
            'name AS "alias_name"',
            $builder->expression('name', 'text', 'alias_name')
        );
    }

    public function testExpressionAsDate()
    {
        $builder = new SqliteQueryBuilder(
            new SqliteExpressionBuilder(),
            new SqliteParameterBuilder(new PdoDriver($this->getPdo(), new SqliteMetaDataProcessor()))
        );
        $this->assertEquals(
            "strftime('%Y-%m-%d', birthday) AS \"birthday\"",
            $builder->expression('birthday', 'date')
        );
    }

    public function testExpressionAsDateWithAlias()
    {
        $builder = new SqliteQueryBuilder(
            new SqliteExpressionBuilder(),
            new SqliteParameterBuilder(new PdoDriver($this->getPdo(), new SqliteMetaDataProcessor()))
        );
        $this->assertEquals(
            "strftime('%Y-%m-%d', birthday) AS \"birthday_formatted\"",
            $builder->expression('birthday', 'date', 'birthday_formatted')
        );
    }

    public function testExpressionAsTimestamp()
    {
        $builder = new SqliteQueryBuilder(
            new SqliteExpressionBuilder(),
            new SqliteParameterBuilder(new PdoDriver($this->getPdo(), new SqliteMetaDataProcessor()))
        );
        $this->assertEquals(
            "strftime('%Y-%m-%d %H:%i:%s', birthday) AS \"birthday\"",
            $builder->expression('birthday', 'timestamp')
        );
    }

    public function testExpressionAsTimestampWithAlias()
    {
        $builder = new SqliteQueryBuilder(
            new SqliteExpressionBuilder(),
            new SqliteParameterBuilder(new PdoDriver($this->getPdo(), new SqliteMetaDataProcessor()))
        );
        $this->assertEquals(
            "strftime('%Y-%m-%d %H:%i:%s', birthday) AS \"birthday_formatted\"",
            $builder->expression('birthday', 'timestamp', 'birthday_formatted')
        );
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testExpressionRaiseExceptionWhenUnsupportedType()
    {
        $builder = new SqliteQueryBuilder(
            new SqliteExpressionBuilder(),
            new SqliteParameterBuilder(new PdoDriver($this->getPdo(), new SqliteMetaDataProcessor()))
        );
        $builder->expression('name', 'unsupported-type');
    }

    public function testEscapeLikePattern()
    {
        $builder = new SqliteQueryBuilder(
            new SqliteExpressionBuilder(),
            new SqliteParameterBuilder(new PdoDriver($this->getPdo(), new SqliteMetaDataProcessor()))
        );
        $this->assertEquals('\\%Foo\\%', $builder->escapeLikePattern('%Foo%'));
        $this->assertEquals('\\_Foo\\_', $builder->escapeLikePattern('_Foo_'));
    }

}
