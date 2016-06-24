<?php
/**
 * Yet another simple CMS
 *
 * @copyright 2014 Jakub Kaderka
 * @license GNU General Public License, version 2; see LICENSE.txt
 */

define("IN_CMS", true);

/**
 * Classes autoloader
 *
 * Automaticcaly loads class when requested
 *
 * @param string $name name of the class
 * @return void
 */
function class_autoload($name)
{
	$length = strlen($name) - strlen("Controller");

	if ($length < 0 || (strpos($name, "Controller", $length) === FALSE)) {
		require "models/$name.php";
		return;
	}

	if (is_file("controllers/$name.php")) {
		require "controllers/$name.php";
		return;
	}

	//locate subdirectory
	$dir = preg_split('/(?=[A-Z])/', $name, -1, PREG_SPLIT_NO_EMPTY);
	$dir = lcfirst($dir[0]);

	require "controllers/$dir/$name.php";
}
spl_autoload_register("class_autoload");

require_once "config.php";
require_once "define.php";

//set timezone
if (date_default_timezone_set(TIMEZONE) == false) {
	date_default_timezone_set("UTC");
	Err::log("WARNING - UTC timezone used");
}

//load error logger
Err::init();

//get part of url after current dir
$url = substr($_SERVER['REQUEST_URI'], strlen(dirname($_SERVER['SCRIPT_NAME'])));
$router = new RouterController($url);
$router->printView();

