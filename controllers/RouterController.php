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
 * URL router
 *
 * translate URL, set language, run logging,.. call required controller
 */
class RouterController extends Controller
{
	protected $controller;
	protected $menu;
	protected $view = "main";

	/**
	 * @param string $param url script was called by
	 */
	public function __construct($param) {
		$url = $this->_parseURL($param);
		//set language
		if (isset($url['lang'])) {
			Lang::setLang($url['lang']);
			unset($url['lang']);
		} else {
			Lang::setLang();
			/* no language in url, redirect to correct url */
			if (count($url) != 0) {
				$this->statusCode(301);
				$link = call_user_func_array("Url::get", $url);
				$this->redirect($link);
				return;
			}
		}
		$this->data['lang'] = Lang::getLang();

		if (!Db::connect(DB_HOST, DB_USER, DB_PASS, DB_NAME)) {
			$this->data['title'] = Lang::get("TITLE_ERROR");
			$this->data['message'] = Lang::get("DATABASE_CON_ERR");
			$this->statusCode(503);
			$this->view = "error";
			return;
		}
		register_shutdown_function("Db::close");

		/* log user's visit */
		Logging::logVisit();
		$this->_loadFooter();

		$this->menu = new MenuController();
		$this->data['lang_switch'] = $this->_langSwitch($url);

		/* load required controller */
		if ($this->_route($url)) {
			$this->data = array_merge($this->data, $this->controller->head);
		} else {
			$this->data['title'] = Lang::get("TITLE_NOT_FOUND");
			Message::add(Lang::get("NOT_FOUND"));
			$this->statusCode(404);
		}

		/* load all messages */
		$this->data['messages'] = Message::getMessages();
	}

	/**
	 * Get language switch array
	 *
	 * @param mixed $param params to set in url
	 * @return mixed false or array
	 */
	private function _langSwitch($param) {
		$langs = Lang::getList();
		if ($langs === false)
			return false;

		/* just in case somebody would play with urls (getTemp use sprintf) */
		foreach ($param as & $p)
			$p = str_replace("%", "%%", $p);

		$temp = call_user_func_array("Url::getTempLang", $param);
		$cur = Lang::getLang();
		$ret = array();
		foreach ($langs as $l) {
			if ($l != $cur) {
				$ret[] = array("name" => $l,
					"link" => Url::getFrom($temp, $l));
			}
		}
		return $ret;
	}

	/**
	 * Check params, route
	 *
	 * @param mixed $param - params to send
	 * @return boolean true if successful
	 */
	private function _route($param) {
		/* no param, show category */
		if (count($param) == 0) {
			$this->controller = new CategoryController($param);
			return true;
		}
		/* first param number, show article */
		if (is_numeric($param[0])) {
			$this->controller = new ArticleController($param);
			return true;
		}

		/* load given controller */
		$contClass = ucfirst(array_shift($param))."Controller";
		if (!is_file("controllers/$contClass.php") ||
		    $contClass == "Controller" || $contClass == "RouterController") {
			return false;
		}

		/* avoid loading controller without title set */
		$tmp = new $contClass($param);
		if (!isset($tmp->head['title']))
			return false;

		$this->controller = $tmp;
		return true;
	}

	/**
	 * Parse given url to array, separate by /
	 *
	 * @param string $url url to parse
	 * @return array of parsed strings, in array, there could be 'lang' line containing
	 *	requested language
	 */
	private function _parseURL($url) {
		if ($url[0] == '/')
			$url = substr($url, 1);
		if ($url[strlen($url)-1] == '/')
			$url = substr($url, 0, -1);

		if (strlen($url))
			$array = explode('/', $url);
		else
			$array = array();

		//try to find language
		$length = count($array);
		if ($length != 0 && strlen($array[$length-1]) == 2)  {
			$str = $array[$length -1];
			if (Lang::isLang($str)) {
				unset($array[$length-1]);
				$array['lang'] = $str;
			}
		}

		return $array;
	}

	/**
	 * Load data for page footer - counter and rss
	 */
	private function _loadFooter() {
		$this->data['count_total'] = Logging::visitorsTotal();
		$this->data['count_today'] = Logging::visitorsToday();
		$this->data['msg_count'] = Lang::get('COUNTER');
		$this->data['msg_count_total'] = Lang::get('COUNT_TOTAL');
		$this->data['msg_count_today'] = Lang::get('COUNT_TODAY');

		$this->data['rss_link'] = Url::get("rss");
	}
}
