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
 * Facadeクラス
 *
 * @author k.holy74@gmail.com
 */
class Facade
{
    /**
     * @var DriverInterface
     */
    private DriverInterface $driver;

    /**
     * @var QueryBuilderInterface
     */
    private QueryBuilderInterface $builder;

    /**
     * @var bool 取得するカラム名をキャメルケースに変換するかどうか
     */
    private bool $enableCamelize = false;

    /**
     * Facade constructor.
     *
     * @param DriverInterface $driver
     * @param QueryBuilderInterface $builder
     */
    public function __construct(DriverInterface $driver, QueryBuilderInterface $builder)
    {
        $this->driver = $driver;
        $this->builder = $builder;
    }

    /**
     * 取得するカラム名をキャメルケースに変換するかどうかを設定します。
     *
     * @param mixed $enabled 取得するカラム名をキャメルケースに変換するかどうか
     * @return void
     */
    public function enableCamelize(mixed $enabled = true): void
    {
        $this->enableCamelize = (bool)$enabled;
    }

    /**
     * データ型に合わせて項目を別名で取得するSQL句を生成します。
     *
     * @param string $expr 項目名
     * @param string|null $type データ型
     * @param string|null $alias 別名
     * @return string SQL句
     */
    public function expression(string $expr, string $type = null, string $alias = null): string
    {
        return $this->builder->expression($expr, $type, $alias);
    }

    /**
     * カラム定義に従い、SELECT句の配列を生成します。
     *
     * @param string $tableName テーブル名
     * @param string|null $tableAlias テーブル別名
     * @param array|null $excludeKeys 除外列名のリスト
     * @param array|null $columnAliases 別名取得設定 (キー=列名、値=別名)
     * @return array SQL SELECT句となる列指定の配列
     */
    public function expressions(string $tableName, string $tableAlias = null, ?array $excludeKeys = [], ?array $columnAliases = []): array
    {
        $metaColumns = $this->driver->getMetaColumns($tableName);
        $expressions = [];
        foreach ($metaColumns as $column) {
            $columnName = ($this->enableCamelize) ? $this->camelize($column->name) : $column->name;
            if (is_array($excludeKeys) && in_array($columnName, $excludeKeys)) {
                continue;
            }
            $expressions[$column->name] = $this->expression(
                isset($tableAlias) ? $tableAlias . '.' . $column->name : $tableName . '.' . $column->name,
                $column->type,
                is_array($columnAliases) && array_key_exists($columnName, $columnAliases) ? $columnAliases[$columnName] : $columnName
            );
        }
        return $expressions;
    }

    /**
     * 値を指定した型に応じたSQLパラメータ値に変換します。
     *
     * @param mixed $value データ
     * @param string $type 型名 ($typesフィールド参照)
     * @return string 変換結果
     */
    public function parameter(mixed $value, string $type): string
    {
        return $this->builder->parameter($value, $type);
    }

    /**
     * カラム定義に従い、値を変換して返します。
     * カラム定義に存在しない列名の値は無視します。
     * 列名の先頭が PREFIX_NO_CONVERT の場合は値の変換を回避します。
     *
     * @param string $tableName テーブル名
     * @param array $columns 登録内容 array(列名 => 列値, 列名 => 列値...)
     * @return array 変換済みの登録内容の配列
     */
    public function parameters(string $tableName, array $columns): array
    {
        $metaColumns = $this->driver->getMetaColumns($tableName);
        $expressions = [];
        foreach ($columns as $name => $value) {
            $columnName = $this->enableCamelize ? $this->underscore($name) : $name;
            $noConvert = false;
            if (0 === strncmp($columnName, QueryBuilder::PREFIX_NO_CONVERT, 1)) {
                $columnName = substr($columnName, 1);
                $noConvert = true;
            }
            if (!array_key_exists($columnName, $metaColumns)) {
                continue;
            }
            $expressions[$columnName] = ($noConvert)
                ? $value
                : $this->parameter($value, $metaColumns[$columnName]['type']);
        }
        return $expressions;
    }

