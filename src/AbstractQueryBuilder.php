<?php
/**
 * Volcanus libraries for PHP
 *
 * @copyright k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */

namespace Volcanus\QueryBuilder;

/**
 * クエリビルダ抽象クラス
 *
 * @author k_horii@rikcorp.jp
 */
abstract class AbstractQueryBuilder implements QueryBuilderInterface
{

    /**
     * @var ExpressionBuilderInterface
     */
    protected $expressionBuilder;

    /**
     * @var ParameterBuilderInterface
     */
    protected $parameterBuilder;

    /**
     * @var array サポートするデータ型名
     */
    protected static $types = [];

    /**
     * @param ExpressionBuilderInterface
     */
    protected function setExpressionBuilder(ExpressionBuilderInterface $expressionBuilder)
    {
        $this->expressionBuilder = $expressionBuilder;
    }

    /**
     * @param ParameterBuilderInterface
     */
    protected function setParameterBuilder(ParameterBuilderInterface $parameterBuilder)
    {
        $this->parameterBuilder = $parameterBuilder;
    }

    /**
     * 型名から、SQLパラメータ用の型名を返します。
     *
     * @param string $type 型名 ($typesフィールド参照)
     * @return string SQLパラメータ用の型名
     */
    public function parameterType(string $type)
    {
        $type = strtolower($type);
        foreach (static::$types as $parameterType => $parameterTypes) {
            if ($type === $parameterType || in_array($type, $parameterTypes)) {
                return $parameterType;
            }
        }
        return false;
    }

    /**
     * 値を指定した型に応じたSQLパラメータ値に変換します。
     *
     * @param mixed $value データ
     * @param string $type 型名 ($typesフィールド参照)
     * @return string 変換結果
     */
    public function parameter($value, string $type): string
    {
        $sqlType = $this->parameterType($type);
        if (!$sqlType) {
            throw new \InvalidArgumentException(
                sprintf('Unsupported type:"%s"', $type)
            );
        }
        $methodName = 'to' . ucfirst($sqlType);
        if (!method_exists($this->parameterBuilder, $methodName)) {
            throw new \InvalidArgumentException(
                sprintf('Method not exists, Unsupported type:"%s"', $type)
            );
        }
        return $this->parameterBuilder->$methodName($value, $type);
    }

    /**
     * データ型に合わせて項目を別名で取得するSQL句を生成します。
     *
     * @param string $expr 項目名
     * @param string|null $type データ型
     * @param string|null $alias 別名
     * @return string SQL句
     */
    public function expression(string $expr, ?string $type = null, ?string $alias = null): string
    {
        if (isset($type)) {
            $sqlType = $this->parameterType($type);
            if (!$sqlType) {
                throw new \InvalidArgumentException(
                    sprintf('Unsupported type:"%s"', $type)
                );
            }
            $methodName = 'as' . ucfirst($sqlType);
            if (method_exists($this->expressionBuilder, $methodName)) {
                if (!isset($alias)) {
                    $alias = $expr;
                }
                $expr = $this->expressionBuilder->$methodName($expr, $type);
            }
        }
        return $this->expressionBuilder->resultColumn($expr, $alias);
    }

    /**
     * Like演算子のパターンとして使用する文字列をエスケープして返します。
     *
     * @param string $pattern 抽出対象項目名
     * @param string $escapeChar エスケープに使用する文字
     * @return string エスケープされた文字列
     */
    public function escapeLikePattern(string $pattern, string $escapeChar = '\\'): string
    {
        $transTable = [
            '_' => "{$escapeChar}_",
            '%' => "{$escapeChar}%",
            "{$escapeChar}" => "{$escapeChar}{$escapeChar}",
        ];
        return strtr($pattern, $transTable);
    }

}
