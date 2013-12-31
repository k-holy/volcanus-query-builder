<?php
/**
 * Volcanus libraries for PHP
 *
 * @copyright 2011-2013 k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */

namespace Volcanus\QueryBuilder\Test;

use Volcanus\QueryBuilder\Facade;
use Volcanus\QueryBuilder\QueryBuilder;

use Volcanus\QueryBuilder\Adapter\Sqlite\SqliteQueryBuilder;
use Volcanus\QueryBuilder\Adapter\Sqlite\SqliteExpressionBuilder;
use Volcanus\QueryBuilder\Adapter\Sqlite\SqliteParameterBuilder;

use Volcanus\Database\Driver\Pdo\PdoDriver;
use Volcanus\Database\MetaData\SqliteMetaDataProcessor;

/**
 * Test for Facade
 *
 * @author k.holy74@gmail.com
 */
class FacadeTest extends \PHPUnit_Framework_TestCase
{

	private static $pdo;

	private function getPdo()
	{
		if (!isset(static::$pdo)) {
			static::$pdo = new \PDO('sqlite::memory:');
			static::$pdo->exec(<<<SQL
CREATE TABLE test(
     id         INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL
    ,name       TEXT
    ,updated_at DATETIME NOT NULL
);
SQL
			);
		}
		return static::$pdo;
	}

	private function getDriver()
	{
		return new PdoDriver(
			$this->getPdo(),
			new SqliteMetaDataProcessor()
		);
	}

	private function getBuilder()
	{
		return new SqliteQueryBuilder(
			new SqliteExpressionBuilder(),
			new SqliteParameterBuilder($this->getDriver())
		);
	}

	public function testExpression()
	{
		$facade = new Facade($this->getDriver(), $this->getBuilder());
		$this->assertEquals('test.id AS "id"', $facade->expression('test.id', 'int', "id"));
		$this->assertEquals('test.name AS "name"', $facade->expression('test.name', 'text', "name"));
		$this->assertEquals("strftime('%Y-%m-%d %H:%i:%s', test.updated_at) AS \"updated_at_formatted\"", $facade->expression('test.updated_at', 'datetime', "updated_at_formatted"));
	}

	public function testExpressions()
	{
		$facade = new Facade($this->getDriver(), $this->getBuilder());
		$expressions = $facade->expressions('test');
		$this->assertEquals('test.id AS "id"', $expressions['id']);
		$this->assertEquals('test.name AS "name"', $expressions['name']);
		$this->assertEquals("strftime('%Y-%m-%d %H:%i:%s', test.updated_at) AS \"updated_at\"", $expressions['updated_at']);
	}

	public function testExpressionsWithTableAlias()
	{
		$facade = new Facade($this->getDriver(), $this->getBuilder());
		$expressions = $facade->expressions('test', 't01');
		$this->assertEquals('t01.id AS "id"', $expressions['id']);
		$this->assertEquals('t01.name AS "name"', $expressions['name']);
		$this->assertEquals("strftime('%Y-%m-%d %H:%i:%s', t01.updated_at) AS \"updated_at\"", $expressions['updated_at']);
	}

	public function testExpressionsWithExcludeKeys()
	{
		$facade = new Facade($this->getDriver(), $this->getBuilder());
		$expressions = $facade->expressions('test', null, array('updated_at'));
		$this->assertArrayHasKey('id', $expressions);
		$this->assertArrayHasKey('name', $expressions);
		$this->assertArrayNotHasKey('updated_at', $expressions);
	}

	public function testExpressionsWithColumnAliases()
	{
		$facade = new Facade($this->getDriver(), $this->getBuilder());
		$expressions = $facade->expressions('test', null, null, array('updated_at' => 'updated_at_formatted'));
		$this->assertEquals('test.id AS "id"', $expressions['id']);
		$this->assertEquals('test.name AS "name"', $expressions['name']);
		$this->assertEquals("strftime('%Y-%m-%d %H:%i:%s', test.updated_at) AS \"updated_at_formatted\"", $expressions['updated_at']);
	}

