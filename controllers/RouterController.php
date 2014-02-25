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

	public function __construct($param) {
		$url = $this->_parseURL($param);
		//set language
		if (isset($url['lang'])) {
			Lang::setLang($url['lang']);
			unset($url['lang']);
		} else {
			Lang::setLang();
			/*
			if (count($url) != 0) {
				$this->statusCode(301);
				$this->redirect(Url::get("article", $res['id'], $res['url']), 0);
			}
			*/
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
	
		$this->menu = new MenuController();
		$this->data['lang_switch'] = $this->_langSwitch($url);

		if ($this->_route($url)) {
			$this->data['title'] = $this->controller->head['title'];
			$this->data['description'] = $this->controller->head['description'];
			$this->data['keywords'] = $this->controller->head['keywords'];
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
	
		/* just in case somebody would play with urls */
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

		$this->controller = new $contClass($param);
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
}
