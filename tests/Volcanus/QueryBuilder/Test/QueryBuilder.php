<?php
/**
 * Volcanus libraries for PHP
 *
 * @copyright k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */

namespace Volcanus\QueryBuilder\Test;

use Volcanus\QueryBuilder\QueryBuilderInterface;
use Volcanus\QueryBuilder\AbstractQueryBuilder;
use Volcanus\QueryBuilder\ExpressionBuilderInterface;
use Volcanus\QueryBuilder\ParameterBuilderInterface;

class QueryBuilder extends AbstractQueryBuilder implements QueryBuilderInterface
{
    protected static $types = [
        'text' => ['char', 'varchar', 'text'],
        'int' => ['int', 'integer'],
        'float' => ['float', 'real'],
        'bool' => ['bool', 'boolean'],
        'date' => ['date'],
        'timestamp' => ['timestamp', 'datetime'],
    ];

    public function __construct(ExpressionBuilderInterface $expressionBuilder, ParameterBuilderInterface $parameterBuilder)
    {
        $this->setExpressionBuilder($expressionBuilder);
        $this->setParameterBuilder($parameterBuilder);
    }

    public function limitOffset($sql, $limit = null, $offset = null)
    {
        return sprintf("%s LIMIT %d OFFSET %d",
            $sql,
            $this->parameterBuilder->toInt(!is_int($limit) ? 50 : $limit),
            $this->parameterBuilder->toInt(!is_int($offset) ? 0 : $offset)
        );
    }

    public function count($sql)
    {
        return sprintf("SELECT COUNT(*) FROM (%s) AS X", $sql);
    }

}