	public function testParameter()
	{
		$facade = new Facade($this->getDriver(), $this->getBuilder());
		$this->assertEquals('1', $facade->parameter(1, 'int'));
		$this->assertEquals("'Foo'", $facade->parameter('Foo', 'text'));
		$this->assertEquals("datetime('2013-10-01 00:00:00')", $facade->parameter(new \DateTime('2013-10-01 00:00:00'), 'datetime'));
	}

	public function testParameters()
	{
		$facade = new Facade($this->getDriver(), $this->getBuilder());
		$columns = array(
			'id'         => '1',
			'name'       => 'Foo',
			'updated_at' => new \DateTime('2013-10-01 00:00:00'),
		);
		$parameters = $facade->parameters('test', $columns);
		$this->assertEquals('1', $parameters['id']);
		$this->assertEquals("'Foo'", $parameters['name']);
		$this->assertEquals("datetime('2013-10-01 00:00:00')", $parameters['updated_at']);
	}

	public function testInsert()
	{
		$facade = new Facade($this->getDriver(), $this->getBuilder());
		$columns = array(
			'id'         => '1',
			'name'       => 'Foo',
			'updated_at' => new \DateTime('2013-10-01 00:00:00'),
		);
		$this->assertEquals(<<<SQL
INSERT INTO
test
(id, name, updated_at)
VALUES
(1, 'Foo', datetime('2013-10-01 00:00:00'))
SQL
			, $facade->insert('test', $columns)
		);
	}

	public function testUpdate()
	{
		$facade = new Facade($this->getDriver(), $this->getBuilder());
		$columns = array(
			'id'         => '1',
			'name'       => 'Foo',
			'updated_at' => new \DateTime('2013-10-01 00:00:00'),
		);
		$this->assertEquals(<<<SQL
UPDATE
test
SET
id = 1,
name = 'Foo',
updated_at = datetime('2013-10-01 00:00:00')
SQL
			, $facade->update('test', $columns)
		);
	}

	public function testUpdateWithWhere()
	{
		$facade = new Facade($this->getDriver(), $this->getBuilder());
		$columns = array(
			'id'         => '1',
			'name'       => 'Foo',
			'updated_at' => new \DateTime('2013-10-01 00:00:00'),
		);
		$this->assertEquals(<<<SQL
UPDATE
test
SET
id = 1,
name = 'Foo',
updated_at = datetime('2013-10-01 00:00:00')
WHERE
id = 1
SQL
			, $facade->update('test', $columns, 'id = 1')
		);
	}

	public function testDelete()
	{
		$facade = new Facade($this->getDriver(), $this->getBuilder());
		$this->assertEquals(<<<SQL
DELETE FROM
test
SQL
			, $facade->delete('test')
		);
	}

	public function testDeleteWithWhere()
	{
		$facade = new Facade($this->getDriver(), $this->getBuilder());
		$this->assertEquals(<<<SQL
DELETE FROM
test
WHERE
id = 1
SQL
			, $facade->delete('test', 'id = 1')
		);
	}

	public function testSelectSyntax()
	{
		$facade = new Facade($this->getDriver(), $this->getBuilder());
		$this->assertEquals(<<<SQL
SELECT
test.id AS "id",
test.name AS "name",
strftime('%Y-%m-%d %H:%i:%s', test.updated_at) AS "updated_at"
SQL
			, $facade->selectSyntax('test')
		);
	}

	public function testFromSyntax()
	{
		$facade = new Facade($this->getDriver(), $this->getBuilder());
		$this->assertEquals(<<<SQL
FROM
test
SQL
			, $facade->fromSyntax('test')
		);
	}

	public function testSelect()
	{
		$facade = new Facade($this->getDriver(), $this->getBuilder());
		$this->assertEquals(<<<SQL
SELECT
test.id AS "id",
test.name AS "name",
strftime('%Y-%m-%d %H:%i:%s', test.updated_at) AS "updated_at"
FROM
test
SQL
			, $facade->select('test')
		);
	}

