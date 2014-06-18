<?php
/**
 * Yet another simple CMS
 *
 * @copyright 2014 Jakub Kaderka
 * @license GNU General Public License, version 2; see LICENSE.txt
 */

//no direct access
defined("IN_CMS") or die("Unauthorized access");

class Serial
{
	/**
	 * Get all serials
	 *
	 * @return mixed false or array
	 */
	public static function getAll() {
		return Db::query("SELECT name_".Lang::getLang()." AS name, id FROM `serial`");
	}

	/**
	 * Get id of serial for given article
	 *
	 * @param int $id article id
	 * @return mixed false or id
	 */
	public static function getId($id) {
		$res = Db::queryRow("SELECT serial_id FROM `articles` WHERE id=?", array($id));
		if (!$res)
			return false;
		return $res['serial_id'];
	}

	/**
	 * Add new serial item
	 *
	 * @param string $name entry name
	 *
	 * @return bool true if succeed or false
	 */
	public static function addItem($name) {
		if (!self::_check($name))
			return false;

		$res = Db::insert("serial", array('name_'.Lang::getLang() => $name));
		if (!$res) {
			Message::add(Lang::get("DB_UNABLE_SAVE"));
			return false;
		}

		Message::add(Lang::get("SAVED"));
		return true;
	}

	/**
	 * Edit item
	 *
	 * @param int $id item id
	 * @param string $name entry name
	 *
	 * @return bool true if succeed or false
	 */
	public static function modifyItem($id, $name) {
		if (!self::_check($name))
			return false;

		if (!self::itemExists($id)) {
			Message::add(Lang::get('SERIAL_NO_ITEM'));
			return false;
		}

		Db::update("serial", array('id' => $id), array('name_'.Lang::getLang() => $name));
		//TODO:should check if suceed, but update returns false when data was same as stored ones
		Message::add(Lang::get("SAVED"));
		return true;
	}

	/**
	 * Delete item
	 *
	 * @param int $id item id
	 * @return bool true if succeed or false
	 */
	public static function deleteItem($id) {
		if (!self::itemExists($id)) {
			Message::add(Lang::get('SERIAL_NO_ITEM'));
			return false;
		}

		if (!Db::remove("serial", array('id' => $id))) {
			Message::add(Lang::get('SERIAL_DEL_ERR'));
			return false;
		}

		Message::add(Lang::get('SERIAL_DELETED'));
		return true;
	}

	/**
	 * Check if given item exists
	 *
	 * @param int $id item id
	 * @return bool true if exists, false otherwise
	 */
	public static function itemExists($id) {
		$res = Db::queryRow("SELECT COUNT(*) FROM serial WHERE id=?", array($id));
		if ($res == false || $res['COUNT(*)'] < 1)
			return false;

		return true;
	}

	/**
	 * Check if data are valid
	 *
	 * @param string $name entry name
	 *
	 * @return bool true if ok, false otherwise
	 */
	private static function _check($name) {
		$ret = true;
		if (strlen($name) < SERIAL_LENGTH_MIN) {
			Message::add(Lang::get("SERIAL_SHORT"));
			$err = false;
		} else if (strlen($name) > SERIAL_LENGTH_MAX) {
			Message::add(Lang::get("SERIAL_LONG"));
			$err = false;
		}

		return $ret;
	}
}
