<?php
/**
 * Volcanus libraries for PHP
 *
 * @copyright 2011-2013 k-holy <k.holy74@gmail.com>
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
	 * @param string データ
	 * @param string 型名 ($typesフィールド参照)
	 * @return string 変換結果
	 */
	public function parameter($value, $type);

	/**
	 * SELECT文にLIMIT値およびOFFSET値を付与して返します。
	 *
	 * @param string SELECT文
	 * @param int 最大取得件数
	 * @param int 取得開始行index
	 * @return string SQL
	 */
	public function limitOffset($sql, $limit = null, $offset = null);

	/**
	 * SELECT文を元に件数を返すクエリを生成して返します。
	 *
	 * @param string SELECT文
	 * @return string SQL
	 */
	public function count($sql);

	/**
	 * データ型に合わせて項目を別名で取得するSQL句を生成します。
	 *
	 * @param string 項目名
	 * @param string データ型
	 * @param string 別名
	 * @return string SQL句
	 */
	public function expression($expr, $type = null, $alias = null);

}
