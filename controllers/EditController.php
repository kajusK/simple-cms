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
 * Articles administration, requires login
 */
class EditController extends Controller
{

	/**
	 * @param mixed $param [logout/add/modify/delete][id]
	 */
	public function __construct($param)
	{
		$this->head['title'] = Lang::get("TITLE_ADMIN");
		if (!Login::isLogged() && !$this->_login($param))
			return;

		if (count($param) == 0) {
			$this->_notFound();
			return;
		}

		switch ($param[0]) {
		case "logout":
			Login::logout();
			$this->redirect(Url::get("edit"));
			break;
		case "add":
			$this->_add($param);
			break;
		case "modify":
			$this->_modify($param);
			break;
		case "delete":
			$this->_delete($param);
			break;
		default:
			$this->_notFound();
			break;
		}

	}

	/**
	 * Login user
	 *
	 * @param mixed $param array of controller parameters
	 */
	private function _login($param) {
		if (count($param) != 0)
			$this->statusCode(401);

		$this->view = "edit/login";
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

	/**
	 * Modify article of given id
	 *
	 * @param mixed $param count must be 2 and second must be article id
	 */
	private function _modify($param) {
		if (count($param) != 2 || !is_numeric($param[1])) {
			$this->_notFound();
			return;
		}
		$id = $param[1];

		$this->_articleCommon(Article::getCategory($id));

		if (isset($_POST['name'])) {
			if (Article::modify($id, $_POST['name'], $_POST['description'], $_POST['keywords'], $_POST['content'], $_POST['category'])) {
				$this->view = false;
				$this->redirect(Url::get(false), 2);
			}
			return;
		}

		$article = Article::getArticle($id);
		if (!$article) {
			$this->_notFound(Lang::get("NO_ARTICLE"));
			return;
		}

		$this->data['name'] = $article['title'];
		$this->data['description'] = $article['description'];
		$this->data['keywords'] = $article['keywords'];
		$this->data['content'] = $article['content'];
	}

	/**
	 * Add new article
	 *
	 * @param mixed $param count must be 1
	 */
	private function _add($param) {
		if (count($param) != 1) {
			$this->_notFound();
			return;
		}

		$this->_articleCommon();

		if (isset($_POST['name'])) {
			if (Article::add($_POST['name'], $_POST['description'], $_POST['keywords'], $_POST['content'], $_POST['category'])) {
				$this->view = false;
				$this->redirect(Url::get(false), 2);
			}
			return;
		}

		$this->data['name'] = "";
		$this->data['description'] = "";
		$this->data['keywords'] = "";
		$this->data['content'] = "";
	}

	/**
	 * Delete article
	 *
	 * @param mixed $param count must be 2 and second must be article id
	 */
	private function _delete($param) {
		if (count($param) != 2 || !is_numeric($param[1])) {
			$this->_notFound();
			return;
		}

		$this->view = "edit/delete";
		$id = $param[1];

		$name = Article::getName($id);
		if (!$name) {
			$this->_notFound(Lang::get("NO_ARTICLE"));
			return;
		}

		$this->data = array('article_delete' => Lang::get("ARTICLE_DELETE", $name),
				'yes' => Lang::get("YES"),
				'no' => Lang::get("NO"),
				'confirm' => Lang::get("CONFIRM_DELETE"),
				'send' => Lang::get("SEND"));

		if (isset($_POST['delete']) && $_POST['delete'] == "yes") {
			if (Article::remove($param[1])) {
				$this->view = false;
				$this->redirect(Url::get(false), 2);
			}
			return;
		}
	}

	/**
	 * Common method for article modify and add
	 *
	 * @param int $cat category to be marked as selected
	 */
	private function _articleCommon($cat_id = false) {
		$this->view = "edit/article";
		$this->data = array('article_edit' => Lang::get("EDIT_ARTICLE"),
				'article_name' => Lang::get("EDIT_TITLE"),
				'article_description' => Lang::get("EDIT_DESCRIPTION"),
				'article_keywords' => Lang::get("EDIT_KEYWORDS"),
				'article_category' => Lang::get("CATEGORY"),
				'article_content' => Lang::get("EDIT_CONTENT"),
				'send' => Lang::get("SEND"));

		if (isset($_POST['name'])) {
			$this->data['name'] = $_POST['name'];
			$this->data['description'] = $_POST['description'];
			$this->data['keywords'] = $_POST['keywords'];
			$this->data['content'] = $_POST['content'];
			$this->data['cat_id'] = $_POST['category'];
		} else {
			$this->data['cat_id'] = $cat_id ? $cat_id : -1;
		}

		$this->data['category'] = $this->_categories();
	}

	/**
	 * Get array of all possible categories
	 *
	 * @return array of categories
	 */
	private function _categories() {
		$list = Menu::getRaw();
		if (!$list)
			return array();

		return $list;
	}
}