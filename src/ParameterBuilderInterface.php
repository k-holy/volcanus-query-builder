<?php
/**
 * Volcanus libraries for PHP
 *
 * @copyright k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */

namespace Volcanus\QueryBuilder;

/**
 * パラメータビルダインタフェース
 *
 * @author k.holy74@gmail.com
 */
interface ParameterBuilderInterface
{

    /**
     * 値を可変長/固定長文字列を表すSQLパラメータ値に変換します。
     *
     * @param mixed $value 値
     * @return string 変換結果
     */
    public function toText($value): string;

    /**
     * 値を数値を表すSQLパラメータ値に変換します。
     *
     * @param mixed $value 値
     * @param string|null $type 型名 ($typesフィールド参照)
     * @return string 変換結果
     */
    public function toInt($value, ?string $type = null): string;

    /**
     * 値を浮動小数点数を表すSQLパラメータ値に変換します。
     *
     * @param mixed $value 値
     * @param string|null $type 型名 ($typesフィールド参照)
     * @return string 変換結果
     */
    public function toFloat($value, ?string $type = null): string;

    /**
     * 値を真偽値を表すSQLパラメータ値に変換します。
     *
     * @param mixed $value 値
     * @return string 変換結果
     */
    public function toBool($value): string;

    /**
     * 値を日付を表すSQLパラメータ値に変換します。
     *
     * @param mixed $value 値
     * @return string 変換結果
     */
    public function toDate($value): string;

    /**
     * 値を日時を表すSQLパラメータ値に変換します。
     *
     * @param mixed $value 値
     * @return string 変換結果
     */
    public function toTimestamp($value): string;

}
