<?php
/**
 * Volcanus libraries for PHP
 *
 * @copyright 2011-2013 k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */

namespace Volcanus\QueryBuilder;

use Volcanus\QueryBuilder\QueryBuilder;
use Volcanus\QueryBuilder\QueryBuilderInterface;
use Volcanus\Database\Driver\DriverInterface;

/**
 * Facadeクラス
 *
 * @author k.holy74@gmail.com
 */
class Facade
{
	/**
	 * @var Volcanus\Database\Driver\DriverInterface
	 */
	private $driver;

	/**
	 * @var Volcanus\QueryBuilder\QueryBuilderInterface
	 */
	private $builder;

	/**
	 * @var boolean 取得するカラム名をキャメルケースに変換するかどうか
	 */
	private $enableCamelize = false;

	public function __construct(DriverInterface $driver, QueryBuilderInterface $builder)
	{
		$this->driver = $driver;
		$this->builder = $builder;
	}

	/**
	 * 取得するカラム名をキャメルケースに変換するかどうかを設定します。
	 *
	 * @param boolean 取得するカラム名をキャメルケースに変換するかどうか
	 */
	public function enableCamelize($enabled = true)
	{
		$this->enableCamelize = (bool)$enabled;
	}

	/**
	 * データ型に合わせて項目を別名で取得するSQL句を生成します。
	 *
	 * @param string 項目名
	 * @param string データ型
	 * @param string 別名
	 * @return string SQL句
	 */
	public function expression($expr, $type = null, $alias = null)
	{
		return $this->builder->expression($expr, $type, $alias);
	}

	/**
	 * カラム定義に従い、SELECT句の配列を生成します。
	 *
	 * @param string テーブル名
	 * @param string テーブル別名
	 * @param array 除外列名のリスト
	 * @param array 別名取得設定 (キー=列名、値=別名)
	 * @return array SQL SELECT句となる列指定の配列
	 */
	public function expressions($tableName, $tableAlias = null, $excludeKeys = array(), $columnAliases = array())
	{
		$metaColumns = $this->driver->getMetaColumns($tableName);
		$expressions = array();
		foreach ($metaColumns as $column) {
			$columnName = ($this->enableCamelize) ? $this->camelize($column->name) : $column->name;
			if (is_array($excludeKeys) && in_array($columnName, $excludeKeys)) {
				continue;
			}
			$expressions[$column->name] = $this->expression(
				isset($tableAlias) ? $tableAlias . '.' . $column->name : $tableName . '.' . $column->name,
				$column->type,
				array_key_exists($columnName, $columnAliases) ? $columnAliases[$columnName] : $columnName
			);
		}
		return $expressions;
	}

	/**
	 * 値を指定した型に応じたSQLパラメータ値に変換します。
	 *
	 * @param string データ
	 * @param string 型名 ($typesフィールド参照)
	 * @return string 変換結果
	 */
	public function parameter($value, $type)
	{
		return $this->builder->parameter($value, $type);
	}

	/**
	 * カラム定義に従い、値を変換して返します。
	 * カラム定義に存在しない列名の値は無視します。
	 * 列名の先頭が PREFIX_NO_CONVERT の場合は値の変換を回避します。
	 *
	 * @param string テーブル名
	 * @param array 登録内容 array(列名 => 列値, 列名 => 列値...)
	 * @return array 変換済みの登録内容の配列
	 */
	public function parameters($tableName, $columns)
	{
		$metaColumns = $this->driver->getMetaColumns($tableName);
		$expressions = array();
		foreach ($columns as $name => $value) {
			$columnName = $this->enableCamelize ? $this->underscore($name) : $name;
			$noConvert = false;
			if (0 === strncmp($columnName, QueryBuilder::PREFIX_NO_CONVERT, 1)) {
				$columnName = substr($columnName, 1);
				$noConvert = true;
			}
			if (!array_key_exists($columnName, $metaColumns)) {
				continue;
			}
			$expressions[$columnName] = ($noConvert)
				? $value
				: $this->parameter($value, $metaColumns[$columnName]['type']);
		}
		return $expressions;
	}

