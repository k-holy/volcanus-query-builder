<?php
/**
 * Volcanus libraries for PHP 8.1~
 *
 * @copyright k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */

namespace Volcanus\QueryBuilder\Test;

use Volcanus\QueryBuilder\ExpressionBuilderInterface;

class ExpressionBuilder implements ExpressionBuilderInterface
{

    public function resultColumn(string $expr, string $alias = null): string
    {
        return (isset($alias)) ? $expr . ' AS "' . $alias . '"' : $expr;
    }

    public function asDate(string $name): string
    {
        return sprintf("TO_CHAR(%s, 'YYYY-MM-DD')", $name);
    }

    public function asTimestamp(string $name): string
    {
        return sprintf("TO_CHAR(%s, 'YYYY-MM-DD HH24:MI:SS')", $name);
    }

}
