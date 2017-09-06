<?php
/**
 * Volcanus libraries for PHP
 *
 * @copyright k-holy <k.holy74@gmail.com>
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
     * @var \Volcanus\QueryBuilder\Adapter\Sqlite\SqliteExpressionBuilder
     */
    protected $expressionBuilder;

    /**
     * @var \Volcanus\QueryBuilder\Adapter\Sqlite\SqliteParameterBuilder
     */
    protected $parameterBuilder;

    /**
     * @var array サポートするデータ型名
     */
    protected static $types = [
        'text' => [
            'character',
            'varchar',
            'varying character',
            'nchar',
            'native character',
            'nvarchar',
            'text',
            'clob',
        ],
        'int' => ['int', 'integer', 'tinyint', 'smallint', 'mediumint', 'bigint', 'int2', 'int8'],
        'float' => ['real', 'double', 'double precision', 'float'],
        'bool' => ['boolean'],
        'date' => ['date'],
        'timestamp' => ['datetime'],
    ];

    /**
     * コンストラクタ
     *
     * @param \Volcanus\QueryBuilder\Adapter\Sqlite\SqliteExpressionBuilder $expressionBuilder
     * @param \Volcanus\QueryBuilder\Adapter\Sqlite\SqliteParameterBuilder $parameterBuilder
     */
    public function __construct(SqliteExpressionBuilder $expressionBuilder, SqliteParameterBuilder $parameterBuilder)
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
     * @param string $sql SELECT文
     * @return string SQL
     */
    public function count($sql)
    {
        return sprintf('SELECT COUNT(*) FROM (%s) AS __SUBQUERY', $sql);
    }

}
