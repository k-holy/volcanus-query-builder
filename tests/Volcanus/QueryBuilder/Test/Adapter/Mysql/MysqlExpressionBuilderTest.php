<?php
/**
 * Volcanus libraries for PHP
 *
 * @copyright k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */

namespace Volcanus\QueryBuilder\Test\Adapter\Mysql;

use PHPUnit\Framework\TestCase;
use Volcanus\QueryBuilder\Adapter\Mysql\MysqlExpressionBuilder;

/**
 * Test for MysqlExpressionBuilder
 *
 * @author k.holy74@gmail.com
 */
class MysqlExpressionBuilderTest extends TestCase
{

    public function testResultColumn()
    {
        $builder = new MysqlExpressionBuilder();
        $this->assertEquals('name AS `alias`', $builder->resultColumn('name', 'alias'));
    }

    public function testAsDate()
    {
        $builder = new MysqlExpressionBuilder();
        $this->assertEquals("DATE_FORMAT(name, '%Y-%m-%d')", $builder->asDate('name'));
    }

    public function testAsTimestamp()
    {
        $builder = new MysqlExpressionBuilder();
        $this->assertEquals("DATE_FORMAT(name, '%Y-%m-%d %H:%i:%s')", $builder->asTimestamp('name'));
    }

    public function testAsGeometry()
    {
        $builder = new MysqlExpressionBuilder();
        $this->assertEquals("ASTEXT(name)", $builder->asGeometry('name'));
    }

}
