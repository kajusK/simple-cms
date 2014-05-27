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
 * Menu administration
 */
class AdminMenuController extends Controller
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
	 * Show simple menu of menu entries
	 */
	private function _list() {
		$this->view = "admin/menu_list";

		$menu = Menu::get();
		if (!$menu)
			return;

		foreach ($menu as & $m) {
			foreach ($m as & $i) {
				$i['link'] = Url::get("admin", "menu", "edit", $i['id']);
				$i['del'] = Url::get("admin", "menu", "delete", $i['id']);
			}
		}
		$this->data['menu'] = $menu;
		$this->data['add_msg'] = Lang::get('MENU_ADD');
		$this->data['menu_edit'] = Lang::get('EDIT_MENU');
		$this->data['add_link'] = Url::get("admin", "menu", "add");
	}

	/**
	 * Modify menu entry
	 *
	 * @param array $param menu id
	 */
	private function _edit($param) {
		if (count($param) != 1 || !is_numeric($param[0])) {
			$this->_notFound();
			return;
		}
		$id = $param[0];
		if (!Menu::itemExists($id)) {
			Message::add(Lang::get("MENU_NO_ITEM"));
			return;
		}

		self::_menuCommon($id);

		if (isset($_POST['name'])) {
			if (Menu::modifyItem($id, $_POST['name'], $_POST['parent'])) {
				$this->view = false;
				$this->redirect(Url::get("admin", "menu"), 2);
			}
			return;
		}

		$item = Menu::getItem($id);
		if (!$item) {
			$item = array('name' => "",
				'parent_id' => "");
		}
		$this->data['name'] = $item['name'];
		$this->data['cur_parent'] = $item['parent_id'];
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
		self::_menuCommon();

		if (isset($_POST['name'])) {
			if (Menu::addItem($_POST['name'], $_POST['parent'])) {
				$this->view = false;
				$this->redirect(Url::get("admin", "menu"), 2);
			}
			return;
		}

		$this->data['name'] = "";
		$this->data['cur_parent'] = "";
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

		$name = Menu::getName($id);
		if (!$name) {
			$this->_notFound(Lang::get("MENU_NO_ITEM"));
			return;
		}

		$this->data = array('delete_msg' => Lang::get("MENU_DELETE_ITEM", $name),
				'yes' => Lang::get("YES"),
				'no' => Lang::get("NO"),
				'confirm' => Lang::get("CONFIRM_DELETE"),
				'send' => Lang::get("SEND"));

		if (isset($_POST['delete']) && $_POST['delete'] == "yes") {
			if (Menu::deleteItem($id)) {
				$this->view = false;
				$this->redirect(Url::get("admin","menu"), 2);
			}
			return;
		}
	}

	/**
	 * Common method for menu edit and add
	 *
	 * @param int $id menu item id or false if adding new
	 */
	private function _menuCommon($id = false) {
		$this->view = "admin/menu";
		$this->data = array('menu_edit' => Lang::get("EDIT_MENU"),
				'menu_name' => Lang::get("MENU_NAME"),
				'menu_parent' => Lang::get("MENU_PARENT"),
				'menu_no_parent' => Lang::get("MENU_MAIN"),
				'send' => Lang::get("SEND"));

		if (isset($_POST['name'])) {
			$this->data['name'] = $_POST['name'];
			$this->data['cur_parent'] = $_POST['parent'];
		} else {
			$this->data['cur_parent'] = 0;
		}

		$parents = Menu::getParents();
		foreach ($parents as $i => $n) {
			if ($n['id'] == $id) {
				unset($parents[$i]);
			}
			if ($n['name'] == "") {
				unset($parents[$i]);
			}
		}
		$this->data['parents'] = $parents;
	}
}
