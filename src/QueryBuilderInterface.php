<?php
/**
 * Volcanus libraries for PHP 8.1~
 *
 * @copyright k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */

namespace Volcanus\QueryBuilder;

/**
 * クエリビルダインタフェース
 *
 * @author k.holy74@gmail.com
 */
interface QueryBuilderInterface
{

    /**
     * 値を指定した型に応じたSQLパラメータ値に変換します。
     *
     * @param mixed $value データ
     * @param string $type 型名 ($typesフィールド参照)
     * @return string 変換結果
     */
    public function parameter(mixed $value, string $type): string;

    /**
     * SELECT文にLIMIT値およびOFFSET値を付与して返します。
     *
     * @param string $sql SELECT文
     * @param int|null $limit 最大取得件数
     * @param int|null $offset 取得開始行index
     * @return string SQL
     */
    public function limitOffset(string $sql, int $limit = null, int $offset = null): string;

    /**
     * SELECT文を元に件数を返すクエリを生成して返します。
     *
     * @param string $sql SELECT文
     * @return string SQL
     */
    public function count(string $sql): string;

    /**
     * データ型に合わせて項目を別名で取得するSQL句を生成します。
     *
     * @param string $expr 項目名
     * @param string|null $type データ型
     * @param string|null $alias 別名
     * @return string SQL句
     */
    public function expression(string $expr, string $type = null, string $alias = null): string;

    /**
     * Like演算子のパターンとして使用する文字列をエスケープして返します。
     *
     * @param string $pattern 抽出対象項目名
     * @param string $escapeChar エスケープに使用する文字
     * @return string エスケープされた文字列
     */
    public function escapeLikePattern(string $pattern, string $escapeChar = '\\'): string;

}
