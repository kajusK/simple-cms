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
 * Url generation
 */
class Url
{
	private static $start;

	/**
	 * Generate url to pages' base dir
	 *
	 * @return string
	 */
	public static function getBase() {
		if (isset(self::$start))
			return self::$start;

		$url = "http";
		if (isset($_SERVER['HTTPS']) && $_SERVER["HTTPS"] != "off")
			$url .= "s";
		$url .= "://".$_SERVER['SERVER_NAME'];

		if ($_SERVER["SERVER_PORT"] != "80")
			$url .= ":".$_SERVER['SERVER_PORT'];

		$url .= dirname($_SERVER['SCRIPT_NAME']);

		self::$start = $url;
		return $url;
	}

	/**
	 * Generate local link
	 *
	 * Language is added automatically
	 *
	 * @param string $controller controller to call
	 * @param mixed $... variable lenght arguments - parts of url
	 *		false are ignored
	 * @return string url
	 */
	public static function get($controller) {
		$args = func_get_args();

		$link = call_user_func_array(array("self","getPure"), $args);
		$link .= "/".Lang::getLang();

		return $link;
	}

	/**
	 * Get url to current page
	 */
	public static function getSelf() {
		$url = self::getBase();
		$url .= substr($_SERVER['REQUEST_URI'], strlen(dirname($_SERVER['SCRIPT_NAME'])));
		return $url;
	}

	/**
	 * Generate local link
	 *
	 * No language added
	 *
	 * @param mixed $... variable lenght arguments - parts of url
	 *		false are ignored
	 * @return string url
	 */
	public static function getPure() {
		$link = self::getBase();
		$args = func_get_args();

		foreach ($args as $a) {
			if ($a !== false)
				$link .= "/$a";
		}
		return $link;
	}

	/**
  	 * Generate local url template
	 *
	 * Used to generate string for getFrom function,
	 * language added automatically
	 *
	 * @param string $controller controller to call
	 * @param mixed $... variable lenght arguments - parts of url
	 *		false are ignored, %char work in same way like sprintf
	 * @return string url template
	 */
	public static function getTemp($controller) {
		return call_user_func_array(array("self", "get"), func_get_args());
	}

	/**
  	 * Generate local url template
	 *
	 * Used to generate string for getFrom function,
	 * no Lang added
	 *
	 * @param mixed $... variable lenght arguments - parts of url
	 *		false are ignored, %char work in same way like sprintf
	 * @return string url template
	 */
	public static function getTempPure() {
		return call_user_func_array(array("self", "getPure"), func_get_args());
	}

	/**
  	 * Generate local url template
	 *
	 * Used to generate string for getFrom function
	 * %s for lang added as last parameter
	 *
	 * @param mixed $... variable lenght arguments - parts of url
	 *		false are ignored, %char work in same way like sprintf
	 * @return string url template
	 */
	public static function getTempLang() {
		$ret = call_user_func_array(array("self", "getPure"), func_get_args());
		$ret .= '/%s';
		return $ret;
	}

	/**
	 * Generate local url from template
	 *
	 * @param string $format generated by getTemp function
	 * @param mixed $... for each %char from template must be one argument, like
	 *		for sprintf
	 * @return string url
	 */
	public static function getFrom($format) {
		return call_user_func_array("sprintf", func_get_args());
	}
}
