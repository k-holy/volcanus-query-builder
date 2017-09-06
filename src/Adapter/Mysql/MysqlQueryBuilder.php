<?php
/**
 * Volcanus libraries for PHP
 *
 * @copyright 2011-2013 k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */

namespace Volcanus\QueryBuilder\Adapter\Mysql;

use Volcanus\QueryBuilder\QueryBuilderInterface;
use Volcanus\QueryBuilder\AbstractQueryBuilder;

use Volcanus\QueryBuilder\QueryBuilder;

/**
 * MySQL クエリビルダ
 *
 * @author k_horii@rikcorp.jp
 */
class MysqlQueryBuilder extends AbstractQueryBuilder implements QueryBuilderInterface
{

	/**
	 * @var \Volcanus\QueryBuilder\Adapter\Mysql\MysqlExpressionBuilder
	 */
	protected $expressionBuilder;

	/**
	 * @var \Volcanus\QueryBuilder\Adapter\Mysql\MysqlParameterBuilder
	 */
	protected $parameterBuilder;

	/**
	 * @var array サポートするデータ型名
	 */
	protected static $types = array(
		'text'      => array('text', 'char', 'varchar', 'tinytext', 'longtext', 'mediumtext'),
		'int'       => array('int', 'integer', 'tinyint', 'int4', 'smallint', 'mediumint', 'bigint'),
		'float'     => array('float', 'double', 'real'),
		'bool'      => array('bool', 'boolean'),
		'date'      => array('date'),
		'time'      => array('time'),
		'timestamp' => array('timestamp', 'datetime'),
		'geometry'  => array('geometry'),
	);

	/**
	 * コンストラクタ
	 *
	 * @param \Volcanus\QueryBuilder\Adapter\Mysql\MysqlExpressionBuilder $expressionBuilder
	 * @param \Volcanus\QueryBuilder\Adapter\Mysql\MysqlParameterBuilder $parameterBuilder
	 */
	public function __construct(MysqlExpressionBuilder $expressionBuilder, MysqlParameterBuilder $parameterBuilder)
	{
		parent::setExpressionBuilder($expressionBuilder);
		parent::setParameterBuilder($parameterBuilder);
	}

	/**
	 * SELECT文にLIMIT値およびOFFSET値を付与して返します。
	 *
	 * @param string $sql SELECT文
	 * @param int $limit 最大取得件数
	 * @param int $offset 取得開始行index
	 * @return string SQL
	 */
	public function limitOffset($sql, $limit = null, $offset = null)
	{
		return sprintf('%s LIMIT %s%s',
			$sql,
			(isset($offset) && (int)$offset >= 0) ? $this->parameterBuilder->toInt($offset) . ',' : '',
			(isset($limit ) && (int)$limit >= 0) ? $this->parameterBuilder->toInt($limit) : '18446744073709551615'
		);
	}

	/**
	 * SELECT文を元に件数を返すクエリを生成して返します。
	 *
	 * @param string $sql SELECT文
	 * @return string SQL
	 */
	public function count($sql)
	{
		if (false !== strpos($sql, 'SQL_CALC_FOUND_ROWS')) {
			return 'SELECT FOUND_ROWS()';
		}
		return sprintf('SELECT COUNT(*) FROM (%s) AS __SUBQUERY', $sql);
	}

}
