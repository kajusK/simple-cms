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
 * Menu manager
 */
class Menu
{
	/**
	 * Get whole menu
	 *
	 * @return mixed false or array - each element contain at least ['main'] array
	 *		and may contain unspecified number of submenus array
	 */
	public function get() {
		$res = Db::query("SELECT name_".Lang::getLang()." AS name, id, parent_id FROM
				  menu  ORDER BY parent_id, id");
		if (!$res)
			return false;

		$menu = array();

		foreach ($res as $n) {
			if ($n['parent_id'] == 0) {
				$menu[$n['id']]['main'] = $n;
				continue;
			}

			$menu[$n['parent_id']][] = $n;
		}

		return $menu;
	}

	/**
	 * Get all menu items
	 *
	 * @return mixed array of items or false
	 */
	public function getRaw() {
		return Db::query("SELECT name_".Lang::getLang()." AS name, id FROM menu");
	}

	/**
	 * Get name of given category
	 *
	 * @param int $id category id
	 * @return mixed string or false
	 */
	public function getName($id) {
		$res = Db::queryRow("SELECT name_".Lang::getLang()." AS name FROM
				  menu  WHERE id=?", array($id));

		if (!$res)
			return false;
		return $res['name'];
	}
}
