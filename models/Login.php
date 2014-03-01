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
 * Simple admin user validation
 */
class Login
{
	private static $session = false;

	/**
	* Log user in
	*
	*
	* @param string $user username
	* @param string $pass
	* @return boolean true if suceed
	*/
	public static function create($user, $pass) {
		if ($user != ADMIN_USER || $pass != ADMIN_PASS) {
			Message::add(Lang::get("WRONG_LOGIN"));
			return false;
		}

		self::_start();
		$_SESSION["logged"] = true;
		session_regenerate_id();
		return true;
	}

	/**
	* Logout user
	*
	* @return boolean true if suceed
	*/
	public static function logout() {
		session_destroy();
		return true;
	}

	/**
	* Is user logged id?
	*
	* @return boolean true if is, else otherwise
	*/
	public static function isLogged() {
		self::_start();
		if (isset($_SESSION["logged"]) && $_SESSION['logged'] == true)
			return true;
		return false;
	}

	/**
	 * Start session if not running already
	 */
	private static function _start() {
		if (self::$session)
			return;
		session_start();
		self::$session = true;
	}
}
