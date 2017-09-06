<?php
/**
 * Volcanus libraries for PHP
 *
 * @copyright 2011-2013 k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */

namespace Volcanus\QueryBuilder\Test\Adapter\Sqlite;

use Volcanus\QueryBuilder\Adapter\Sqlite\SqliteExpressionBuilder;

/**
 * Test for SqliteExpressionBuilder
 *
 * @author k.holy74@gmail.com
 */
class SqliteExpressionBuilderTest extends \PHPUnit\Framework\TestCase
{

    public function testResultColumn()
    {
        $builder = new SqliteExpressionBuilder();
        $this->assertEquals('name AS "alias"', $builder->resultColumn('name', 'alias'));
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
