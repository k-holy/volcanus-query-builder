<?php
/**
 * Volcanus libraries for PHP
 *
 * @copyright 2011-2013 k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */

namespace Volcanus\QueryBuilder;

use Volcanus\Database\Driver\DriverInterface;
use Volcanus\QueryBuilder\QueryBuilderInterface;
use Volcanus\QueryBuilder\Facade;

/**
 * クエリビルダ
 *
 * @author k.holy74@gmail.com
 */
class QueryBuilder
{
    const NOW = 'NOW';
    const MAX = 'MAX';
    const MIN = 'MIN';

    const PREFIX_NEGATIVE = '!';
    const PREFIX_NO_CONVERT = '#';

    /**
     * ファサードクラスを生成します。
     *
     * @param \Volcanus\Database\Driver\DriverInterface
     * @return \Volcanus\QueryBuilder\Facade
     */
    public static function facade(DriverInterface $driver)
    {
        return new Facade($driver, static::factory($driver));
    }

    /**
     * 指定されたドライバに合ったクエリビルダクラスを生成します。
     *
     * @param \Volcanus\Database\Driver\DriverInterface
     * @return \Volcanus\QueryBuilder\QueryBuilderInterface
     */
    public static function factory(DriverInterface $driver)
    {
        $driverName = $driver->getDriverName();
        if (!isset($driverName)) {
            throw new \RuntimeException('Could not create QueryBuilder, unknown driverName.');
        }
        $driverName = ucfirst($driverName);
        $namespace = sprintf('\\Volcanus\\QueryBuilder\\Adapter\\%s', $driverName);
        $queryBuilderClass = sprintf('%s\\%sQueryBuilder', $namespace, $driverName);
        $expressionBuilderClass = sprintf('%s\\%sExpressionBuilder', $namespace, $driverName);
        $parameterBuilderClass = sprintf('%s\\%sParameterBuilder', $namespace, $driverName);
        return new $queryBuilderClass(
            new $expressionBuilderClass(),
            new $parameterBuilderClass($driver)
        );
    }

}
