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

	if ($length >= 0 && (strpos($name, "Controller", $length) !== FALSE))
		require "controllers/$name.php";
	else
		require "models/$name.php";
}

spl_autoload_register("class_autoload");

require_once "config.php";
require_once "define.php";

//set timezone
if (date_default_timezone_set(TIMEZONE) == false) {
	date_default_timezone_set("UTC");
	Logging::warning("UTC timezone used");
}


//get part of url after current dir
$url = substr($_SERVER['REQUEST_URI'], strlen(dirname($_SERVER['SCRIPT_NAME'])));
$router = new RouterController($url);
$router->printView();

