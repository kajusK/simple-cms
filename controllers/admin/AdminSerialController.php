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
 * Serials administration
 */
class AdminSerialController extends Controller
{
	/**
	 * @param array $param add/edit/delete id
	 */
	public function __construct($param)
	{
		if (count($param) == 0) {
			self::_list();
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
	 * Show list of serials
	 */
	private function _list() {
		$this->view = "admin/serial_list";

		$serial = Serial::getAll();
		if (!$serial)
			$serial = array();

		foreach ($serial as & $s) {
			$s['link'] = Url::get("admin", "serial", "edit", $s['id']);
			$s['del'] = Url::get("admin", "serial", "delete", $s['id']);
		}
		$this->data['serial'] = $serial;
		$this->data['add_msg'] = Lang::get('SERIAL_ADD');
		$this->data['serial_edit'] = Lang::get('SERIAL_EDIT');
		$this->data['add_link'] = Url::get("admin", "serial", "add");
	}

	/**
	 * Modify serial
	 *
	 * @param array $param serial id
	 */
	private function _edit($param) {
		if (count($param) != 1 || !is_numeric($param[0])) {
			$this->_notFound();
			return;
		}
		$id = $param[0];
		if (!Serial::itemExists($id)) {
			Message::add(Lang::get("SERIAL_NO_ITEM"));
			return;
		}

		self::_common($id);

		if (isset($_POST['name'])) {
			if (Serial::modifyItem($id, $_POST['name'])) {
				$this->view = false;
				$this->redirect(Url::get("admin", "serial"), 2);
			}
			return;
		}

		$this->data['name'] = Serial::getName($id);
	}

	/**
	 * Add new menu item
	 *
	 * @param array $param must be empty
	 */
	private function _add($param) {
		if (count($param) != 0) {
			$this->_notFound();
			return;
		}
		self::_common();

		if (isset($_POST['name'])) {
			if (Serial::addItem($_POST['name'])) {
				$this->view = false;
				$this->redirect(Url::get("admin", "serial"), 2);
			}
			return;
		}

		$this->data['name'] = "";
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

		$name = Serial::getName($id);
		if (!$name) {
			$this->_notFound(Lang::get("SERIAL_NO_ITEM"));
			return;
		}

		$this->data = array('delete_msg' => Lang::get("SERIAL_DELETE_ITEM", $name),
				'yes' => Lang::get("YES"),
				'no' => Lang::get("NO"),
				'confirm' => Lang::get("CONFIRM_DELETE"),
				'send' => Lang::get("SEND"));

		if (isset($_POST['delete']) && $_POST['delete'] == "yes") {
			if (Serial::deleteItem($id)) {
				$this->view = false;
				$this->redirect(Url::get("admin","serial"), 2);
			}
			return;
		}
	}

	/**
	 * Common method for serial edit and add
	 *
	 * @param int $id serial item id or false if adding new
	 */
	private function _common($id = false) {
		$this->view = "admin/serial";
		$this->data = array('serial_edit' => Lang::get("SERIAL_EDIT"),
				'serial_name' => Lang::get("SERIAL_NAME"),
				'send' => Lang::get("SEND"));

		if (isset($_POST['name'])) {
			$this->data['name'] = $_POST['name'];
		}
	}
}
