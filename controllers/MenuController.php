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
			$r['main']['link'] = Url::get("category", $r['main']['id'], $r['main']['name']);
			foreach ($r as $m => & $i) {
				if (is_numeric($m))
					$i['link'] = Url::get("category", $i['id'], $i['name']);
			}
		}

		$this->data['menu'] = $res;
	}
}
