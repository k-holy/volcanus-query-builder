<?php
/**
 * Volcanus libraries for PHP
 *
 * @copyright 2011-2013 k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */

namespace Volcanus\QueryBuilder;

use Volcanus\QueryBuilder\ParameterBuilderInterface;

/**
 * パラメータビルダ抽象クラス
 *
 * @author k_horii@rikcorp.jp
 */
abstract class AbstractParameterBuilder implements ParameterBuilderInterface
{

	/**
	 * 値を1ビットの数値を表すSQLパラメータ値に変換します。
	 *
	 * @param int|float|string $value 値
	 * @return string 変換結果
	 */
	public function toInt1($value)
	{
		if (!isset($value)) {
			return 'NULL';
		}
		if (is_int($value) || is_float($value)) {
			return sprintf('%d', $value);
		}
		if (is_string($value)) {
			if (strlen($value) === 0) {
				return 'NULL';
			}
			if ($value === QueryBuilder::MIN) {
				return '-128';
			}
			if ($value === QueryBuilder::MAX) {
				return '127';
			}
			return $value;
		}
		return (string)$value;
	}

	/**
	 * 値を2ビットの数値を表すSQLパラメータ値に変換します。
	 *
	 * @param int|float|string $value 値
	 * @return string 変換結果
	 */
	public function toInt2($value)
	{
		if (!isset($value)) {
			return 'NULL';
		}
		if (is_int($value) || is_float($value)) {
			return sprintf('%d', $value);
		}
		if (is_string($value)) {
			if (strlen($value) === 0) {
				return 'NULL';
			}
			if ($value === QueryBuilder::MIN) {
				return '-32768';
			}
			if ($value === QueryBuilder::MAX) {
				return '32767';
			}
			return $value;
		}
		return (string)$value;
	}

	/**
	 * 値を3ビットの数値を表すSQLパラメータ値に変換します。
	 *
	 * @param int|float|string $value 値
	 * @return string 変換結果
	 */
	public function toInt3($value)
	{
		if (!isset($value)) {
			return 'NULL';
		}
		if (is_int($value) || is_float($value)) {
			return sprintf('%d', $value);
		}
		if (is_string($value)) {
			if (strlen($value) === 0) {
				return 'NULL';
			}
			if ($value === QueryBuilder::MIN) {
				return '-8388608';
			}
			if ($value === QueryBuilder::MAX) {
				return '8388607';
			}
			return $value;
		}
		return (string)$value;
	}

	/**
	 * 値を4ビットの数値を表すSQLパラメータ値に変換します。
	 *
	 * @param int|float|string $value 値
	 * @return string 変換結果
	 */
	public function toInt4($value)
	{
		if (!isset($value)) {
			return 'NULL';
		}
		if (is_int($value) || is_float($value)) {
			return sprintf('%d', $value);
		}
		if (is_string($value)) {
			if (strlen($value) === 0) {
				return 'NULL';
			}
			if ($value === QueryBuilder::MIN) {
				return '-2147483648';
			}
			if ($value === QueryBuilder::MAX) {
				return '2147483647';
			}
			return $value;
		}
		return (string)$value;
	}

	/**
	 * 値を8ビットの数値を表すSQLパラメータ値に変換します。
	 *
	 * @param int|float|string $value 値
	 * @return string 変換結果
	 */
	public function toInt8($value)
	{
		if (!isset($value)) {
			return 'NULL';
		}
		if (is_int($value) || is_float($value)) {
			return sprintf('%d', $value);
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

}
