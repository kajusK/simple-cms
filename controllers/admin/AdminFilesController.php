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
 * Files administration
 */
class AdminFilesController extends Controller
{
	/**
	 * @param array $param action id
	 */
	public function __construct($param) {
		if (count($param) == 0) {
			$this->_notFound();
			return;
		}

		$action = array_shift($param);
		switch ($action) {
		case "article":
			$this->_article($param);
			break;
		default:
			$this->_notFound();
			break;
		}
	}

	/**
	 * Upload files for article
	 *
	 * @param array $param article_id
	 */
	private function _article($param) {
		if (count($param) != 1 || !is_numeric($param[0]) || !Article::existsIgnoreLang($param[0])) {
			$this->_notFound();
			return;
		}
		$id = $param[0];

		$this->view = "admin/files";
		$this->data = array('upload' => Lang::get("UPLOAD_FILES"),
				'files_edit' => Lang::get("EDIT_FILES"),
				'send' => Lang::get("SEND"),
				'name' => Lang::get("FILENAME"),
				'delete' => Lang::get("DELETE"));

		$target = UPLOAD_ARTICLE."$id/";

		if (isset($_POST['upload']))
			Files::upload($target, $_FILES['upload']);
		else if (isset($_POST['delete']))
			$this->_delete($target);

		$this->data['files'] = Files::getFilenames($target);
	}

	/**
	 * Delete files for article function
	 *
	 * @param string $target target directory
	 */
	private function _delete($target) {
		foreach ($_POST['files'] as $f=>$r) {
			if ($r == "on")
				Files::remove($target.$f);
		}
	}
}