	/**
	 * INSERT文を組み立てて返します。
	 *
	 * @param string テーブル名
	 * @param array 登録内容 array(列名 => 列値, 列名 => 列値...)
	 * @return string SQL
	 */
	public function insert($tableName, $columns)
	{
		$parameters = $this->parameters($tableName, $columns);
		$insertColumnNames = join(', ', array_keys($parameters));
		$insertColumnValues = join(', ', array_values($parameters));
		return <<<SQL
INSERT INTO
$tableName
($insertColumnNames)
VALUES
($insertColumnValues)
SQL;
	}

	/**
	 * UPDATE文を組み立てて返します。
	 *
	 * @param string テーブル名
	 * @param array 更新内容 array(列名 => 列値, 列名 => 列値...)
	 * @param string WHERE句
	 * @return string SQL
	 */
	public function update($tableName, $columns, $where = null)
	{
		$parameters = $this->parameters($tableName, $columns);
		$updateColumnSet = array();
		foreach ($parameters as $column => $value) {
			$updateColumnSet[] = sprintf('%s = %s', $column, $value);
		}
		$updateField = join(",\n", $updateColumnSet);
		$sql = <<<SQL
UPDATE
$tableName
SET
$updateField
SQL;
		return (is_string($where) && strlen($where) >= 1)
			? $sql . "\nWHERE\n" . $where
			: $sql;
	}

	/**
	 * DELETE文を組み立てて返します。
	 *
	 * @param string テーブル名
	 * @param string WHERE句
	 * @return string SQL
	 */
	public function delete($tableName, $where = null)
	{
		$sql = <<<SQL
DELETE FROM
$tableName
SQL;
		return (is_string($where) && strlen($where) >= 1)
			? $sql . "\nWHERE\n" . $where
			: $sql;
	}

	/**
	 * SELECT節を組み立てて返します。
	 *
	 * @param string テーブル名
	 * @param string テーブル別名
	 * @param string WHERE句
	 * @param array 除外列名のリスト
	 * @param array 別名取得設定 (キー=列名、値=別名)
	 * @return string SQL
	 */
	public function selectSyntax($tableName, $tableAlias = null, $excludeKeys = array(), $columnAliases = array())
	{
		$expressions = $this->expressions($tableName, $tableAlias, $excludeKeys, $columnAliases);
		return (is_array($expressions) && count($expressions) >= 1) ? "SELECT\n" . join(",\n", $expressions) : '';
	}

	/**
	 * FROM節を組み立てて返します。
	 *
	 * @param string テーブル名
	 * @param string テーブル別名
	 * @return string SQL FROM節
	 */
	public function fromSyntax($tableName, $tableAlias = null)
	{
		return (isset($tableAlias)) ? "FROM\n{$tableName} {$tableAlias}" : "FROM\n{$tableName}";
	}

	/**
	 * SELECT文を組み立てて返します。
	 *
	 * @param string テーブル名
	 * @param string テーブル別名
	 * @param string WHERE句
	 * @param array 除外列名のリスト
	 * @param array 別名取得設定 (キー=列名、値=別名)
	 * @return string SQL
	 */
	public function select($tableName, $tableAlias = null, $where = null, $excludeKeys = array(), $columnAliases = array())
	{
		$sql = join("\n", array(
			$this->selectSyntax($tableName, $tableAlias, $excludeKeys, $columnAliases),
			$this->fromSyntax($tableName, $tableAlias),
		));
		return (is_string($where) && strlen($where) >= 1)
			? $sql . "\nWHERE\n" . $where
			: $sql;
	}

	/**
	 * SELECT文を元に件数を返すクエリを生成して返します。
	 *
	 * @param string SELECT文
	 * @return string SQL
	 */
	public function count($sql)
	{
		return $this->builder->count($sql);
	}

