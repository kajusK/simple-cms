<?php
/**
 * Yet another simple CMS
 *
 * @copyright 2014 Jakub Kaderka
 * @license GNU General Public License, version 2; see LICENSE.txt
 */

//no direct access
defined("IN_CMS") or die("Unauthorized access");

class MenuController extends Controller
{
	protected $view = "menu";

	public function __construct($param = false) {
		$res = Menu::get();
		if (!$res) {
			$this->view = false;
			return;
		}

		//create links
		foreach ($res as & $r) {
			$r['main']['link'] = self::_link($r['main']['id']);
			foreach ($r as $m => & $i) {
				if (is_numeric($m))
					$i['link'] = self::_link($i['id']);
			}
		}

		$this->data['menu'] = $res;
	}

	private function _link($id) {
		if (Article::countAll($id) != 1)
			return Url::get("category", $id);
		
		$page = Article::getPage(0, 1, $id);
		if ($page == false)
			return Url::get("category", $id);

		$article = $page[0];
		return Url::get("page", $article['id'], $article['url']);
	}
}
