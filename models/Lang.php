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
 * Translantions and locale settings
 */
class Lang
{
	private static $transl = array();
	private static $lang = LOCAL_LANG;

	/**
	 * Set language and locale
	 *
	 * Language is choosen in order - parameter, HTTP_ACCEPT_LANGUAGE, default one
	 *
	 * @param string $name (optional), name of language to load (eg. cs; en...)
	 * @return string of language name if set, else false
	 */
	public static function setLang($name = '') {
		if (!empty($name) && self::isLang($name)) {
		        self::$lang = $name;
		} else if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
			$parsed = self::_getAcceptLang($_SERVER['HTTP_ACCEPT_LANGUAGE']);
			if ($parsed)
	        		self::$lang = $parsed;
		}

		if (!self::isLang(self::$lang)) {
			Logging::error("No language file for ".self::$lang);
			return false;
		}

		require_once "lang/".self::$lang.".php";

		self::$transl = $lang;
		setlocale(LC_ALL, $locale);

		return self::$lang;
	}

	/**
	 * Get translation for given key
	 *
	 * Supply key string as param for sprintf, if string contains some formating labels,
	 * this function must be called with more parameters (just like sprintf);
	 *
	 * @param string $key: keyword to show
	 * @return string translation of given key or Unknown
	*/
	public static function get($key) {
		if (!isset(self::$transl[$key])) {
			Logging::error("Requested translation of key '$key'");
			return "Unknown";
		}

		$args = array(self::$transl[$key]);
		for ($i = 1; $i < func_num_args(); $i++)
			$args[] = func_get_arg($i);

		return call_user_func_array('sprintf', $args);
	}

	/**
	 * @return current language
	 */
	public static function getLang() {
		return self::$lang;
	}

	/**
	 * Get installed languages
	 *
	 * @return array of available languages or false
	 */
	public static function getList() {
		if (($dir = opendir("lang/")) == false)
			return false;

		$list = array();
		while (($file = readdir($dir)) !== false) {
			if ($file[0] != '.')
				$list[] = basename($file, ".php");
		}

		return $list;
	}

	/**
  	 * Does this translation exists?
	 *
	 * @param string $str language to check
	 * @return boolean
	 */
	public static function isLang($str) {
		if (is_file("lang/$str.php"))
			return true;
		return false;
	}

	/**
	 * Get first avaliable language from string
	 *
	 * @param string $string string to parse
	 * @return string first avaliable language name or false
	 */
	private static function _getAcceptLang($string) {
		$lang = substr($string, 0, 2);
		if (self::isLang($lang))
			return $lang;
		return false;
	}
}