    /**
     * INSERT文を組み立てて返します。
     *
     * @param string $tableName テーブル名
     * @param array $columns 登録内容 array(列名 => 列値, 列名 => 列値...)
     * @return string SQL
     */
    public function insert(string $tableName, array $columns): string
    {
        $parameters = $this->parameters($tableName, $columns);
        $insertColumnNames = join(', ', array_keys($parameters));
        $insertColumnValues = join(', ', array_values($parameters));
        return <<<SQL
INSERT INTO
$tableName
($insertColumnNames)
VALUES
($insertColumnValues)
SQL;
    }

    /**
     * UPDATE文を組み立てて返します。
     *
     * @param string $tableName テーブル名
     * @param array $columns 更新内容 array(列名 => 列値, 列名 => 列値...)
     * @param string|null $where WHERE句
     * @return string SQL
     */
    public function update(string $tableName, array $columns, ?string $where = null): string
    {
        $parameters = $this->parameters($tableName, $columns);
        $updateColumnSet = [];
        foreach ($parameters as $column => $value) {
            $updateColumnSet[] = sprintf('%s = %s', $column, $value);
        }
        $updateField = join(",\n", $updateColumnSet);
        $sql = <<<SQL
UPDATE
$tableName
SET
$updateField
SQL;
        return (is_string($where) && strlen($where) >= 1)
            ? $sql . "\nWHERE\n" . $where
            : $sql;
    }

    /**
     * DELETE文を組み立てて返します。
     *
     * @param string $tableName テーブル名
     * @param string|null $where WHERE句
     * @return string SQL
     */
    public function delete(string $tableName, ?string $where = null): string
    {
        $sql = <<<SQL
DELETE FROM
$tableName
SQL;
        return (is_string($where) && strlen($where) >= 1)
            ? $sql . "\nWHERE\n" . $where
            : $sql;
    }

    /**
     * SELECT節を組み立てて返します。
     *
     * @param string $tableName テーブル名
     * @param string|null $tableAlias テーブル別名
     * @param array|null $excludeKeys 除外列名のリスト
     * @param array|null $columnAliases 別名取得設定 (キー=列名、値=別名)
     * @return string SQL
     */
    public function selectSyntax(string $tableName, ?string $tableAlias = null, ?array $excludeKeys = [], ?array $columnAliases = []): string
    {
        $expressions = $this->expressions($tableName, $tableAlias, $excludeKeys, $columnAliases);
        return (count($expressions) >= 1) ? "SELECT\n" . join(",\n", $expressions) : '';
    }

    /**
     * FROM節を組み立てて返します。
     *
     * @param string $tableName テーブル名
     * @param string|null $tableAlias テーブル別名
     * @return string SQL FROM節
     */
    public function fromSyntax(string $tableName, ?string $tableAlias = null): string
    {
        return (isset($tableAlias)) ? "FROM\n{$tableName} {$tableAlias}" : "FROM\n{$tableName}";
    }

    /**
     * SELECT文を組み立てて返します。
     *
     * @param string $tableName テーブル名
     * @param string|null $tableAlias テーブル別名
     * @param string|null $where WHERE句
     * @param array|null $excludeKeys 除外列名のリスト
     * @param array|null $columnAliases 別名取得設定 (キー=列名、値=別名)
     * @return string SQL
     */
    public function select(string $tableName, string $tableAlias = null, string $where = null, ?array $excludeKeys = [], ?array $columnAliases = []): string
    {
        $sql = join("\n", [
            $this->selectSyntax($tableName, $tableAlias, $excludeKeys, $columnAliases),
            $this->fromSyntax($tableName, $tableAlias),
        ]);
        return (is_string($where) && strlen($where) >= 1)
            ? $sql . "\nWHERE\n" . $where
            : $sql;
    }

    /**
     * SELECT文を元に件数を返すクエリを生成して返します。
     *
     * @param string $sql SELECT文
     * @return string SQL
     */
    public function count(string $sql): string
    {
        return $this->builder->count($sql);
    }

    /**
     * SELECT文にLIMIT値およびOFFSET値を付与して返します。
     *
     * @param string $sql SELECT文
     * @param int|null $limit 最大取得件数
     * @param int|null $offset 取得開始行index
     * @return string SQL
     */
    public function limitOffset(string $sql, int $limit = null, int $offset = null): string
    {
        return $this->builder->limitOffset($sql, $limit, $offset);
    }

