<?php
/**
 * Volcanus libraries for PHP 8.1~
 *
 * @copyright k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */

namespace Volcanus\QueryBuilder\Test\Adapter\Mysql;

use PHPUnit\Framework\TestCase;
use Volcanus\QueryBuilder\Adapter\Mysql\MysqlQueryBuilder;
use Volcanus\QueryBuilder\Adapter\Mysql\MysqlExpressionBuilder;
use Volcanus\QueryBuilder\Adapter\Mysql\MysqlParameterBuilder;

use Volcanus\Database\Driver\Pdo\PdoDriver;
use Volcanus\Database\MetaData\MysqlMetaDataProcessor;

/**
 * Test for MysqlQueryBuilder
 *
 * @author k.holy74@gmail.com
 */
class MysqlQueryBuilderTest extends TestCase
{

    private static \PDO $pdo;

    private function getPdo(): \PDO
    {
        if (!isset(static::$pdo)) {
            static::$pdo = new \PDO('sqlite::memory:');
        }
        return static::$pdo;
    }

    public function testParameterTypeOfText()
    {
        $builder = new MysqlQueryBuilder(
            new MysqlExpressionBuilder(),
            new MysqlParameterBuilder(new PdoDriver($this->getPdo(), new MysqlMetaDataProcessor()))
        );
        $this->assertEquals('text', $builder->parameterType('text'));
        $this->assertEquals('text', $builder->parameterType('char'));
        $this->assertEquals('text', $builder->parameterType('varchar'));
        $this->assertEquals('text', $builder->parameterType('tinytext'));
        $this->assertEquals('text', $builder->parameterType('longtext'));
        $this->assertEquals('text', $builder->parameterType('mediumtext'));
        $this->assertEquals('text', $builder->parameterType('json'));
    }

    public function testParameterTypeOfInt()
    {
        $builder = new MysqlQueryBuilder(
            new MysqlExpressionBuilder(),
            new MysqlParameterBuilder(new PdoDriver($this->getPdo(), new MysqlMetaDataProcessor()))
        );
        $this->assertEquals('int', $builder->parameterType('int'));
        $this->assertEquals('int', $builder->parameterType('integer'));
        $this->assertEquals('int', $builder->parameterType('tinyint'));
        $this->assertEquals('int', $builder->parameterType('int4'));
        $this->assertEquals('int', $builder->parameterType('smallint'));
        $this->assertEquals('int', $builder->parameterType('mediumint'));
        $this->assertEquals('int', $builder->parameterType('bigint'));
    }

    public function testParameterTypeOfFloat()
    {
        $builder = new MysqlQueryBuilder(
            new MysqlExpressionBuilder(),
            new MysqlParameterBuilder(new PdoDriver($this->getPdo(), new MysqlMetaDataProcessor()))
        );
        $this->assertEquals('float', $builder->parameterType('real'));
        $this->assertEquals('float', $builder->parameterType('double'));
        $this->assertEquals('float', $builder->parameterType('float'));
        $this->assertEquals('float', $builder->parameterType('decimal'));
    }

    public function testParameterTypeOfDate()
    {
        $builder = new MysqlQueryBuilder(
            new MysqlExpressionBuilder(),
            new MysqlParameterBuilder(new PdoDriver($this->getPdo(), new MysqlMetaDataProcessor()))
        );
        $this->assertEquals('date', $builder->parameterType('date'));
    }

    public function testParameterTypeOfTimestamp()
    {
        $builder = new MysqlQueryBuilder(
            new MysqlExpressionBuilder(),
            new MysqlParameterBuilder(new PdoDriver($this->getPdo(), new MysqlMetaDataProcessor()))
        );
        $this->assertEquals('timestamp', $builder->parameterType('timestamp'));
        $this->assertEquals('timestamp', $builder->parameterType('datetime'));
    }

    public function testParameterTypeReturnFalseWhenUnsupportedType()
    {
        $builder = new MysqlQueryBuilder(
            new MysqlExpressionBuilder(),
            new MysqlParameterBuilder(new PdoDriver($this->getPdo(), new MysqlMetaDataProcessor()))
        );
        $this->assertFalse($builder->parameterType('unsupported-type'));
    }

    public function testParameterCallParameterBuilderToText()
    {
        $builder = new MysqlQueryBuilder(
            new MysqlExpressionBuilder(),
            new MysqlParameterBuilder(new PdoDriver($this->getPdo(), new MysqlMetaDataProcessor()))
        );
        $this->assertEquals("'Foo'", $builder->parameter('Foo', 'text'));
    }

