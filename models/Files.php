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
	 * Escape filename
	 *
	 * @param string $file
	 * @return string escaped filename
	 */
	private static function _escapeName($file) {
		return str_replace("/", "_", $file);
	}
}
