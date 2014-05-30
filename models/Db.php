<?php
/**
 * Yet another simple CMS
 *
 * @copyright 2014 Jakub Kaderka
 * @license GNU General Public License, version 2; see LICENSE.txt
 */

//no direct access
defined("IN_CMS") or die("Unauthorized access");

/**
 * Database wrapper
 */
class Db
{
	private static $result = false;
	private static $mysqli;
	private static $rows_affected;

	/**
	 * Connect to database
	 *
	 * @return boolean true if suceed, false otherwise
	 */
	public static function connect($host, $user, $pass, $database, $port = NULL) {
		if (!empty(self::$mysqli)) {
			return true;
		}
		if (!$port)
			$port = ini_get("mysqli.default_port");

		self::$mysqli = new mysqli($host, $user, $pass, $database, $port);
		if (self::$mysqli->connect_errno) {
			Error::log("Unable to connect to DB - ".self::$mysqli->connect_error);
			return false;
		}
		self::$mysqli->set_charset('utf8');

		return true;
	}

	/**
	 * Disconnect from database if connected
	 */
	public static function close() {
		if (self::$result)
			self::$result->free();

		if (!empty(self::$mysqli))
			self::$mysqli->close();
	}

	/**
	 * Query all rows
	 *
	 * @param string $query string, where params are replaced by ?
	 * @param array $params params to use in query, size of array must be equal to number of ?
	 * @return mixed array of result's arrays or false
	 */
	public static function query($query, $params = array()) {
		if (!self::_genQuery($query, $params))
			return false;

		return self::$result->fetch_all(MYSQLI_ASSOC);
	}

	/**
	 * Query one row
	 *
	 * @param string $query string, where params are replaced by ?
	 * @param array $params params to use in query, size of array must be equal to number of ?
	 * @return mixed array of one result or false
	 */
	public static function queryRow($query, $params = array()) {
		if (!self::_genQuery($query, $params))
			return false;

		return self::$result->fetch_assoc();
	}

	/**
	 * Insert params into table
	 *
	 * @param string $table table to insert into
	 * @param array $params associative array, keys are column names, its values data to insert
	 *
	 * @return int rows affected
	 */
	public static function insert($table, $params = array()) {
		self::_genQuery("INSERT INTO `$table` (`".
				implode("`,`", array_keys($params))."`) VALUES (".
				str_repeat("?,", sizeof($params) - 1)."?)", $params);
		return self::rowsAffected();
	}

	/**
	 * Update table
	 *
	 * @param string $table table to update
	 * @param array $where associative array, keys are column names, values are compared as equal
	 * @param array $params associative array, keys are column names, its values data to insert
	 *
	 * @return int rows affected
	 */
	public static function update($table, $where = array(), $params = array()) {
		self::_genQuery("UPDATE `$table` SET `".implode("`=?,`", array_keys($params))."`=? WHERE `".
			implode("`=?,`", array_keys($where))."`=?", array_merge($params, array_values($where)));
		return self::rowsAffected();
	}

	/**
	 * Remove rows from table
	 *
	 * @param string $table table to delete from
	 * @param array $where associative array, keys are column names, values are compared as equal
	 * @return int rows affected
	 */
	public static function remove($table, $where = array()) {
		self::_genQuery("DELETE FROM $table WHERE `".
			implode("`=?,`", array_keys($where))."`=?", $where);
		return self::rowsAffected();
	}

	/**
	 * Get number of rows in result
	 *
	 * @return int number of rows
	 */
	public static function rowsCount() {
		return self::$result->num_rows;
	}

	/**
	 * Get total number of affected rows (for INSERT, UPDATE or DELETE)
	 *
	 * @return int number of affected rows
	 */
	public static function rowsAffected() {
		return self::$rows_affected;
	}

	/**
	 * Load file.sql and run it as query
	 *
	 * @param string $string query
	 * @return boolean
	 */
	public static function loadDump($string) {
		return self::$mysqli->multi_query($string);
	}

	/**
	 * Generate query
	 *
	 * @param string $query string, where params are replaced by ?
	 * @param array $params params to use in query, size of array must be equal to number of ?
	 * @return boolean true if suceed, else false
	 */
	private static function _genQuery($query, $params = array()) {
		if (self::$result) {
			self::$result->free();
			self::$result = false;
		}

		$stmt = self::$mysqli->prepare($query);
		if ($stmt == false) {
			Error::log("DB error - ".self::$mysqli->error);
			return false;
		}

		if ($params) {
			$_params = array();
			$_params[] = self::_getTypes($params);
			$params = array_merge($_params, $params);

			call_user_func_array(array($stmt, "bind_param"), self::_refValues($params));
		}

		$stmt->execute();
		if ($stmt->error)
			Error::log("DB error - ".$stmt->error);

		self::$result = $stmt->get_result();
		self::$rows_affected = $stmt->affected_rows;

		return true;
	}

	/**
	 * Get types of values from array
	 *
	 * @return string of types
	 */
	private static function _getTypes($array) {
		$out = "";
		foreach ($array as $a)
			$out .= self::_getVarType($a);

		return $out;
	}

	/**
  	 * Get type of $var, s is default
	 */
	private static function _getVarType($var) {
		switch (gettype($var)) {
		case "string":
			return 's';
			break;

		case "integer":
			return 'i';
			break;

		case "double":
			return 'd';
			break;

		case "blob":
			return 'b';
			break;
		}

		return 's';
	}

	/**
	 * Translate all values in array into references
	 *
	 * @param array $arr array of values
	 * @return array of references
	 */
	private static function _refValues($arr){
		if (strnatcmp(phpversion(),'5.3') >= 0) {
			$refs = array();
			foreach($arr as $key => $value)
				$refs[$key] = &$arr[$key];
			return $refs;
		}
		return $arr;
	}
}
