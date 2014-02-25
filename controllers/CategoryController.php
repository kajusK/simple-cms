<?php
/**
 * Yet another simple CMS
 *
 * @copyright 2014 Jakub Kaderka
 * @license GNU General Public License, version 2; see LICENSE.txt
 */

//no direct access
defined("IN_CMS") or die("Unauthorized access");

class CategoryController extends Controller
{
	protected $view = "category";

	private $cat_id = false;
	private $cat_name = false;

	/**
	 * Generate page of articles
	 *
	 * @param array $param if first is number, show category, else show all
	 */
	public function __construct($param) {
		if (isset($param[0]) && is_numeric($param[0]))
			$this->_category($param);
		else
			$this->_all($param);
	}

	/**
	 * Show page of articles of given category
	 *
	 * @param array $param id,url,[page, page_num]
	 */
	private function _category($param) {
		$name = Menu::getName($param[0]);
		if ($name == false) {
			$this->_notFound();
			return;
		}

		$this->head = array('title' => $name, 'keywords' => "", 'description' => "");

		$page = 1;
		if (isset($param[2])) {
			if ($param[2] != "page" || !isset($param[3]) || !is_numeric($param[3])) {
				$this->_notFound();
				return;
			}
			$page = $param[3];
		}

		//nice url do not match, redirect to correct one
		if (!isset($param[1]) || $name != $param[1]) {
			$this->statusCode(301);
			if (isset($param[2]))
				$this->redirect(Url::get("category", $param[0], $name, "page", $page), 0);

			$this->redirect(Url::get("category", $param[0], $name), 0);
		}

		$this->cat_id = $param[0];
		$this->cat_name = $name;
		$this->_genPaging($param[0], $page, PER_PAGE);
	}

	/**
	 * Show page of all articles
	 *
	 * @param array $param [page, page_num]
	 */
	private function _all($param) {
		$this->head = array('title' => constant("TITLE_".strtoupper(Lang::getLang())),
			       	'keywords' => "", 'description' => "");

		if (count($param) == 0) {
			$this->_genPaging(false, 1, PER_PAGE);
			return;
		}

		if (count($param) != 2 || $param[0] != "page" || !is_numeric($param[1])) {
			$this->_notFound();
			return;
		}

		$this->_genPaging(false, $param[1], PER_PAGE);

	}

	/**
	 * Generate page of articles
	 *
	 * @param mixed $cat_id false or number
	 * @param int $page
	 * @param int $per_page articles per one page
	 */
	private function _genPaging($cat_id, $page, $per_page) {
		$count = Article::countAll($cat_id);
		if (!$count) {
			$this->_empty();
			return;
		}

		$from = ($page - 1)*$per_page;
		if ($from < 0 || $from > $count - 1) {
			$this->_notFound();
			return;
		}

		$this->data['articles'] = Article::getPage($from, $per_page, $cat_id);
		if (!$this->data['articles']) {
			$this->_empty();
			return;
		}
		$temp = Url::getTemp("article", "%d", "%s");
		foreach ($this->data['articles'] as &$a)
			$a['link'] = Url::getFrom($temp, $a['id'], $a['url']);

		$this->_genNav($count, $page, $per_page);
	}

	/**
	 * Generate page navigation
	 *
	 * @param int $count number of articles in category
	 * @param int $page
	 * @param int $per_page articles per one page
	 */
	private function _genNav($count, $page, $per_page) {
		$pages = ceil($count / $per_page);
		if ($pages <= 1) {
			$this->data['nav'] = false;
			return;
		}

		//prepare link
		$url_temp = Url::getTemp("category", $this->cat_id, $this->cat_name, "page", "%d");

		if ($page != 1) {
			$this->data['first'] = Url::get("category", $this->cat_id, $this->cat_name);
			$this->data['prev'] = Url::getFrom($url_temp, $page-1);
		}

		//calculate minimum page number to show
		$first = $page - (int) NAV_MAX_COUNT/2;
		if ($first < 1)
			$first = 1;

		//if close to the end, show NAV_MAX_COUNT pages
		$diff = $first + NAV_MAX_COUNT - $pages; //pages over the last page
		if ($diff > 0)
			$first = ($first - $diff > 0) ? $first - $diff + 1 : 1;

		$this->data['nav'] = array();
		for ($i = $first; $i < $first + NAV_MAX_COUNT && $i <= $pages; $i++)
			$this->data['nav'][$i] = $i == $page ? false : Url::getFrom($url_temp, $i);

		if ($page < $pages) {
			$this->data['next'] = Url::getFrom($url_temp, $page+1);
			$this->data['last'] = Url::getFrom($url_temp, $pages);
		}
	}

	private function _empty() {
		Message::add(Lang::get("CATEGORY_EMPTY"));
		$this->view = false;
	}
}