	/**
	 * SELECT文にLIMIT値およびOFFSET値を付与して返します。
	 *
	 * @param string SELECT文
	 * @param int 最大取得件数
	 * @param int 取得開始行index
	 * @return string SQL
	 */
	public function limitOffset($sql, $limit = null, $offset = null)
	{
		return $this->builder->limitOffset($sql, $limit, $offset);
	}

	/**
	 * 抽出条件およびスキーマ情報に合わせてSQLのWHERE句を作成します。
	 *
	 * @param string テーブル名
	 * @param array 抽出条件を格納した配列(キー=列名、値=列値)
	 * @return array SQL WHERE句となる抽出条件の配列
	 */
	public function whereExpressions($tableName, $tableAlias = null, $columns = array())
	{
		$metaColumns = $this->driver->getMetaColumns($tableName);
		$expressions = array();
		foreach ($columns as $key => $value) {
			if (!isset($value)) {
				continue;
			}
			$columnName = $key;
			$keys = explode('.', $key);
			if (count($keys) > 1) {
				if (strcmp($tableName, $keys[0]) != 0) {
					continue;
				}
				$columnName = $keys[1];
			}
			$negative = false;
			$noConvert = false;
			if (0 === strncmp($columnName, QueryBuilder::PREFIX_NEGATIVE, 1)) {
				$columnName = substr($columnName, 1);
				$negative = true;
			} elseif (0 === strncmp($columnName, QueryBuilder::PREFIX_NO_CONVERT, 1)) {
				$columnName = substr($columnName, 1);
				$noConvert = true;
			}
			if ($this->enableCamelize) {
				$columnName = $this->underscore($columnName);
			}
			if (!array_key_exists($columnName, $metaColumns)) {
				throw new \RuntimeException(
					sprintf('columnName "%s" is not defined in tableName "%s"', $columnName, $tableName)
				);
			}
			$where = null;
			if ($noConvert) {
				$where = "{$columnName} {$value}";
			} else {
				$type = $metaColumns[$columnName]['type'];
				if (isset($type)) {
					if (is_array($value)) {
						$columnValues = array();
						foreach ($value as $_val) {
							$columnValues[] = $this->builder->parameter($_val, $type);
						}
						if (count($columnValues) == 0) {
							continue;
						}
						$columnValue = join(',', $columnValues);
						$where = ($negative) ? "{$columnName} NOT IN ({$columnValue})" : "{$columnName} IN ({$columnValue})";
					} else {
						$columnValue = $this->builder->parameter($value, $type);
						if (strcmp($columnValue, 'NULL') != 0) {
							$where = ($negative) ? "{$columnName} <> {$columnValue}" : "{$columnName} = {$columnValue}";
						} else {
							$where = ($negative) ? "{$columnName} IS NOT NULL" : "{$columnName} IS NULL";
						}
					}
				}
			}
			if (isset($where)) {
				$expressions[] = (isset($tableAlias)) ? "{$tableAlias}.{$where}" : "{$tableName}.{$where}";
			}
		}
		return $expressions;
	}

	/**
	 * 抽出条件およびスキーマ情報に合わせてSQLのORDER BY句を作成します。
	 *
	 * @param string テーブル名
	 * @param array 整列順を格納した配列(キー=順序、値=列名 + 昇順/降順)
	 * @return array SQL ORDER BY句となる列名の配列
	 */
	public function orderByExpressions($tableName = null, $tableAlias = null, $orders = array())
	{
		$expressions = array();
		foreach ($orders as $order) {
			if (strlen(trim($order)) === 0) {
				continue;
			}
			$sortKey = $order;
			$desc = '';
			if (isset($tableName) && preg_match('/\A\s*([a-zA-Z0-9_]+)(?:\s+(desc|asc))?\s*\z/i',$order, $matches)) {
				$columnName = $matches[1];
				if ($this->enableCamelize) {
					$columnName = $this->underscore($columnName);
				}
				$desc = (isset($matches[2])) ? $matches[2] : '';
				$sortKey = (isset($tableAlias)) ? "{$tableAlias}.{$columnName}" : "{$tableName}.{$columnName}";
				$expressions[] = (strcmp($desc, '') != 0) ? "{$sortKey} {$desc}" : $sortKey;
			} else {
				$expressions[] = $order;
			}
		}
		return $expressions;
	}

