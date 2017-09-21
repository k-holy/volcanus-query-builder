<?php
/**
 * Volcanus libraries for PHP
 *
 * @copyright 2011-2013 k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */

namespace Volcanus\QueryBuilder\Adapter\Sqlite;

use Volcanus\QueryBuilder\ExpressionBuilderInterface;

/**
 * SQLite Expressionビルダ
 *
 * @author k_horii@rikcorp.jp
 */
class SqliteExpressionBuilder implements ExpressionBuilderInterface
{

    /**
     * @var string 日付区切符（年月日）
     */
    private static $dateDelimiter = '-';

    /**
     * @var string 日付区切符（時分秒）
     */
    private static $timeDelimiter = ':';

    /**
     * @var string 日付区切符（年月日と時分秒）
     */
    private static $dateTimeDelimiter = ' ';

    /**
     * 項目名/式により値を取得するSQL句を生成します。
     *
     * @param string $expr 項目名/式
     * @param string $alias 別名
     * @return string 値を取得するSQL句
     */
    public function resultColumn($expr, $alias = null)
    {
        return (isset($alias)) ? $expr . ' AS "' . $alias . '"' : $expr;
    }

    /**
     * 日付型の項目を書式化して取得するSQL句を生成します。
     *
     * @param string $name 項目名
     * @return string 値を取得するSQL句
     */
    public function asDate($name)
    {
        $format = '%Y' . self::$dateDelimiter
            . '%m' . self::$dateDelimiter
            . '%d';
        return sprintf("strftime('%s', %s)", $format, $name);
    }

    /**
     * 日付時刻型の項目を書式化して取得するSQL句を生成します。
     *
     * @param string $name 項目名
     * @return string 値を取得するSQL句
     */
    public function asTimestamp($name)
    {
        $format = '%Y' . self::$dateDelimiter
            . '%m' . self::$dateDelimiter
            . '%d' . self::$dateTimeDelimiter
            . '%H' . self::$timeDelimiter
            . '%i' . self::$timeDelimiter
            . '%s';
        return sprintf("strftime('%s', %s)", $format, $name);
    }

}
