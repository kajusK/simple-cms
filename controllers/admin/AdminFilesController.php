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
		case "new":
			$this->_new($param);
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

		$target = UPLOAD_ARTICLE.$param[0]."/";
		$this->_common($target);
	}

	/**
	 * Upload files for new article - into temporary directory
	 *
	 * @param array $param nothing
	 */
	private function _new($param) {
		if (count($param) != 0) {
			$this->_notFound();
			return;
		}

		$target = UPLOAD_ARTICLE_TMP;
		$this->_common($target);
	}

	/**
	 * Common function for new and article
	 *
	 * @param string $target directory to save files to
	 */
	private function _common($target) {
		$this->view = "admin/files";
		$this->data = array('upload' => Lang::get("UPLOAD_FILES"),
				'files_edit' => Lang::get("EDIT_FILES"),
				'new_dir' => Lang::get("NEW_DIR"),
				'send' => Lang::get("SEND"),
				'name' => Lang::get("FILENAME"),
				'delete' => Lang::get("DELETE"),
				'dir_selected' => "./");

		if (isset($_POST['upload']))
			$this->_upload($target);
		else if (isset($_POST['new_dir']))
			$this->_newDir($target);
		else if (isset($_POST['delete']))
			$this->_delete($target);

		$ret = Files::getFiles($target, true);
		if (count($ret['dirs']) == 0)
			$ret['dirs'][] = './';
		$this->data['dirs'] = $ret['dirs'];
		$this->data['files'] = $ret['files'];
	}

	/**
	 * Create new subdir
	 *
	 * @param string $target target directory
	 */
	private function _newDir($target) {
		Files::newDir($target.$_POST['dir']);
	}

	/**
	 * Upload all selected files
	 *
	 * @param string $target target directory
	 */
	private function _upload($target) {
		$this->data['dir_selected'] = $_POST['dir'];
		Files::upload($target.$_POST['dir'], $_FILES['upload']);
	}

	/**
	 * Delete files for article function
	 *
	 * @param string $target target directory
	 */
	private function _delete($target) {
		if (!isset($_POST['files']))
			return;

		foreach ($_POST['files'] as $f=>$r) {
			if ($r == "on")
				Files::remove($target.$f);
		}
	}
}
