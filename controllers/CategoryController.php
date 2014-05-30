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
	 * @param array $param id,[page, page_num]
	 */
	private function _category($param) {
		$name = Menu::getName($param[0]);
		if ($name == false) {
			$this->_notFound();
			return;
		}

		$this->head = array('title' => $name, 'keywords' => "", 'description' => constant("DESCRIPTION_".strtoupper(Lang::getLang())));

		$page = 1;
		if (isset($param[1])) {
			if ($param[1] != "page" || !isset($param[2]) || !is_numeric($param[2])) {
				$this->_notFound();
				return;
			}
			$page = $param[2];
		}

		$this->_genPaging($param[0], $page, PER_PAGE);
	}

	/**
	 * Show page of all articles
	 *
	 * @param array $param [page, page_num]
	 */
	private function _all($param) {
		$this->head = array('title' => constant("TITLE_".strtoupper(Lang::getLang())),
			       	'keywords' => "", 'description' => constant("DESCRIPTION_".strtoupper(Lang::getLang())));

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

		if (($from = Paging::getFrom($page, $per_page, $count)) === false) {
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

		/* gen page navigation */
		$this->data['nav'] = Paging::genNav($count, $page, $per_page,
				Url::get("category", $cat_id),
				Url::getTemp("category", $cat_id, "page", "%d"));
	}

	private function _empty() {
		Message::add(Lang::get("CATEGORY_EMPTY"));
		$this->view = false;
	}
}
