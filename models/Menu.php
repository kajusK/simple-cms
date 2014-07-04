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
	public static function get() {
		$res = Db::query("SELECT name_".Lang::getLang()." AS name, id, parent_id FROM
				  menu  ORDER BY parent_id, id");
		if (!$res)
			return false;

		$menu = array();

		foreach ($res as $n) {
			//skip items without name
			if (!$n['name'])
				continue;

			if ($n['parent_id'] == 0) {
				$menu[$n['id']]['main'] = $n;
				continue;
			}

			$menu[$n['parent_id']][] = $n;
		}

		return $menu;
	}

	/**
	 * Get all names of menus and submenus
	 *
	 * @return mixed array of items or false
	 */
	public static function getRaw() {
		return Db::query("SELECT name_".Lang::getLang()." AS name, id FROM menu");
	}

	/**
	 * Get all parents (main items)
	 *
	 * @return mixed array of items or false
	 */
	public static function getParents() {
		return Db::query("SELECT id, name_".Lang::getLang()." AS name
				FROM menu WHERE parent_id=0");
	}

	/**
	 * Get one menu entry
	 *
	 * @param int $id item id
	 * @return mixed array or false
	 */
	public static function getItem($id) {
		return Db::queryRow("SELECT parent_id, name_".Lang::getLang()." AS name
			FROM menu WHERE id=?", array($id));
	}

	/**
	 * Get name of given menu item
	 *
	 * @param int $id category id
	 * @return mixed string or false
	 */
	public static function getName($id) {
		$res = Db::queryRow("SELECT name_".Lang::getLang()." AS name FROM
				  menu  WHERE id=?", array($id));

		if (!$res)
			return false;
		return $res['name'];
	}

	/**
	 * Add new menu item
	 *
	 * @param string $name entry name
	 * @param int $parent parent id
	 *
	 * @return bool true if succeed or false
	 */
	public static function addItem($name, $parent = 0) {
		if (!self::_check($name, $parent))
			return false;

		$res = Db::insert("menu", array('parent_id' => $parent,
					'name_'.Lang::getLang() => $name));
		if (!$res) {
			Message::add(Lang::get("DB_UNABLE_SAVE"));
			return false;
		}

		Message::add(Lang::get("SAVED"));
		return true;
	}

	/**
	 * Edit menu item
	 *
	 * @param int $id item id
	 * @param string $name entry name
	 * @param int $parent parent id
	 *
	 * @return bool true if succeed or false
	 */
	public static function modifyItem($id, $name, $parent) {
		if (!self::_check($name, $parent))
			return false;

		if (!self::itemExists($id)) {
			Message::add(Lang::get('MENU_NO_ITEM'));
			return false;
		}

		Db::update("menu", array('id' => $id), array('parent_id' => $parent,
					'name_'.Lang::getLang() => $name));
		//TODO:should check if suceed, but update returns false when data was same as stored ones
		Message::add(Lang::get("SAVED"));
		return true;
	}

	/**
	 * Delete menu item
	 *
	 * @param int $id item id
	 * @return bool true if succeed or false
	 */
	public static function deleteItem($id) {
		if (!self::itemExists($id)) {
			Message::add(Lang::get('MENU_NO_ITEM'));
			return false;
		}

		if (!Db::remove("menu", array('id' => $id))) {
			Message::add(Lang::get('MENU_DEL_ERR'));
			return false;
		}

		Message::add(Lang::get('MENU_DELETED'));
		return true;
	}

	/**
	 * Check if given item exists
	 *
	 * @param int $id item id
	 * @return bool true if exists, false otherwise
	 */
	public static function itemExists($id) {
		$res = Db::queryRow("SELECT COUNT(*) FROM menu WHERE id=?", array($id));
		if ($res == false || $res['COUNT(*)'] < 1)
			return false;

		return true;
	}

	/**
	 * Check if data are valid
	 *
	 * @param string $name entry name
	 * @param int $parent parent id
	 *
	 * @return bool true if ok, false otherwise
	 */
	private static function _check($name, $parent) {
		$ret = true;
		if (strlen($name) < MENU_LENGTH_MIN) {
			Message::add(Lang::get("MENU_SHORT"));
			$ret = false;
		} else if (strlen($name) > MENU_LENGTH_MAX) {
			Message::add(Lang::get("MENU_LONG"));
			$ret = false;
		}
		if ($parent != 0 && !self::itemExists($parent)) {
			Message::add(Lang::get("MENU_NO_PARENT"));
			$ret = false;
		}

		return $ret;
	}
}
