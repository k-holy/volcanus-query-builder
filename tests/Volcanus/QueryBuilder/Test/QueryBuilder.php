<?php
/**
 * Volcanus libraries for PHP
 *
 * @copyright 2011-2013 k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */

namespace Volcanus\QueryBuilder\Test;

use Volcanus\QueryBuilder\QueryBuilderInterface;
use Volcanus\QueryBuilder\AbstractQueryBuilder;
use Volcanus\QueryBuilder\ExpressionBuilderInterface;
use Volcanus\QueryBuilder\ParameterBuilderInterface;

class QueryBuilder extends AbstractQueryBuilder implements QueryBuilderInterface
{
    protected static $types = array(
        'text' => array('char', 'varchar', 'text'),
        'int' => array('int', 'integer'),
        'float' => array('float', 'real'),
        'bool' => array('bool', 'boolean'),
        'date' => array('date'),
        'timestamp' => array('timestamp', 'datetime'),
    );

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
