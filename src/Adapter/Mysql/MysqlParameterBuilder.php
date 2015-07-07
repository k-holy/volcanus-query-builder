<?php
/**
 * Volcanus libraries for PHP
 *
 * @copyright 2011-2013 k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */

namespace Volcanus\QueryBuilder\Adapter\Mysql;

use Volcanus\QueryBuilder\ParameterBuilderInterface;
use Volcanus\QueryBuilder\AbstractParameterBuilder;

use Volcanus\Database\Driver\DriverInterface;
use Volcanus\QueryBuilder\QueryBuilder;

/**
 * MySQL パラメータビルダ
 *
 * @author k_horii@rikcorp.jp
 */
class MysqlParameterBuilder extends AbstractParameterBuilder implements ParameterBuilderInterface
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
	 * @var \Volcanus\Database\Driver\DriverInterface
	 */
	protected $driver;

	/**
	 * コンストラクタ
	 *
	 * @param \Volcanus\Database\Driver\DriverInterface
	 * @param array 設定
	 */
	public function __construct(DriverInterface $driver)
	{
		$this->driver = $driver;
	}

	/**
	 * 値を可変長/固定長文字列を表すSQLパラメータ値に変換します。
	 *
	 * @param mixed 値
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
	 * @param mixed 値
	 * @param string 型名 ($typesフィールド参照)
	 * @return string 変換結果
	 */
	public function toInt($value, $type = null)
	{
		if (isset($type)) {
			if ($type === 'tinyint') {
				return $this->toTinyInt($value);
			} elseif ($type === 'smallint') {
				return $this->toSmallInt($value);
			} elseif ($type === 'mediumint') {
				return $this->toMediumInt($value);
			} elseif ($type === 'bigint') {
				return $this->toBigInt($value);
			}
		}
		return parent::toInt4($value);
	}

	/**
	 * 値を浮動小数点数を表すSQLパラメータ値に変換します。
	 *
	 * @param mixed 値
	 * @param string 型名 ($typesフィールド参照)
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
				return "'-3.402823466E+38'";
			}
			if ($value === QueryBuilder::MAX) {
				return "'3.402823466E+38'";
			}
			return $value;
		}
		return (string)$value;
	}

	/**
	 * 値を真偽値を表すSQLパラメータ値に変換します。
	 *
	 * @param mixed 値
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
	 * @param mixed 値 int | DateTime | string | array
	 * @return string 変換結果
	 */
	public function toDate($value)
	{
		if (!isset($value)) {
			return 'NULL';
		}

		$format = '%Y' . self::$dateDelimiter
				. '%m' . self::$dateDelimiter
				. '%d';

		// Unix Timestamp
		if (is_int($value)) {
			$value = new \DateTime(sprintf('@%d', $value));
			$value->setTimezone(new \DateTimeZone(date_default_timezone_get()));
		}

		// DateTime
		if ($value instanceof \DateTime) {
			return sprintf("STR_TO_DATE('%s', '%s')",
				$value->format(sprintf('Y%sm%sd',
					self::$dateDelimiter,
					self::$dateDelimiter
				)),
				$format
			);
		}

		// String of a date
		if (is_string($value)) {
			if (strlen($value) === 0) {
				return 'NULL';
			}
			if ($value === QueryBuilder::NOW) {
				return 'CURDATE()';
			}
			if ($value === QueryBuilder::MIN) {
				return sprintf("STR_TO_DATE('%04d%s%02d%s%02d', '%s')",
					1000,
					self::$dateDelimiter,
					1,
					self::$dateDelimiter,
					1,
					$format
				);
			}
			if ($value === QueryBuilder::MAX) {
				return sprintf("STR_TO_DATE('%04d%s%02d%s%02d', '%s')",
					9999,
					self::$dateDelimiter,
					12,
					self::$dateDelimiter,
					31,
					$format
				);
			}
			return sprintf("STR_TO_DATE('%s', '%s')", $value, $format);
		}

		// array
		if (is_array($value)) {
			if (!isset($value[0])) {
				return 'NULL';
			}
			return sprintf("STR_TO_DATE('%04d%s%02d%s%02d', '%s')",
				(int)$value[0],
				self::$dateDelimiter,
				(isset($value[1]) && $value[1] !== '') ? (int)$value[1] : 1,
				self::$dateDelimiter,
				(isset($value[2]) && $value[2] !== '') ? (int)$value[2] : 1,
				$format
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
	 * @param mixed 値 int | DateTime | string | array
	 * @return string 変換結果
	 */
	public function toTimestamp($value)
	{
		if (!isset($value)) {
			return 'NULL';
		}

		$format = '%Y' . self::$dateDelimiter
				. '%m' . self::$dateDelimiter
				. '%d' . self::$dateTimeDelimiter
				. '%H' . self::$timeDelimiter
				. '%i' . self::$timeDelimiter
				. '%s';

		// Unix Timestamp
		if (is_int($value)) {
			$value = new \DateTime(sprintf('@%d', $value));
			$value->setTimezone(new \DateTimeZone(date_default_timezone_get()));
		}

		if ($value instanceof \DateTime) {
			return sprintf("STR_TO_DATE('%s', '%s')",
				$value->format(sprintf('Y%sm%sd%sH%si%ss',
					self::$dateDelimiter,
					self::$dateDelimiter,
					self::$dateTimeDelimiter,
					self::$timeDelimiter,
					self::$timeDelimiter
				)),
				$format
			);
		}

		if (is_string($value)) {
			if (strlen($value) === 0) {
				return 'NULL';
			}
			if ($value === QueryBuilder::NOW) {
				return 'NOW()';
			}
			if ($value === QueryBuilder::MIN) {
				return sprintf("STR_TO_DATE('%04d%s%02d%s%02d%s%02d%s%02d%s%02d', '%s')",
					1000,
					self::$dateDelimiter,
					1,
					self::$dateDelimiter,
					1,
					self::$dateTimeDelimiter,
					0,
					self::$timeDelimiter,
					0,
					self::$timeDelimiter,
					0,
					$format
				);
			}
			if ($value === QueryBuilder::MAX) {
				return sprintf("STR_TO_DATE('%04d%s%02d%s%02d%s%02d%s%02d%s%02d', '%s')",
					9999,
					self::$dateDelimiter,
					12,
					self::$dateDelimiter,
					31,
					self::$dateTimeDelimiter,
					23,
					self::$timeDelimiter,
					59,
					self::$timeDelimiter,
					59,
					$format
				);
			}
			return sprintf("STR_TO_DATE('%s', '%s')", $value, $format);
		}

		// array
		if (is_array($value)) {
			if (!isset($value[0])) {
				return 'NULL';
			}
			return sprintf("STR_TO_DATE('%04d%s%02d%s%02d%s%02d%s%02d%s%02d', '%s')",
				(int)$value[0],
				self::$dateDelimiter,
				(isset($value[1]) && $value[1] !== '') ? (int)$value[1] : 1,
				self::$dateDelimiter,
				(isset($value[2]) && $value[2] !== '') ? (int)$value[2] : 1,
				self::$dateTimeDelimiter,
				(isset($value[3]) && $value[3] !== '') ? (int)$value[3] : 0,
				self::$timeDelimiter,
				(isset($value[4]) && $value[4] !== '') ? (int)$value[4] : 0,
				self::$timeDelimiter,
				(isset($value[5]) && $value[5] !== '') ? (int)$value[5] : 0,
				$format
			);
		}

		throw new \InvalidArgumentException(
			sprintf('The value is invalid toTimestamp(), type:%s',
				(is_object($value)) ? get_class($value) : gettype($value)
			)
		);
	}

	/**
	 * 値を時刻を表すSQLパラメータ値に変換します。
	 *
	 * @param mixed 値 int | DateTime | string | array
	 * @return string 変換結果
	 */
	public function toTime($value)
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
		if ($value instanceof \DateTime) {
			return sprintf("'%s'", $value->format(sprintf('H%si%ss',
				self::$timeDelimiter,
				self::$timeDelimiter
			)));
		}

		// String of a time
		if (is_string($value)) {
			if (strlen($value) === 0) {
				return 'NULL';
			}
			if ($value === QueryBuilder::NOW) {
				return 'TIME(NOW())';
			}
			if ($value === QueryBuilder::MIN) {
				return "'00:00:00'";
			}
			if ($value === QueryBuilder::MAX) {
				return "'23:59:59'";
			}
			return sprintf("'%s'", $value);
		}

		// array
		if (is_array($value)) {
			if (!isset($value[0])) {
				return 'NULL';
			}
			return sprintf("'%02d%s%02d%s%02d'",
				(int)$value[0],
				self::$timeDelimiter,
				(isset($value[1]) && $value[1] !== '') ? (int)$value[1] : 0,
				self::$timeDelimiter,
				(isset($value[2]) && $value[2] !== '') ? (int)$value[2] : 0
			);
		}

		throw new \InvalidArgumentException(
			sprintf('The value is invalid toTime(), type:%s',
				(is_object($value)) ? get_class($value) : gettype($value)
			)
		);
	}

	/**
	 * 値をTINYINT型を表すSQLパラメータ値に変換します。
	 *
	 * @param mixed 値
	 * @return string 変換結果
	 */
	public function toTinyInt($value)
	{
		return parent::toInt1($value);
	}

	/**
	 * 値をSMALLINT型を表すSQLパラメータ値に変換します。
	 *
	 * @param mixed 値
	 * @return string 変換結果
	 */
	public function toSmallInt($value)
	{
		return parent::toInt2($value);
	}

	/**
	 * 値をMEDIUMINT型を表すSQLパラメータ値に変換します。
	 *
	 * @param mixed 値
	 * @return string 変換結果
	 */
	public function toMediumInt($value)
	{
		return parent::toInt3($value);
	}

	/**
	 * 値をBIGINT型を表すSQLパラメータ値に変換します。
	 *
	 * @param mixed 値
	 * @return string 変換結果
	 */
	public function toBigInt($value)
	{
		return parent::toInt8($value);
	}

}
