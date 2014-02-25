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
 * Abstract class inherited by all controllers
 */
abstract class Controller
{
	protected $data = array(); //data accessible by view
	protected $head = array(); //data to show in head - title, keywords, description
	protected $view; //corresponding name of view

	/**
	 * Class initialization, get data from model, etc.
	 *
	 * @param mixed $param usually parsed url parameters or something similar
	 * @return void
	 */
	abstract public function __construct($param);

	/**
	 * Print view associated with this controller
	 */
	public function printView() {
		if (!$this->view)
			return;
		
		$this->data['base'] = Url::getBase()."/";
		extract($this->data);
		require "views/".$this->view.".phtml";
	}

	/**
	 * Simple redirection without waiting
	 *
	 * @param string $url urt to redirect to
	 * @param int $time time to wait before refresh
	 */
	public function redirect($url, $time = 0) {
		header("refresh: $time; url=$url");
		if ($time == 0)
			exit();
	}

	/**
	 * Send status code to user's browser
	 *
	 * Must be called before any output
	 *
	 * @param int $code code to send
	 * @return boolean true if code is known, else false
	 */
	public function statusCode($code) {
		switch ($code){
		case 301:
			$string = "301 Moved Permanently";
			break;
		case 404:
			$string = "404 Not Found";
			break;
		case 500:
			$string = "500 Internal Server Error";
			break;
		case 503:
			$string = "503 Service Unavaliable";
			break;
		}

		if (!isset($string))
			return false;

		header("HTTP/1.1 ".$string);
		return true;
	}

	/**
	 * Page not found
	 *
	 * Don't use in router controller
	 *
	 * @param mixed $message if set, show this message else show NOT_FOUND
	 */
	protected function _notFound($message = false) {
		if ($message)
			Message::add($message);
		else
			Message::add(Lang::get("NOT_FOUND"));

		$this->head = array('title' => Lang::get("TITLE_NOT_FOUND"),
			'keywords' => "",
			'description' => "");

		$this->view = false;
		$this->statusCode(404);
	}
}

/**
 * Escape html chars
 *
 * @param string $string
 * @return string
 */
function e($string)
{
	return htmlspecialchars($string);
}
