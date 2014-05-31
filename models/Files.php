<?php
/**
 * Yet another simple CMS
 *
 * @copyright 2014 Jakub Kaderka
 * @license GNU General Public License, version 2; see LICENSE.txt
 */

//no direct access
defined("IN_CMS") or die("Unauthorized access");

class Files
{
	/**
	 * Upload files
	 *
	 * @param string $dir to save uploaded files to
	 * @param array $files files in $_FILES like structure (for multiple files!)
	 *
	 * @return boolean
	 */
	public static function upload($dir, $files) {
		if ($files['name'][0] == "")
			return true;
		if (!is_dir($dir) && !self::newDir($dir))
			return false;

		$ret = true;
		for ($i = 0; $i < count($files['name']); $i++) {
			if ($files['error'][$i] != 0) {
				$ret = false;
				continue;
			}
			$file = self::_escapeName($files['name'][$i]);
			if (file_exists($dir.$file)) {
				Message::add(Lang::get("FILE_EXISTS", $file));
				continue;
			}
			if (!move_uploaded_file($files['tmp_name'][$i], $dir.$file))
				$ret = false;
		}

		if (!$ret) {
			Message::add(Lang::get("UPLOAD_ERROR"));
			return false;
		}
		Message::add(Lang::get("UPLOAD_FINISHED"));
		return true;
	}

	/**
	 * Delete file or directory
	 *
	 * @param string $filename
	 * @return boolean
	 */
	public static function remove($filename) {
		if (is_file($filename))
			return unlink($filename);
		else if (is_dir($filename))
			return self::_rmDir($filename);
		return true;
	}

	/**
	 * Create new directory (or more directories if parent doesn't exist)
	 *
	 * @param string $target path
	 * @return boolean
	 */
	public static function newDir($target) {
		if (is_dir($target) || mkdir($target, 0777, true)) {
			Message::add(Lang::get('DIR_CREATED', $target));
			return true;
		}
		Message::add(Lang::get('DIR_CREATE_FAILED'));
		return false;
	}

	/**
	 * Move directory
	 *
	 * @param string $source path to source dir
	 * @param string $target path to target dir
	 * @param boolean $replace if true and target exists, rewrite it, else return false
	 *
	 * @return boolean true if succeed or source doesn't exist/isn't dir
	 */
	public static function mvDir($source, $target, $replace = false) {
		if (!is_dir($source))
			return true;
		if (file_exists($target)) {
			if ($replace && !self::remove($target))
				return false;
		}
		return rename($source, $target);
	}

	/**
	 * Get array of subdirs (and files)
	 *
	 * @param string $dir path to source directory
	 * @param string $prefix prefix to add before dirname
	 *
	 * @return array of dirs (including ./)
	 */
	public static function getFiles($dir, $dirs = false, $prefix = './') {
		if (!is_dir($dir) || !($obj = scandir($dir))) {
			if ($dirs)
				return array('files' => array(), 'dirs' => array());
			return array();
		}

		$array['dirs'] = array($prefix);
		$array['files'] = array();
		foreach($obj as $o) {
			if ($o == "." || $o == "..")
				continue;
			$str = $dir."/".$o;
			if (is_dir($str))
				$array = array_merge_recursive($array, self::getFiles($str, $dirs, $prefix.$o."/"));
			else if (is_file($str))
				$array['files'][] = $prefix.$o;
		}
		if ($dirs)
			return $array;
		return $array['files'];
	}


	/**
	 * Remove directory
	 *
	 * @param source $dir directory to delete
	 * @return boolean
	 */
	private static function _rmDir($dir) {
		if (!($obj = scandir($dir)))
			return false;
		foreach($obj as $o) {
			if ($o != "." && $o != "..") {
				if (filetype($dir."/".$o) == "dir")
					self::_rmDir($dir."/".$o);
				else
					unlink($dir."/".$o);
			}
		}
		return rmdir($dir);
	}

	/**
	 * Escape filename
	 *
	 * @param string $file
	 * @return string escaped filename
	 */
	private static function _escapeName($file) {
		return str_replace("/", "_", $file);
	}
}
