<?php
/**
 * Volcanus libraries for PHP 8.1~
 *
 * @copyright k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */

namespace Volcanus\QueryBuilder\Adapter\Mysql;

use Volcanus\QueryBuilder\ExpressionBuilderInterface;

/**
 * MySQL Expressionビルダ
 *
 * @author k_horii@rikcorp.jp
 */
class MysqlExpressionBuilder implements ExpressionBuilderInterface
{

    /**
     * @var string 日付区切符（年月日）
     */
    private static string $dateDelimiter = '-';

    /**
     * @var string 日付区切符（時分秒）
     */
    private static string $timeDelimiter = ':';

    /**
     * @var string 日付区切符（年月日と時分秒）
     */
    private static string $dateTimeDelimiter = ' ';

    /**
     * 項目名/式により値を取得するSQL句を生成します。
     *
     * @param string $expr 項目名/式
     * @param string|null $alias 別名
     * @return string 値を取得するSQL句
     */
    public function resultColumn(string $expr, string $alias = null): string
    {
        return (is_string($alias) && strlen($alias) >= 1) ? $expr . ' AS `' . $alias . '`' : $expr;
    }

    /**
     * 日付型の項目を書式化して取得するSQL句を生成します。
     *
     * @param string $name 項目名
     * @return string 値を取得するSQL句
     */
    public function asDate(string $name): string
    {
        $format = '%Y' . self::$dateDelimiter
            . '%m' . self::$dateDelimiter
            . '%d';
        return sprintf("DATE_FORMAT(%s, '%s')", $name, $format);
    }

    /**
     * 日付時刻型の項目を書式化して取得するSQL句を生成します。
     *
     * @param string $name 項目名
     * @return string 値を取得するSQL句
     */
    public function asTimestamp(string $name): string
    {
        $format = '%Y' . self::$dateDelimiter
            . '%m' . self::$dateDelimiter
            . '%d' . self::$dateTimeDelimiter
            . '%H' . self::$timeDelimiter
            . '%i' . self::$timeDelimiter
            . '%s';
        return sprintf("DATE_FORMAT(%s, '%s')", $name, $format);
    }

    /**
     * GEOMETRY型の項目をテキストとして取得するSQL句を生成します。
     *
     * @param string $name 項目名
     * @return string 値を取得するSQL句
     */
    public function asGeometry(string $name): string
    {
        return sprintf('ST_AsText(%s)', $name);
    }

}
