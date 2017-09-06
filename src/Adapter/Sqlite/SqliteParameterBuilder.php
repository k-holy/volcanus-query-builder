<?php
/**
 * Volcanus libraries for PHP
 *
 * @copyright 2011-2013 k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */

namespace Volcanus\QueryBuilder\Adapter\Sqlite;

use Volcanus\QueryBuilder\ParameterBuilderInterface;
use Volcanus\QueryBuilder\AbstractParameterBuilder;

use Volcanus\Database\Driver\DriverInterface;
use Volcanus\QueryBuilder\QueryBuilder;

/**
 * SQLite パラメータビルダ
 *
 * @author k_horii@rikcorp.jp
 */
class SqliteParameterBuilder extends AbstractParameterBuilder implements ParameterBuilderInterface
{

	/**
	 * @var \Volcanus\Database\Driver\DriverInterface
	 */
	private $driver;

	/**
	 * コンストラクタ
	 *
	 * @param \Volcanus\Database\Driver\DriverInterface
	 */
	public function __construct(DriverInterface $driver)
	{
		$this->driver = $driver;
	}

	/**
	 * 値を可変長/固定長文字列を表すSQLパラメータ値に変換します。
	 *
	 * @param string $value 値
	 * @return string 変換結果
	 */
	public function toText($value)
	{
		if (!isset($value)) {
			return 'NULL';
		}
		if (is_string($value) && strlen($value) === 0) {
			return 'NULL';
		}
		return $this->driver->quote($value);
	}

	/**
	 * 値を数値を表すSQLパラメータ値に変換します。
	 *
	 * @param int|float|string $value 値
	 * @param string $type 型名 ($typesフィールド参照)
	 * @return string 変換結果
	 */
	public function toInt($value, $type = null)
	{
		if (isset($type)) {
			if ($type === 'smallint' || $type === 'int2') {
				return parent::toInt2($value);
			} elseif ($type === 'bigint' || $type === 'int8') {
				return parent::toInt8($value);
			}
		}
		return parent::toInt4($value);
	}

	/**
	 * 値を浮動小数点数を表すSQLパラメータ値に変換します。
	 *
	 * @param int|float|string $value 値
	 * @param string $type 型名 ($typesフィールド参照)
	 * @return string 変換結果
	 */
	public function toFloat($value, $type = null)
	{
		if (!isset($value)) {
			return 'NULL';
		}
		if (is_int($value) || is_float($value)) {
			return (string)floatval($value);
		}
		if (is_string($value)) {
			if (strlen($value) === 0) {
				return 'NULL';
			}
			if ($value === QueryBuilder::MIN) {
					return '-9223372036854775808';
			}
			if ($value === QueryBuilder::MAX) {
					return '9223372036854775807';
			}
			return $value;
		}
		return (string)$value;
	}

	/**
	 * 値を真偽値を表すSQLパラメータ値に変換します。
	 *
	 * @param string|int $value 値
	 * @return string 変換結果
	 */
	public function toBool($value)
	{
		if (!isset($value)) {
			return 'NULL';
		}
		if ($value === QueryBuilder::MIN) {
			return '0';
		}
		if ($value === QueryBuilder::MAX) {
			return '1';
		}
		if (is_string($value)) {
			if (strlen($value) === 0) {
				return 'NULL';
			}
		}
		return sprintf('%d', (bool)$value ? 1 : 0);
	}

	/**
	 * 値を日付を表すSQLパラメータ値に変換します。
	 *
	 * @param int|\DateTime|string|array $value 値
	 * @return string 変換結果
	 */
	public function toDate($value)
	{
		if (!isset($value)) {
			return 'NULL';
		}

		// Unix Timestamp
		if (is_int($value)) {
			$value = new \DateTime(sprintf('@%d', $value));
			$value->setTimezone(new \DateTimeZone(date_default_timezone_get()));
		}

		// DateTime
		// SQLiteの日付関数は固定書式
		if ($value instanceof \DateTime) {
			return sprintf("date('%s')", $value->format('Y-m-d'));
		}

		// String of a date
		// SQLiteの日付関数は固定書式
		if (is_string($value)) {
			if (strlen($value) === 0) {
				return 'NULL';
			}
			if ($value === QueryBuilder::NOW) {
				return "date('now')";
			}
			if ($value === QueryBuilder::MIN) {
				return sprintf("date('%04d-%02d-%02d')",
					0,
					1,
					1
				);
			}
			if ($value === QueryBuilder::MAX) {
				return sprintf("date('%04d-%02d-%02d')",
					9999,
					12,
					31
				);
			}
			return sprintf("date('%s')", $value);
		}

		// array
		// SQLiteの日付関数は固定書式
		if (is_array($value)) {
			if (!isset($value[0])) {
				return 'NULL';
			}
			return sprintf("date('%04d-%02d-%02d')",
				(int)$value[0],
				(isset($value[1]) && $value[1] !== '') ? (int)$value[1] : 1,
				(isset($value[2]) && $value[2] !== '') ? (int)$value[2] : 1
			);
		}

		throw new \InvalidArgumentException(
			sprintf('The value is invalid toDate(), type:%s',
				(is_object($value)) ? get_class($value) : gettype($value)
			)
		);
	}

	/**
	 * 値を日時を表すSQLパラメータ値に変換します。
	 *
     * @param int|\DateTime|string|array $value 値
	 * @return string 変換結果
	 */
	public function toTimestamp($value)
	{
		if (!isset($value)) {
			return 'NULL';
		}

		// Unix Timestamp
		if (is_int($value)) {
			$value = new \DateTime(sprintf('@%d', $value));
			$value->setTimezone(new \DateTimeZone(date_default_timezone_get()));
		}

		// DateTime
		// SQLiteの日付関数は固定書式
		if ($value instanceof \DateTime) {
			return sprintf("datetime('%s')", $value->format('Y-m-d H:i:s'));
		}

		// Datetime string
		// SQLiteの日付関数は固定書式
		if (is_string($value)) {
			if (strlen($value) === 0) {
				return 'NULL';
			}
			if ($value === QueryBuilder::NOW) {
				return "datetime('now')";
			}
			if ($value === QueryBuilder::MIN) {
				return "datetime('0000-01-01 00:00:00')";
			}
			if ($value === QueryBuilder::MAX) {
				return "datetime('9999-12-31 23:59:59')";
			}
			return "datetime('{$value}')";
		}

		// array
		// SQLiteの日付関数は固定書式
		if (is_array($value)) {
			if (!isset($value[0])) {
				return 'NULL';
			}
			return sprintf("datetime('%04d-%02d-%02d %02d:%02d:%02d')",
				(int)$value[0],
				(isset($value[1]) && $value[1] !== '') ? (int)$value[1] : 1,
				(isset($value[2]) && $value[2] !== '') ? (int)$value[2] : 1,
				(isset($value[3]) && $value[3] !== '') ? (int)$value[3] : 0,
				(isset($value[4]) && $value[4] !== '') ? (int)$value[4] : 0,
				(isset($value[5]) && $value[5] !== '') ? (int)$value[5] : 0
			);
		}

		throw new \InvalidArgumentException(
			sprintf('The value is invalid toTimestamp(), type:%s',
				(is_object($value)) ? get_class($value) : gettype($value)
			)
		);
	}

}