    /**
     * 抽出条件およびスキーマ情報に合わせてSQLのWHERE句を作成します。
     *
     * @param string $tableName テーブル名
     * @param string|null $tableAlias テーブル別名
     * @param array|null $columns 抽出条件を格納した配列(キー=列名、値=列値)
     * @return array SQL WHERE句となる抽出条件の配列
     */
    public function whereExpressions(string $tableName, string $tableAlias = null, ?array $columns = []): array
    {
        if (empty($columns)) {
            return [];
        }
        $metaColumns = $this->driver->getMetaColumns($tableName);
        $expressions = [];
        foreach ($columns as $key => $value) {
            if (!isset($value)) {
                continue;
            }
            $columnName = $key;
            $keys = explode('.', $key);
            if (count($keys) > 1) {
                if (strcmp($tableName, $keys[0]) != 0) {
                    continue;
                }
                $columnName = $keys[1];
            }
            $negative = false;
            $noConvert = false;
            if (0 === strncmp($columnName, QueryBuilder::PREFIX_NEGATIVE, 1)) {
                $columnName = substr($columnName, 1);
                $negative = true;
            } elseif (0 === strncmp($columnName, QueryBuilder::PREFIX_NO_CONVERT, 1)) {
                $columnName = substr($columnName, 1);
                $noConvert = true;
            }
            if ($this->enableCamelize) {
                $columnName = $this->underscore($columnName);
            }
            if (!array_key_exists($columnName, $metaColumns)) {
                throw new \RuntimeException(
                    sprintf('columnName "%s" is not defined in tableName "%s"', $columnName, $tableName)
                );
            }
            $where = null;
            if ($noConvert) {
                $where = "{$columnName} {$value}";
            } else {
                $type = $metaColumns[$columnName]['type'];
                if (isset($type)) {
                    if (is_array($value)) {
                        $columnValues = [];
                        foreach ($value as $_val) {
                            $columnValues[] = $this->builder->parameter($_val, $type);
                        }
                        if (count($columnValues) == 0) {
                            continue;
                        }
                        $columnValue = join(',', $columnValues);
                        $where = ($negative) ? "{$columnName} NOT IN ({$columnValue})" : "{$columnName} IN ({$columnValue})";
                    } else {
                        $columnValue = $this->builder->parameter($value, $type);
                        if (strcmp($columnValue, 'NULL') != 0) {
                            $where = ($negative) ? "{$columnName} <> {$columnValue}" : "{$columnName} = {$columnValue}";
                        } else {
                            $where = ($negative) ? "{$columnName} IS NOT NULL" : "{$columnName} IS NULL";
                        }
                    }
                }
            }
            if (isset($where)) {
                $expressions[] = (isset($tableAlias)) ? "{$tableAlias}.{$where}" : "{$tableName}.{$where}";
            }
        }
        return $expressions;
    }

    /**
     * 抽出条件およびスキーマ情報に合わせてSQLのORDER BY句を作成します。
     *
     * @param string|null $tableName テーブル名
     * @param string|null $tableAlias テーブル別名
     * @param array|null $orders 整列順を格納した配列(キー=順序、値=列名 + 昇順/降順)
     * @return array SQL ORDER BY句となる列名の配列
     */
    public function orderByExpressions(string $tableName = null, string $tableAlias = null, ?array $orders = []): array
    {
        if (empty($orders)) {
            return [];
        }
        $metaColumns = ($tableName !== null) ? $this->driver->getMetaColumns($tableName) : [];
        $expressions = [];
        foreach ($orders as $order) {
            if (strlen(trim($order)) === 0) {
                continue;
            }
            if (isset($tableName) && preg_match('/\A\s*([a-zA-Z0-9_]+)(?:\s+(desc|asc))?\s*\z/i', $order, $matches)) {
                $columnName = $matches[1];
                if ($this->enableCamelize) {
                    $columnName = $this->underscore($columnName);
                }
                $desc = (isset($matches[2])) ? $matches[2] : '';
                $sortKey = (isset($tableAlias)) ? $tableAlias . '.' . $columnName : $tableName . '.' . $columnName;
                if (array_key_exists($columnName, $metaColumns)) {
                    $column = $metaColumns[$columnName];
                    $sortKey = $this->expression($sortKey, $column->type, false);
                }
                $expressions[] = (strcmp($desc, '') != 0) ? $sortKey . ' ' . $desc : $sortKey;
            } else {
                $expressions[] = $order;
            }
        }
        return $expressions;
    }

