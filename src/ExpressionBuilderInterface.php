<?php
/**
 * Volcanus libraries for PHP
 *
 * @copyright 2011-2013 k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */

namespace Volcanus\QueryBuilder;

/**
 * フィールドビルダインタフェース
 *
 * @author k.holy74@gmail.com
 */
interface ExpressionBuilderInterface
{

    /**
     * 項目名/式により値を取得するSQL句を生成します。
     *
     * @param string 項目名/式
     * @param string 別名
     * @return string 値を取得するSQL句
     */
    public function resultColumn($expr, $alias = null);

    /**
     * 日付型の項目を書式化して取得するSQL句を生成します。
     *
     * @param string 項目名
     * @return string 値を取得するSQL句
     */
    public function asDate($name);

    /**
     * 日付時刻型の項目を書式化して取得するSQL句を生成します。
     *
     * @param string 項目名
     * @return string 値を取得するSQL句
     */
    public function asTimestamp($name);

}
