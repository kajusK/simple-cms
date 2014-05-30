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
 * Visitors logging - access log
 */
class Logging
{
	private static $ip = 0;

	/**
	 * Log user's visit
	 */
	public static function logVisit() {
		self::$ip = $_SERVER['REMOTE_ADDR'];
		$user_agent = $_SERVER['HTTP_USER_AGENT'];
		$lang = Lang::getLang();
		$date = date('Y-m-d H:i:s');

		if (!self::_logExists(self::$ip)) {
			Db::query("INSERT INTO `log` (`ip`, `user_agent`, `lang`, `visit_last`) VALUES (INET_ATON(?),?,?,?)",
				       	array(self::$ip,$user_agent,$lang,$date), true);
			return;
		}

		Db::query("UPDATE `log` SET `user_agent`=?,`lang`=?,`visit_last`=? WHERE `ip`=INET_ATON(?)", array($user_agent,$lang,$date,self::$ip), true);
	}

	/**
	 * Number of unique IPs since midnight
	 *
	 * @return int
	 */
	public static function visitorsToday() {
		$res = Db::queryRow("SELECT COUNT(*) FROM `log` WHERE DATE(`visit_last`) = CURDATE()");
		if (!$res)
			return 0;
		return $res['COUNT(*)'];
	}

	/**
	 * Number of unique IPs since first log
	 *
	 * @return int
	 */
	public static function visitorsTotal() {
		$res = Db::queryRow("SELECT COUNT(*) FROM `log`");
		if (!$res)
			return 0;
		return $res['COUNT(*)'];
	}

	/**
	 * Was this ip logged before
	 *
	 * @param string $ip
	 * @return boolean
	 */
	private static function _logExists($ip) {
		$res = Db::queryRow("SELECT COUNT(*) FROM `log` WHERE `ip` = INET_ATON(?)", array($ip));
		if (!$res)
			return true;
		if ($res['COUNT(*)'])
			return true;
		return false;
	}
}