    /**
     * 抽出条件およびスキーマ情報に合わせてSQLのGROUP BY句を作成します。
     *
     * @param string $tableName テーブル名
     * @param string|null $tableAlias テーブル別名
     * @param array|null $excludeKeys 除外列を格納した配列(値=列名)
     * @param array|null $appendKeys 追加列を格納した配列(値=列名)
     * @return array SQL GROUP BY句となる列名の配列
     */
    public function groupByExpressions(string $tableName, string $tableAlias = null, ?array $excludeKeys = [], ?array $appendKeys = []): array
    {
        $metaColumns = $this->driver->getMetaColumns($tableName);
        $expressions = [];
        foreach ($metaColumns as $column) {
            $columnName = ($this->enableCamelize) ? $this->camelize($column->name) : $column->name;
            if (is_array($excludeKeys) && in_array($columnName, $excludeKeys)) {
                continue;
            }
            $expressions[$column->name] = (isset($tableAlias)) ? $tableAlias . '.' . $column->name : $tableName . '.' . $column->name;
        }
        if (!empty($appendKeys)) {
            $expressions = array_merge($expressions, $appendKeys);
        }
        return $expressions;
    }

    /**
     * WHERE節を組み立てて返します。
     *
     * @param string $tableName テーブル名
     * @param string|null $tableAlias テーブル別名
     * @param array|null $columns 抽出条件を格納した配列(キー=列名、値=列値)
     * @return string
     */
    public function whereSyntax(string $tableName, string $tableAlias = null, ?array $columns = []): string
    {
        $expressions = $this->whereExpressions($tableName, $tableAlias, $columns);
        $where = (count($expressions) > 0) ? join(" AND\n", $expressions) : '';
        return (strlen($where) >= 1) ? "WHERE\n{$where}" : '';
    }

    /**
     * ORDER BY節を組み立てて返します。
     *
     * @param string $tableName テーブル名
     * @param string|null $tableAlias テーブル別名
     * @param array|null $orders 整列順を格納した配列(キー=順序、値=列名 + 昇順/降順)
     * @return string
     */
    public function orderBySyntax(string $tableName, string $tableAlias = null, ?array $orders = []): string
    {
        $expressions = $this->orderByExpressions($tableName, $tableAlias, $orders);
        $orderBy = (count($expressions) > 0) ? join(" ,\n", $expressions) : '';
        return (strlen($orderBy) >= 1) ? "ORDER BY\n{$orderBy}" : '';
    }

    /**
     * GROUP BY節を組み立てて返します。
     *
     * @param string $tableName テーブル名
     * @param string|null $tableAlias テーブル別名
     * @param array|null $excludeKeys 除外列を格納した配列(値=列名)
     * @param array|null $appendKeys 追加列を格納した配列(値=列名)
     * @return string
     */
    public function groupBySyntax(string $tableName, string $tableAlias = null, ?array $excludeKeys = [], ?array $appendKeys = []): string
    {
        $expressions = $this->groupByExpressions($tableName, $tableAlias, $excludeKeys, $appendKeys);
        $groupBy = (count($expressions) > 0) ? join(" ,\n", $expressions) : '';
        return (strlen($groupBy) >= 1) ? "GROUP BY\n{$groupBy}" : '';
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
        return $this->builder->escapeLikePattern($pattern, $escapeChar);
    }

    /**
     * @param string $string
     * @return string
     */
    private function camelize(string $string): string
    {
        return lcfirst(str_replace(' ', '', ucwords(str_replace('_', ' ', $string))));
    }

    /**
     * @param string $string
     * @return string
     */
    private function underscore(string $string): string
    {
        return strtolower(preg_replace('/[A-Z]/', '_$0', lcfirst($string)));
    }

}
