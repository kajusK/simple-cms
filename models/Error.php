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
 * Error handling
 */
class Error
{
	private static $log_mask = 'E_ALL';
	/**
	 * Init error handling
	 */
	public static function init() {
		set_error_handler('Error::errorHandler');
		set_exception_handler('Error::exceptionHandler');
		register_shutdown_function('Error::fatalHandler');

		if (defined("DEBUG") && DEBUG == true)
			error_reporting(-1);
		else
			error_reporting(0);

		switch (ERROR_LOGGING)
		{
		case "simple":
			self::$log_mask = E_WARNING | E_USER_WARNING;
			break;
		case "all":
			self::$log_mask = E_ALL;
			break;
		case "none":
		default:
			$log_mask = 0;
			break;
		}
	}

	/**
	 * Log message into log file
	 *
	 * @param string $message
	 * @param if show and DEBUG are true, show message
	 */	
	public static function log($message, $show=true) {
		if ($show && defined('DEBUG') && DEBUG)
			echo $message;

		$ip = $_SERVER['REMOTE_ADDR'];
		$request = $_SERVER['REQUEST_URI'];
		$time = date("d.m. Y - H:i");
		$msg = "$time - $ip - $request :: $message\n";
		error_log($msg, 3, ERROR_LOG_FILE);
	}

	/**
	 * Simple error handler for set_error_handler
	 */
	public static function errorHandler($errno, $errstr, $errfile, $errline) {
		if (!($errno & self::$log_mask))
			return false;

		$msg = self::_getErrorString($errno).":$errstr:$errfile,line $errline";
		self::log($msg, false);

		if ($errno & error_reporting())
			echo $msg;
	}

	/**
	 * Simple handler for uncatched exceptions
	 */
	public static function exceptionHandler(Exception $e) {
		self::log("Unhandled exception: ".$e->getMessage());
		header("HTTP/1.1 500 Internal Server Error");
		echo "<h1>Sorry, error has occured</h1>";
	}

	/**
	 * Handle fatal errors, log, show simple message, and exit
	 */
	public static function fatalHandler() {
		$error = error_get_last();
		if (!$error || !($error['type'] & (E_ERROR | E_PARSE | E_CORE_ERROR |  E_COMPILE_ERROR | E_USER_ERROR | E_PARSE)))
			return;

		$msg = "FATAL error - ".self::_getErrorString($error['type']).":". $error['message'].":".$error['file'].":".$error['line'];
		error_log($msg);
		header("HTTP/1.1 500 Internal Server Error");
		exit("<h1>Sorry, non-recoveable error has occured</h1>");
	}

	/**
	 * Get error string
	 *
	 * @param int $errno error code 
	 * @return string error name
	 */
	private static function _getErrorString($errno) {
		switch ($errno) {
			case E_ERROR: return 'E_ERROR';
			case E_WARNING: return 'E_WARNING';
			case E_PARSE: return 'E_PARSE';
			case E_NOTICE: return 'E_NOTICE';
			case E_CORE_ERROR: return 'E_CORE_ERROR';
			case E_CORE_WARNING: return 'E_CORE_WARNING';
			case E_COMPILE_ERROR: return 'E_COMPILE_ERROR';
			case E_CORE_WARNING: return 'E_COMPILE_WARNING';
			case E_USER_ERROR: return 'E_USER_ERROR';
			case E_USER_WARNING: return 'E_USER_WARNING';
			case E_USER_NOTICE: return 'E_USER_NOTICE';
			case E_STRICT: return 'E_STRICT';
			case E_RECOVERABLE_ERROR: return 'E_RECOVERABLE_ERROR';
			case E_DEPRECATED: return 'E_DEPRECATED';
			case E_USER_DEPRECATED: return 'E_USER_DEPRECATED';
		}
		return "UNKNOWN";
	}
}