	public function testSelectWithTableAlias()
	{
		$facade = new Facade($this->getDriver(), $this->getBuilder());
		$this->assertEquals(<<<SQL
SELECT
t01.id AS "id",
t01.name AS "name",
strftime('%Y-%m-%d %H:%i:%s', t01.updated_at) AS "updated_at"
FROM
test t01
SQL
			, $facade->select('test', 't01')
		);
	}

	public function testSelectWithWhere()
	{
		$facade = new Facade($this->getDriver(), $this->getBuilder());
		$this->assertEquals(<<<SQL
SELECT
test.id AS "id",
test.name AS "name",
strftime('%Y-%m-%d %H:%i:%s', test.updated_at) AS "updated_at"
FROM
test
WHERE
id = 1
SQL
			, $facade->select('test', null, 'id = 1')
		);
	}

	public function testSelectWithExcludeKeys()
	{
		$facade = new Facade($this->getDriver(), $this->getBuilder());
		$this->assertEquals(<<<SQL
SELECT
test.id AS "id",
test.name AS "name"
FROM
test
SQL
			, $facade->select('test', null, null, array('updated_at'))
		);
	}

	public function testSelectWithColumnAliases()
	{
		$facade = new Facade($this->getDriver(), $this->getBuilder());
		$this->assertEquals(<<<SQL
SELECT
test.id AS "id",
test.name AS "name",
strftime('%Y-%m-%d %H:%i:%s', test.updated_at) AS "updated_at_formatted"
FROM
test
SQL
			, $facade->select('test', null, null, null, array('updated_at' => 'updated_at_formatted'))
		);
	}

	public function testLimitOffset()
	{
		$facade = new Facade($this->getDriver(), $this->getBuilder());
		$this->assertEquals("SELECT * FROM test LIMIT 10 OFFSET 1",
			$facade->limitOffset("SELECT * FROM test", 10, 1)
		);
	}

	public function testCount()
	{
		$facade = new Facade($this->getDriver(), $this->getBuilder());
		$this->assertEquals("SELECT COUNT(*) FROM (SELECT * FROM test) AS __SUBQUERY",
			$facade->count("SELECT * FROM test")
		);
	}

	public function testWhereExpressionsEqual()
	{
		$facade = new Facade($this->getDriver(), $this->getBuilder());
		$columns = array(
			'id' => '1',
		);
		$expressions = $facade->whereExpressions('test', null, $columns);
		$this->assertEquals('test.id = 1', $expressions[0]);
	}

	public function testWhereExpressionsEqualWithTableAlias()
	{
		$facade = new Facade($this->getDriver(), $this->getBuilder());
		$columns = array(
			'id' => '1',
		);
		$expressions = $facade->whereExpressions('test', 't01', $columns);
		$this->assertEquals('t01.id = 1', $expressions[0]);
	}

	public function testWhereExpressionsNotEqual()
	{
		$facade = new Facade($this->getDriver(), $this->getBuilder());
		$columns = array(
			QueryBuilder::PREFIX_NEGATIVE . 'id' => '1',
		);
		$expressions = $facade->whereExpressions('test', null, $columns);
		$this->assertEquals('test.id <> 1', $expressions[0]);
	}

	public function testWhereExpressionsNotEqualWithTableAlias()
	{
		$facade = new Facade($this->getDriver(), $this->getBuilder());
		$columns = array(
			QueryBuilder::PREFIX_NEGATIVE . 'id' => '1',
		);
		$expressions = $facade->whereExpressions('test', 't01', $columns);
		$this->assertEquals('t01.id <> 1', $expressions[0]);
	}

	public function testWhereExpressionsIn()
	{
		$facade = new Facade($this->getDriver(), $this->getBuilder());
		$columns = array(
			'id' => array('1', '2', '3'),
		);
		$expressions = $facade->whereExpressions('test', null, $columns);
		$this->assertEquals('test.id IN (1,2,3)', $expressions[0]);
	}

