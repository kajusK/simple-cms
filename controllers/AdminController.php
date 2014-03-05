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
 * Page administration, requires login
 */
class AdminController extends Controller
{
	protected $controller = false;

	/**
	 * @param array $param [logout/admin] [other params]
	 */
	public function __construct($param)
	{
		$this->head['title'] = Lang::get("TITLE_ADMIN");
		$this->view = "admin/main";

		if (!Login::isLogged() && !$this->_login($param))
			return;

		if (count($param) == 0) {
			$this->_notFound();
			return;
		}

		$action = array_shift($param);
		switch ($action) {
		case "logout":
			Login::logout();
			$this->redirect(Url::get("admin"));
			break;

		case "article":
			$this->controller = new AdminArticleController($param);
			break;
		default:
			$this->_notFound();
			break;
		}

	}

	/**
	 * Login user
	 *
	 * @param array $param array of controller parameters
	 */
	private function _login($param) {
		if (count($param) != 0)
			$this->statusCode(401);

		$this->view = "admin/login";
		$this->data = array('username' => Lang::get("USERNAME"),
				'pass' => Lang::get("PASS"),
				'send' => Lang::get("SEND"),
				'action' => Url::get("edit"),
				'admin_login' => Lang::get("ADMIN_LOGIN"),
				'name' => "");

		if (!isset($_POST['name']))
			return false;

		$this->data['name'] = $_POST['name'];
		if (Login::create($_POST['name'], $_POST['pass']))
			$this->redirect(Url::getSelf());
	}
}
