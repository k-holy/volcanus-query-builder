<?php
/**
 * Volcanus libraries for PHP 8.1~
 *
 * @copyright k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */

namespace Volcanus\QueryBuilder;

use Volcanus\Database\Driver\DriverInterface;

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
     * @param DriverInterface $driver
     * @return Facade
     */
    public static function facade(DriverInterface $driver): Facade
    {
        return new Facade($driver, static::factory($driver));
    }

    /**
     * 指定されたドライバに合ったクエリビルダクラスを生成します。
     *
     * @param DriverInterface $driver
     * @return QueryBuilderInterface
     */
    public static function factory(DriverInterface $driver): QueryBuilderInterface
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