	public function testWhereExpressionsInWithTableAlias()
	{
		$facade = new Facade($this->getDriver(), $this->getBuilder());
		$columns = array(
			'id' => array('1', '2', '3'),
		);
		$expressions = $facade->whereExpressions('test', 't01', $columns);
		$this->assertEquals('t01.id IN (1,2,3)', $expressions[0]);
	}

	public function testWhereExpressionsNotIn()
	{
		$facade = new Facade($this->getDriver(), $this->getBuilder());
		$columns = array(
			QueryBuilder::PREFIX_NEGATIVE . 'id' => array('1', '2', '3'),
		);
		$expressions = $facade->whereExpressions('test', null, $columns);
		$this->assertEquals('test.id NOT IN (1,2,3)', $expressions[0]);
	}

	public function testWhereExpressionsNotInWithTableAlias()
	{
		$facade = new Facade($this->getDriver(), $this->getBuilder());
		$columns = array(
			QueryBuilder::PREFIX_NEGATIVE . 'id' => array('1', '2', '3'),
		);
		$expressions = $facade->whereExpressions('test', 't01', $columns);
		$this->assertEquals('t01.id NOT IN (1,2,3)', $expressions[0]);
	}

	public function testWhereExpressionsIsNull()
	{
		$facade = new Facade($this->getDriver(), $this->getBuilder());
		$columns = array(
			'id' => 'NULL'
		);
		$expressions = $facade->whereExpressions('test', null, $columns);
		$this->assertEquals('test.id IS NULL', $expressions[0]);
	}

	public function testWhereExpressionsIsNullWithTableAlias()
	{
		$facade = new Facade($this->getDriver(), $this->getBuilder());
		$columns = array(
			'id' => 'NULL'
		);
		$expressions = $facade->whereExpressions('test', 't01', $columns);
		$this->assertEquals('t01.id IS NULL', $expressions[0]);
	}

	public function testWhereExpressionsIsNotNull()
	{
		$facade = new Facade($this->getDriver(), $this->getBuilder());
		$columns = array(
			QueryBuilder::PREFIX_NEGATIVE . 'id' => 'NULL'
		);
		$expressions = $facade->whereExpressions('test', null, $columns);
		$this->assertEquals('test.id IS NOT NULL', $expressions[0]);
	}

	public function testWhereExpressionsIsNotNullWithTableAlias()
	{
		$facade = new Facade($this->getDriver(), $this->getBuilder());
		$columns = array(
			QueryBuilder::PREFIX_NEGATIVE . 'id' => 'NULL'
		);
		$expressions = $facade->whereExpressions('test', 't01', $columns);
		$this->assertEquals('t01.id IS NOT NULL', $expressions[0]);
	}

	public function testWhereExpressionsNoConvert()
	{
		$facade = new Facade($this->getDriver(), $this->getBuilder());
		$columns = array(
			QueryBuilder::PREFIX_NO_CONVERT . 'id' => '= (SELECT id FROM test WHERE 1=1)'
		);
		$expressions = $facade->whereExpressions('test', null, $columns);
		$this->assertEquals('test.id = (SELECT id FROM test WHERE 1=1)', $expressions[0]);
	}

	public function testWhereExpressionsNoConvertWithTableAlias()
	{
		$facade = new Facade($this->getDriver(), $this->getBuilder());
		$columns = array(
			QueryBuilder::PREFIX_NO_CONVERT . 'id' => '= (SELECT id FROM test WHERE 1=1)'
		);
		$expressions = $facade->whereExpressions('test', 't01', $columns);
		$this->assertEquals('t01.id = (SELECT id FROM test WHERE 1=1)', $expressions[0]);
	}

	public function testEscapeLikePattern()
	{
		$facade = new Facade($this->getDriver(), $this->getBuilder());
		$this->assertEquals('\\%Foo\\%', $facade->escapeLikePattern('%Foo%'));
		$this->assertEquals('\\_Foo\\_', $facade->escapeLikePattern('_Foo_'));
	}

}