	/**
	 * 抽出条件およびスキーマ情報に合わせてSQLのGROUP BY句を作成します。
	 *
	 * @param string テーブル名
	 * @param string テーブル別名
	 * @param array 除外列を格納した配列(値=列名)
	 * @param array 追加列を格納した配列(値=列名)
	 * @return array SQL GROUP BY句となる列名の配列
	 */
	public function groupByExpressions($tableName, $tableAlias = null, $excludeKeys = array(), $appendKeys = array())
	{
		$metaColumns = $this->driver->getMetaColumns($tableName);
		$expressions = array();
		foreach ($metaColumns as $column) {
			$columnName = ($this->enableCamelize) ? $this->camelize($column->name) : $column->name;
			if (is_array($excludeKeys) && in_array($columnName, $excludeKeys)) {
				continue;
			}
			$expressions[$column->name] = (isset($tableAlias)) ? $tableAlias . '.' . $column->name : $tableName . '.' . $column->name;
		}
		return array_merge($expressions, $appendKeys);
	}

	/**
	 * WHERE節を組み立てて返します。
	 *
	 * @param string テーブル名
	 * @param string テーブル別名
	 * @param array 抽出条件を格納した配列(キー=列名、値=列値)
	 * @return string
	 */
	public function whereSyntax($tableName, $tableAlias = null, $columns = array())
	{
		$expressions = $this->whereExpressions($tableName, $tableAlias, $columns);
		$where = (is_array($expressions) && count($expressions) > 0) ? join(" AND\n", $expressions) : '';
		return (strlen($where) >= 1) ? "WHERE\n{$where}" : '';
	}

	/**
	 * ORDER BY節を組み立てて返します。
	 *
	 * @param string テーブル名
	 * @param string テーブル別名
	 * @param array 整列順を格納した配列(キー=順序、値=列名 + 昇順/降順)
	 * @return string
	 */
	public function orderBySyntax($tableName, $tableAlias = null, $orders = array())
	{
		$expressions = $this->orderByExpressions($tableName, $tableAlias, $orders);
		$orderBy = (is_array($expressions) && count($expressions) > 0) ? join(" ,\n", $expressions) : '';
		return (strlen($orderBy) >= 1) ? "ORDER BY\n{$orderBy}" : '';
	}

	/**
	 * GROUP BY節を組み立てて返します。
	 *
	 * @param string テーブル名
	 * @param string テーブル別名
	 * @param array 除外列を格納した配列(値=列名)
	 * @param array 追加列を格納した配列(値=列名)
	 * @return string
	 */
	public function groupBySyntax($tableName, $tableAlias = null, $excludeKeys = array(), $appendKeys = array())
	{
		$expressions = $this->groupByExpressions($tableName, $tableAlias, $excludeKeys, $appendKeys);
		$groupBy = (is_array($expressions) && count($expressions) > 0) ? join(" ,\n", $expressions) : '';
		return (strlen($groupBy) >= 1) ? "GROUP BY\n{$groupBy}" : '';
	}

	/**
	 * Like演算子のパターンとして使用する文字列をエスケープして返します。
	 *
	 * @param string 抽出対象項目名
	 * @param string エスケープに使用する文字
	 * @return string エスケープされた文字列
	 */
	public function escapeLikePattern($pattern, $escapeChar = '\\')
	{
		return $this->builder->escapeLikePattern($pattern, $escapeChar);
	}

	/**
	 * @param string  $string
	 * @return string
	 */
	private function camelize($string)
	{
		return lcfirst(str_replace(' ', '', ucwords(str_replace('_', ' ', $string))));
	}

	/**
	 * @param string  $string
	 * @return string
	 */
	private function underscore($string)
	{
		return strtolower(preg_replace('/[A-Z]/', '_$0', lcfirst($string)));
	}

}
