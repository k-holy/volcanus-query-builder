<?php
/**
 * Volcanus libraries for PHP
 *
 * @copyright k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */

namespace Volcanus\QueryBuilder\Test;

use Volcanus\QueryBuilder\ParameterBuilderInterface;
use Volcanus\QueryBuilder\AbstractParameterBuilder;

class ParameterBuilder extends AbstractParameterBuilder implements ParameterBuilderInterface
{

    public function toText($value): string
    {
        return sprintf("'%s'", $value);
    }

    public function toInt($value, string $type = null): string
    {
        return sprintf('%d', $value);
    }

    public function toFloat($value, string $type = null): string
    {
        return (string)$value;
    }

    public function toBool($value): string
    {
        return ((bool)$value === true) ? '1' : '0';
    }

    public function toDate($value): string
    {
        return sprintf("TO_DATE('%s')", $value);
    }

    public function toTimestamp($value): string
    {
        return sprintf("TO_TIMESTAMP('%s')", $value);
    }

}
