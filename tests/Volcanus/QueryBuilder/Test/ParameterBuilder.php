<?php
/**
 * Volcanus libraries for PHP
 *
 * @copyright 2011-2013 k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */

namespace Volcanus\QueryBuilder\Test;

use Volcanus\QueryBuilder\ParameterBuilderInterface;
use Volcanus\QueryBuilder\AbstractParameterBuilder;

class ParameterBuilder extends AbstractParameterBuilder implements ParameterBuilderInterface
{

    public function toText($value)
    {
        return sprintf("'%s'", $value);
    }

    public function toInt($value, $type = null)
    {
        return sprintf('%d', $value);
    }

    public function toFloat($value, $type = null)
    {
        return (string)$value;
    }

    public function toBool($value)
    {
        return ((bool)$value === true) ? '1' : '0';
    }

    public function toDate($value)
    {
        return sprintf("TO_DATE('%s')", $value);
    }

    public function toTimestamp($value)
    {
        return sprintf("TO_TIMESTAMP('%s')", $value);
    }

}