    public function testParameterCallParameterBuilderToInt()
    {
        $builder = new MysqlQueryBuilder(
            new MysqlExpressionBuilder(),
            new MysqlParameterBuilder(new PdoDriver($this->getPdo(), new MysqlMetaDataProcessor()))
        );
        $this->assertEquals('1', $builder->parameter(1, 'int'));
    }

    public function testParameterCallParameterBuilderToFloat()
    {
        $builder = new MysqlQueryBuilder(
            new MysqlExpressionBuilder(),
            new MysqlParameterBuilder(new PdoDriver($this->getPdo(), new MysqlMetaDataProcessor()))
        );
        $this->assertEquals('0.1', $builder->parameter(0.1, 'float'));
    }

    public function testParameterCallParameterBuilderToBool()
    {
        $builder = new MysqlQueryBuilder(
            new MysqlExpressionBuilder(),
            new MysqlParameterBuilder(new PdoDriver($this->getPdo(), new MysqlMetaDataProcessor()))
        );
        $this->assertEquals('1', $builder->parameter(true, 'bool'));
    }

    public function testParameterCallParameterBuilderToDate()
    {
        $builder = new MysqlQueryBuilder(
            new MysqlExpressionBuilder(),
            new MysqlParameterBuilder(new PdoDriver($this->getPdo(), new MysqlMetaDataProcessor()))
        );
        $this->assertEquals(
            "STR_TO_DATE('2013-01-02', '%Y-%m-%d')",
            $builder->parameter('2013-01-02', 'date')
        );
    }

    public function testParameterCallParameterBuilderToTimestamp()
    {
        $builder = new MysqlQueryBuilder(
            new MysqlExpressionBuilder(),
            new MysqlParameterBuilder(new PdoDriver($this->getPdo(), new MysqlMetaDataProcessor()))
        );
        $this->assertEquals(
            "STR_TO_DATE('2013-01-02 00:00:00', '%Y-%m-%d %H:%i:%s')",
            $builder->parameter('2013-01-02 00:00:00', 'timestamp')
        );
    }

    public function testParameterRaiseExceptionWhenUnsupportedType()
    {
        $this->expectException(\InvalidArgumentException::class);
        $builder = new MysqlQueryBuilder(
            new MysqlExpressionBuilder(),
            new MysqlParameterBuilder(new PdoDriver($this->getPdo(), new MysqlMetaDataProcessor()))
        );
        $builder->parameter('Foo', 'unsupported-type');
    }

    public function testLimitOffset()
    {
        $builder = new MysqlQueryBuilder(
            new MysqlExpressionBuilder(),
            new MysqlParameterBuilder(new PdoDriver($this->getPdo(), new MysqlMetaDataProcessor()))
        );
        $this->assertEquals(
            'SELECT * FROM test LIMIT 20,20',
            $builder->limitOffset('SELECT * FROM test', 20, 20)
        );
    }

    public function testLimitOffsetWithoutOffset()
    {
        $builder = new MysqlQueryBuilder(
            new MysqlExpressionBuilder(),
            new MysqlParameterBuilder(new PdoDriver($this->getPdo(), new MysqlMetaDataProcessor()))
        );
        $this->assertEquals(
            'SELECT * FROM test LIMIT 20',
            $builder->limitOffset('SELECT * FROM test', 20)
        );
    }

    public function testLimitOffsetWithNullOffset()
    {
        $builder = new MysqlQueryBuilder(
            new MysqlExpressionBuilder(),
            new MysqlParameterBuilder(new PdoDriver($this->getPdo(), new MysqlMetaDataProcessor()))
        );
        $this->assertEquals(
            'SELECT * FROM test LIMIT 20',
            $builder->limitOffset('SELECT * FROM test', 20, null)
        );
    }

    public function testLimitOffsetWithoutLimit()
    {
        $builder = new MysqlQueryBuilder(
            new MysqlExpressionBuilder(),
            new MysqlParameterBuilder(new PdoDriver($this->getPdo(), new MysqlMetaDataProcessor()))
        );
        $this->assertEquals(
            'SELECT * FROM test LIMIT 20,18446744073709551615',
            $builder->limitOffset('SELECT * FROM test', null, 20)
        );
    }

