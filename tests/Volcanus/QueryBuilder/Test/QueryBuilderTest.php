<?php
/**
 * Volcanus libraries for PHP 8.1~
 *
 * @copyright k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */

namespace Volcanus\QueryBuilder\Test;

use PHPUnit\Framework\TestCase;
use Volcanus\QueryBuilder\QueryBuilder;
use Volcanus\Database\Driver\Pdo\PdoDriver;
use Volcanus\QueryBuilder\Facade;
use Volcanus\QueryBuilder\Adapter\Sqlite\SqliteQueryBuilder;

/**
 * Test for QueryBuilder
 *
 * @author k.holy74@gmail.com
 */
class QueryBuilderTest extends TestCase
{

    public function testCreateAdapterByFactory()
    {
        $this->assertInstanceOf(SqliteQueryBuilder::class,
            QueryBuilder::factory(
                new PdoDriver(
                    new \PDO('sqlite::memory:')
                )
            )
        );
    }

    public function testCreateFacade()
    {
        $this->assertInstanceOf(Facade::class,
            QueryBuilder::facade(
                new PdoDriver(
                    new \PDO('sqlite::memory:')
                )
            )
        );
    }

}
