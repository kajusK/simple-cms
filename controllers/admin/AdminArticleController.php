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
			$this->_list($param);
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
		case "page":
			$this->_list($param);
			break;
		default:
			$this->_notFound();
			break;
		}
	}

	/**
	 * List all articles and actions
	 */
	private function _list($param) {
		if (count($param) != 0 && (count($param) != 2 || $param[0] != "page" || is_numeric($param[1]))) {
			$this->_notFound();
			return;
		}
		$page = isset($param[1]) ? $param[1] : 1;
		$this->view = "admin/article_list";

		$this->data = array('add_link' => Url::get("admin", "article", "add"),
			'add' => Lang::get("ARTICLE_ADD"),
			'add_msg' => Lang::get("EDIT_ARTICLE"));

		$this->_genPaging($page, ADMIN_PER_PAGE);
	}

	/**
	 * Generate page of articles
	 *
	 * @param int $page number of page
	 * @param int $per_page articles to show per page
	 */
	private function _genPaging($page, $per_page) {
		$count = Article::countAll();
		if (!$count) {
			Message::add(Lang::get('CATEGORY_EMPTY'));
			$this->data['articles'] = array();
			return;
		}
		$from = Paging::getFrom($page, $per_page, $count);
		if ($from === false) {
			$this->_notFound();
			return;
		}
		$this->data['articles'] = Article::getPage($from, $per_page);
		if (!$this->data['articles']) {
			$this->_notFound();
			return;
		}
		$temp = Url::getTemp("admin", "article", "edit", "%d");
		$temp2 = Url::getTemp("admin", "article", "delete", "%d");
		foreach ($this->data['articles'] as &$a) {
			$a['link'] = Url::getFrom($temp, $a['id']);
			$a['del'] = Url::getFrom($temp2, $a['id']);
		}

		$this->data['nav'] = Paging::genNav($count, $page, $per_page,
				Url::get("admin", "article"),
				Url::getTemp("admin", "article", "page", "%d"));
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

		$comn = $this->_articleCommon(Article::getCategory($id), Comments::allowed($id));
		$this->data['files_link'] = Url::get("admin", "files", "article", $id);
		if (!$comn) return;

		if (isset($_POST['name'])) {
			if (Article::modify($id, $_POST['name'], $_POST['description'], $_POST['keywords'], $_POST['content'], $_POST['category'])) {
				Comments::setPermissions($id, $_POST['comments']);
				$this->view = false;
				$this->redirect(Url::get("article", $id), 2);
			}
			return;
		}

		if (!Article::existsIgnoreLang($id)) {
			$this->_notFound(Lang::get("NO_ARTICLE"));
			return;
		}
		$article = Article::getArticle($id, false);

		if (!$article) {
			$article = array('title' => "",
					'description' => "",
					'keywords' => "",
					'content' => "");
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

		$comn = $this->_articleCommon();
		$this->data['files_link'] = Url::get("admin", "files", "new");
		if (!$comn) return;


		if (isset($_POST['name'])) {
			if (($id = Article::add($_POST['name'], $_POST['description'], $_POST['keywords'], $_POST['content'],
			    $_POST['category'], $_POST['comments']))) {
				$this->view = false;
				$this->redirect(Url::get("article", $id), 2);
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

		$this->data = array('delete_msg' => Lang::get("ARTICLE_DELETE", $name),
				'yes' => Lang::get("YES"),
				'no' => Lang::get("NO"),
				'confirm' => Lang::get("CONFIRM_DELETE"),
				'send' => Lang::get("SEND"));

		if (isset($_POST['delete']) && $_POST['delete'] == "yes") {
			if (Article::remove($id)) {
				$this->view = false;
				$this->redirect(Url::get("admin", "article"), 2);
			}
			return;
		}
	}

	/**
	 * Common method for article modify and add
	 *
	 * @param int $cat category to be marked as selected
	 * @param int $com_selected comment settings to be marked as selected
	 *
	 * @return boolean false if menu is empty
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
				'edit_files' => Lang::get("EDIT_FILES"),
				'adding_allowed' => Lang::get("COM_ADDING_ALLOWED"),
				'adding_disabled' => Lang::get("COM_ADDING_DISABLED"),
				'comments_disabled' => Lang::get("COM_DISABLED"),
				'com_settings' => Lang::get("COM_SETTINGS"),
				'message_files' => Lang::get("FILE_TUTORIAL"),
				'com_selected' => $com_selected);

		if (isset($_POST['name'])) {
			$this->data['name'] = $_POST['name'];
			$this->data['description'] = $_POST['description'];
			$this->data['keywords'] = $_POST['keywords'];
			$this->data['content'] = $_POST['content'];
			$this->data['cat_id'] = isset($_POST['category']) ? $_POST['category'] : -1;
			$this->data['com_selected'] = $_POST['comments'];
		} else {
			$this->data['cat_id'] = $cat_id ? $cat_id : -1;
		}

		$this->data['category'] = $this->_categories();
		if (!$this->data['category']) {
			Message::add(Lang::get("MENU_EMPTY"));
			if (isset($_POST['name']))
				return false;
		}
		
		return true;
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