    public function testCount()
    {
        $builder = new MysqlQueryBuilder(
            new MysqlExpressionBuilder(),
            new MysqlParameterBuilder(new PdoDriver($this->getPdo(), new MysqlMetaDataProcessor()))
        );
        $this->assertEquals(
            'SELECT COUNT(*) FROM (SELECT * FROM test) AS __SUBQUERY',
            $builder->count('SELECT * FROM test')
        );
    }

    public function testCountWithSqlCalcFoundRows()
    {
        $builder = new MysqlQueryBuilder(
            new MysqlExpressionBuilder(),
            new MysqlParameterBuilder(new PdoDriver($this->getPdo(), new MysqlMetaDataProcessor()))
        );
        $this->assertEquals(
            'SELECT FOUND_ROWS()',
            $builder->count('SELECT SQL_CALC_FOUND_ROWS  * FROM test')
        );
    }

    public function testExpressionAsText()
    {
        $builder = new MysqlQueryBuilder(
            new MysqlExpressionBuilder(),
            new MysqlParameterBuilder(new PdoDriver($this->getPdo(), new MysqlMetaDataProcessor()))
        );
        $this->assertEquals(
            'name',
            $builder->expression('name', 'text')
        );
    }

    public function testExpressionAsTextWithAlias()
    {
        $builder = new MysqlQueryBuilder(
            new MysqlExpressionBuilder(),
            new MysqlParameterBuilder(new PdoDriver($this->getPdo(), new MysqlMetaDataProcessor()))
        );
        $this->assertEquals(
            'name AS `alias_name`',
            $builder->expression('name', 'text', 'alias_name')
        );
    }

    public function testExpressionAsDate()
    {
        $builder = new MysqlQueryBuilder(
            new MysqlExpressionBuilder(),
            new MysqlParameterBuilder(new PdoDriver($this->getPdo(), new MysqlMetaDataProcessor()))
        );
        $this->assertEquals(
            "DATE_FORMAT(birthday, '%Y-%m-%d') AS `birthday`",
            $builder->expression('birthday', 'date')
        );
    }

    public function testExpressionAsDateWithAlias()
    {
        $builder = new MysqlQueryBuilder(
            new MysqlExpressionBuilder(),
            new MysqlParameterBuilder(new PdoDriver($this->getPdo(), new MysqlMetaDataProcessor()))
        );
        $this->assertEquals(
            "DATE_FORMAT(birthday, '%Y-%m-%d') AS `birthday_formatted`",
            $builder->expression('birthday', 'date', 'birthday_formatted')
        );
    }

    public function testExpressionAsTimestamp()
    {
        $builder = new MysqlQueryBuilder(
            new MysqlExpressionBuilder(),
            new MysqlParameterBuilder(new PdoDriver($this->getPdo(), new MysqlMetaDataProcessor()))
        );
        $this->assertEquals(
            "DATE_FORMAT(birthday, '%Y-%m-%d %H:%i:%s') AS `birthday`",
            $builder->expression('birthday', 'timestamp')
        );
    }

    public function testExpressionAsTimestampWithAlias()
    {
        $builder = new MysqlQueryBuilder(
            new MysqlExpressionBuilder(),
            new MysqlParameterBuilder(new PdoDriver($this->getPdo(), new MysqlMetaDataProcessor()))
        );
        $this->assertEquals(
            "DATE_FORMAT(birthday, '%Y-%m-%d %H:%i:%s') AS `birthday_formatted`",
            $builder->expression('birthday', 'timestamp', 'birthday_formatted')
        );
    }

    public function testExpressionRaiseExceptionWhenUnsupportedType()
    {
        $this->expectException(\InvalidArgumentException::class);
        $builder = new MysqlQueryBuilder(
            new MysqlExpressionBuilder(),
            new MysqlParameterBuilder(new PdoDriver($this->getPdo(), new MysqlMetaDataProcessor()))
        );
        $builder->expression('name', 'unsupported-type');
    }

    public function testEscapeLikePattern()
    {
        $builder = new MysqlQueryBuilder(
            new MysqlExpressionBuilder(),
            new MysqlParameterBuilder(new PdoDriver($this->getPdo(), new MysqlMetaDataProcessor()))
        );
        $this->assertEquals('\\%Foo\\%', $builder->escapeLikePattern('%Foo%'));
        $this->assertEquals('\\_Foo\\_', $builder->escapeLikePattern('_Foo_'));
    }

}
