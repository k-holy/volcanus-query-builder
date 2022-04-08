<?php
/**
 * Volcanus libraries for PHP
 *
 * @copyright k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */

namespace Volcanus\QueryBuilder\Test;

use PHPUnit\Framework\TestCase;
use Volcanus\QueryBuilder\QueryBuilder;
use Volcanus\Database\Driver\Pdo\PdoDriver;

/**
 * Test for QueryBuilder
 *
 * @author k.holy74@gmail.com
 */
class QueryBuilderTest extends TestCase
{

    public function testCreateAdapterByFactory()
    {
        $this->assertInstanceOf('Volcanus\QueryBuilder\Adapter\\Sqlite\\SqliteQueryBuilder',
            QueryBuilder::factory(
                new PdoDriver(
                    new \PDO('sqlite::memory:')
                )
            )
        );
    }

    public function testCreateFacade()
    {
        $this->assertInstanceOf('Volcanus\QueryBuilder\Facade',
            QueryBuilder::facade(
                new PdoDriver(
                    new \PDO('sqlite::memory:')
                )
            )
        );
    }

}
