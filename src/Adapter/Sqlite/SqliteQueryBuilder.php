<?php
/**
 * Volcanus libraries for PHP
 *
 * @copyright 2011-2013 k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */

namespace Volcanus\QueryBuilder\Adapter\Sqlite;

use Volcanus\QueryBuilder\QueryBuilderInterface;
use Volcanus\QueryBuilder\AbstractQueryBuilder;

use Volcanus\QueryBuilder\QueryBuilder;

/**
 * SQLite クエリビルダ
 *
 * @author k_horii@rikcorp.jp
 */
class SqliteQueryBuilder extends AbstractQueryBuilder implements QueryBuilderInterface
{

	/**
	 * @var Volcanus\QueryBuilder\Adapter\Sqlite\SqliteExpressionBuilder
	 */
	protected $expressionBuilder;

	/**
	 * @var Volcanus\QueryBuilder\Adapter\Sqlite\SqliteParameterBuilder
	 */
	protected $parameterBuilder;

	/**
	 * @var array サポートするデータ型名
	 */
	protected static $types = array(
		'text'      => array('character', 'varchar', 'varying character', 'nchar', 'native character', 'nvarchar', 'text', 'clob'),
		'int'       => array('int', 'integer', 'tinyint', 'smallint', 'mediumint', 'bigint', 'int2', 'int8'),
		'float'     => array('real', 'double', 'double precision', 'float'),
		'bool'      => array('boolean'),
		'date'      => array('date'),
		'timestamp' => array('datetime'),
	);

	/**
	 * コンストラクタ
	 *
	 * @param Volcanus\QueryBuilder\Adapter\Sqlite\SqliteExpressionBuilder
	 * @param Volcanus\QueryBuilder\Adapter\Sqlite\SqliteParameterBuilder
	 */
	public function __construct(SqliteExpressionBuilder $expressionBuilder, SqliteParameterBuilder $parameterBuilder)
	{
		parent::setExpressionBuilder($expressionBuilder);
		parent::setParameterBuilder($parameterBuilder);
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
		$sql .= sprintf(' LIMIT %s',
			(isset($limit) && (int)$limit >= 0)
				? $this->parameterBuilder->toInt($limit)
				: '18446744073709551615'
		);
		if (isset($offset) && (int)$offset >= 0) {
			$sql .= sprintf(' OFFSET %s',
				$this->parameterBuilder->toInt($offset)
			);
		}
		return $sql;
	}

	/**
	 * SELECT文を元に件数を返すクエリを生成して返します。
	 *
	 * @param string SELECT文
	 * @return string SQL
	 */
	public function count($sql)
	{
		return sprintf('SELECT COUNT(*) FROM (%s) AS __SUBQUERY', $sql);
	}

}