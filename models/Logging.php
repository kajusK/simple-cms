<?php
/**
 * Yet another simple CMS
 *
 * @copyright 2014 Jakub Kaderka
 * @license GNU General Public License, version 2; see LICENSE.txt
 */

//no direct access
defined("IN_CMS") or die("Unauthorized access");

class Logging
{
	private $log_file = "";

	public static function info($msg) {
		echo "Log: fatal $msg";
	}
	public static function warn($msg) {
		echo "Log: fatal $msg";
	}
	public static function error($msg) {
		echo "Log: fatal $msg";
	}

	public static function setLogFile($file) {
		$f = fopen($file, "a");
		if (!$f)
			return false;
		fclose($f);

		$log_file = $file;
		return true;
	}

}
