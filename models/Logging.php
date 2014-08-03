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

		$id = self::_logToday(self::$ip);
		if (!$id) {
			Db::query("INSERT INTO `log` (`ip`, `user_agent`, `lang`) VALUES (INET_ATON(?),?,?)",
				array(self::$ip,$user_agent,$lang), true);
			return;
		}

		Db::query("UPDATE `log` SET `date`=? WHERE `id`=?", array(date('Y-m-d H:i:s'),$id), true);
	}

	/**
	 * Get current visitor's ip
	 *
	 * @return string ip
	 */
	public static function getIP() {
		return self::$ip;
	}

	/**
	 * Number of unique IPs since midnight
	 *
	 * @return int
	 */
	public static function visitorsToday() {
		$res = Db::queryRow("SELECT COUNT(*) FROM `log` WHERE DATE(`date`) = CURDATE()");
		if (!$res)
			return 0;
		return $res['COUNT(*)'];
	}

	/**
	 * Number of visitors since first log
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
	 * Was this ip logged today already?
	 *
	 * @param string $ip
	 * @return boolean
	 */
	private static function _logToday($ip) {
		$res = Db::queryRow("SELECT `id` FROM `log` WHERE `ip` = INET_ATON(?) AND DATE(`date`) = CURDATE() ORDER BY `date` DESC LIMIT 1", array($ip));
		if (!$res)
			return false;
		return $res['id'];
	}
}
