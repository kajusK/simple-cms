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
	 */
	public static function upload($dir, $files) {
		if (!is_dir($dir) && !self::_createDir($dir))
			return false;

		for ($i = 0; $i < count($files['name']); $i++) {
			if ($files['error'][$i] != 0)
				continue;
			$file = self::_escapeName($files['name'][$i]);
			if (file_exists($dir.$file)) {
				Message::add(Lang::get("FILE_EXISTS", $file));
				continue;
			}

			move_uploaded_file($files['tmp_name'][$i], $dir.$file);
		}
		Message::add(Lang::get("UPLOAD_FINISHED"));
	}

	/**
	 * Delete file
	 *
	 * @param string $filename
	 * @return boolean true if file exists no more
	 */
	public static function remove($filename) {
		if (is_file($filename))
			return unlink($filename);
		return true;
	}

	/**
	 * Get all filenames in directory
	 * Directories are ignored
	 *
	 * @param string $dirname
	 * @return mixed false or array of filenames
	 */
	public static function getFilenames($dirname) {
		if (!is_dir($dirname))
			return false;

		$files = array();
		if (!($handle = opendir($dirname)))
			return false;

		while (($entry = readdir($handle)) !== false) {
			if ($entry != "." && $entry != ".." && !is_dir($dirname.$entry))
				$files[] = $entry;
		}
		closedir($handle);

		return $files;
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
			if ($replace && !self::removeDir($target))
				return false;
		}
		return rename($source, $target);
	}

	/**
	 * Remove directory
	 *
	 * @param source $dir directory to delete
	 * @return boolean
	 */
	public static function removeDir($dir) {
		if (!is_dir($dir))
			return false;
		$obj = scandir($dir);
		if (!$obj)
			return false;
		foreach($obj as $o) {
			if ($o != "." && $o != "..") {
				if (filetype($dir."/".$o) == "dir")
					self::removeDir($dir."/".$o);
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

	/**
	 * Create dir if doesn't exist
	 *
	 * @param string $dir
	 * @return boolean
	 */
	private static function _createDir($dir) {
		if (is_file($dir))
			return false;
		if (is_dir($dir))
			return true;

		return mkdir($dir, 0777, true);
	}
}
