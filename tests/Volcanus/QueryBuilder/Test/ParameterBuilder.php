<?php
/**
 * Volcanus libraries for PHP 8.1~
 *
 * @copyright k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */

namespace Volcanus\QueryBuilder\Test;

use Volcanus\QueryBuilder\ParameterBuilderInterface;
use Volcanus\QueryBuilder\AbstractParameterBuilder;

class ParameterBuilder extends AbstractParameterBuilder implements ParameterBuilderInterface
{

    public function toText(mixed $value): string
    {
        return sprintf("'%s'", $value);
    }

    public function toInt(mixed $value, string $type = null): string
    {
        return sprintf('%d', $value);
    }

    public function toFloat(mixed $value, string $type = null): string
    {
        return (string)$value;
    }

    public function toBool(mixed $value): string
    {
        return ((bool)$value === true) ? '1' : '0';
    }

    public function toDate(mixed $value): string
    {
        return sprintf("TO_DATE('%s')", $value);
    }

    public function toTimestamp(mixed $value): string
    {
        return sprintf("TO_TIMESTAMP('%s')", $value);
    }

}
