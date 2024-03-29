<?php
/**
 * Volcanus libraries for PHP 8.1~
 *
 * @copyright k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */

namespace Volcanus\QueryBuilder\Test\Adapter\Sqlite;

use PHPUnit\Framework\TestCase;
use Volcanus\QueryBuilder\Adapter\Sqlite\SqliteExpressionBuilder;

/**
 * Test for SqliteExpressionBuilder
 *
 * @author k.holy74@gmail.com
 */
class SqliteExpressionBuilderTest extends TestCase
{

    public function testResultColumn()
    {
        $builder = new SqliteExpressionBuilder();
        $this->assertEquals('name AS "alias"', $builder->resultColumn('name', 'alias'));
    }

    public function testResultColumnWithoutAlias()
    {
        $builder = new SqliteExpressionBuilder();
        $this->assertEquals('name', $builder->resultColumn('name'));
    }

    public function testResultColumnWithNullAlias()
    {
        $builder = new SqliteExpressionBuilder();
        $this->assertEquals('name', $builder->resultColumn('name', null));
    }

    public function testAsDate()
    {
        $builder = new SqliteExpressionBuilder();
        $this->assertEquals("strftime('%Y-%m-%d', name)", $builder->asDate('name'));
    }

    public function testAsTimestamp()
    {
        $builder = new SqliteExpressionBuilder();
        $this->assertEquals("strftime('%Y-%m-%d %H:%i:%s', name)", $builder->asTimestamp('name'));
    }

}
