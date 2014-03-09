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
 * Articles administration
 */
class AdminArticleController extends Controller
{

	/**
	 * @param array $param add/modify/delete id
	 */
	public function __construct($param)
	{
		if (count($param) == 0) {
			$this->_notFound();
			return;
		}

		$action = array_shift($param);
		switch ($action) {
		case "add":
			$this->_add($param);
			break;
		case "edit":
			$this->_edit($param);
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
	 * Modify article of given id
	 *
	 * @param array $param article_id
	 */
	private function _edit($param) {
		if (count($param) != 1 || !is_numeric($param[0])) {
			$this->_notFound();
			return;
		}
		$id = $param[0];

		$this->_articleCommon(Article::getCategory($id), Comments::allowed($id));

		if (isset($_POST['name'])) {
			if (Article::modify($id, $_POST['name'], $_POST['description'], $_POST['keywords'], $_POST['content'], $_POST['category'])) {
				Comments::setPermissions($id, $_POST['comments']);
				$this->view = false;
				$this->redirect(Url::get("article", $id), 2);
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
	 * @param array $param must be empty
	 */
	private function _add($param) {
		if (count($param) != 0) {
			$this->_notFound();
			return;
		}

		$this->_articleCommon();

		if (isset($_POST['name'])) {
			if (Article::add($_POST['name'], $_POST['description'], $_POST['keywords'], $_POST['content'],
			    $_POST['category'], $_POST['comments'])) {
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
	 * @param array $param article_id
	 */
	private function _delete($param) {
		if (count($param) != 1 || !is_numeric($param[0])) {
			$this->_notFound();
			return;
		}

		$this->view = "admin/delete";
		$id = $param[0];

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
			if (Article::remove($id)) {
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
	 * @param int $com_selected comment settings to be marked as selected
	 */
	private function _articleCommon($cat_id = false, $com_selected = 1) {
		$this->view = "admin/article";
		$this->data = array('article_edit' => Lang::get("EDIT_ARTICLE"),
				'article_name' => Lang::get("EDIT_TITLE"),
				'article_description' => Lang::get("EDIT_DESCRIPTION"),
				'article_keywords' => Lang::get("EDIT_KEYWORDS"),
				'article_category' => Lang::get("CATEGORY"),
				'article_content' => Lang::get("EDIT_CONTENT"),
				'send' => Lang::get("SEND"),
				'adding_allowed' => Lang::get("COM_ADDING_ALLOWED"),
				'adding_disabled' => Lang::get("COM_ADDING_DISABLED"),
				'comments_disabled' => Lang::get("COM_DISABLED"),
				'com_settings' => Lang::get("COM_SETTINGS"),
				'com_selected' => $com_selected);

		if (isset($_POST['name'])) {
			$this->data['name'] = $_POST['name'];
			$this->data['description'] = $_POST['description'];
			$this->data['keywords'] = $_POST['keywords'];
			$this->data['content'] = $_POST['content'];
			$this->data['cat_id'] = $_POST['category'];
			$this->data['com_selected'] = $_POST['comments'];
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
