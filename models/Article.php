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
 * Article manager
 */
class Article
{
	/**
	 * Get article content
	 *
	 * @param int $id article id
	 * @param boolean $expand if true, expand text between []
	 * @return mixed false or array of article content
	 */
	public static function getArticle($id, $expand=true) {
		$ret = Db::queryRow("SELECT a.id,l.title,l.description,l.content,l.keywords,l.url,a.date_created as date FROM
		       	  articles_".Lang::getLang()." AS l JOIN articles AS a ON l.id=a.id WHERE l.id=?", array($id));
		if (!$expand)
			return $ret;

		if (!$ret)
			return false;

		$ret['content'] = self::_expandPaths($id, $ret['content']);
		return $ret;
	}

	/**
	 * Get article name
	 *
	 * @param int $id article id
	 * @return mixed false or string
	 */
	public static function getName($id) {
		$ret = Db::queryRow("SELECT title FROM articles_".Lang::getLang()." WHERE id=?", array($id));
		if (!$ret)
			return false;
		return $ret['title'];
	}

	/**
	 * Get category of given article
	 *
	 * @param int $id article id
	 * @return mixed false or category id
	 */
	public static function getCategory($id) {
		$ret = Db::queryRow("SELECT menu_id FROM articles WHERE id=?", array($id));
		if (!$ret)
			return false;

		return $ret['menu_id'];
	}

	/**
	 * Get article url
	 *
	 * @param int $id article id
	 * @return mixed false or url
	 */
	public static function getUrl($id) {
		$ret = Db::queryRow("SELECT url FROM articles_".Lang::getLang()." WHERE id=?", array($id));
		if (!$ret)
			return false;
		return $ret['url'];
	}

	/**
	 * Does this article translation exists?
	 *
	 * @param int $id article id
	 * @return boolean true if exists
	 */
	public static function exists($id) {
		$ret = Db::queryRow("SELECT COUNT(*) FROM articles_".Lang::getLang()." WHERE id=?", array($id));
		if (!$ret)
			return false;

		$count = $ret['COUNT(*)'];
		if ($count > 0)
			return true;
		return false;
	}

	/**
	 * Does this article exists regardless of languages?
	 *
	 * @param int $id article id
	 * @return boolean true if exists
	 */
	public static function existsIgnoreLang($id) {
		$ret = Db::queryRow("SELECT COUNT(*) FROM articles WHERE id=?", array($id));
		if (!$ret)
			return false;
		if ($ret['COUNT(*)'])
			return true;
		return false;
	}

	/**
	 * Get article translations
	 *
	 * @param int $id article id
	 * @return array of language names
	 */
	public static function translAvailable($id) {
		$query = Db::query("SHOW TABLES LIKE 'articles\_%'");
		if (!$query)
			return array();

		$array = array();

		foreach ($query as $q) {
			foreach ($q as $p) {
				if (Db::queryRow("SELECT COUNT(*) FROM $p WHERE id=$id")['COUNT(*)'] != 0)
					$array[] = explode('_', $p)[1];
			}
		}
		return $array;
	}

	/**
 	 * Get one page of articles
	 *
	 * @param int $from first item position
	 * @param int $item number of elements to return
	 * @param int $cat_id if set, return only articles of given category
	 *
	 * @return mixed array of articles or false
	 */
	public static function getPage($from, $items, $cat_id = false) {
		$where = "";
		$in = self::_getIn($cat_id);
		if ($in)
			$where .= "WHERE a.menu_id IN ($in)";

		return Db::query("SELECT l.title,l.description,l.id,l.url,a.date_created as date
				  FROM articles_".Lang::getLang()." AS l JOIN articles AS a ON
				  l.id=a.id $where ORDER BY a.id DESC LIMIT ?, ?", array($from, $items));
	}

	/**
	 * Count articles
	 *
	 * @param mixed $cat_id if not false, count only articles
	 *		of given menu_id and its submenus
	 * @return mixed false or array
	 */
	public static function countAll($cat_id = false) {
		$where = "";
		$in = self::_getIn($cat_id);
		if ($in)
			$where .= "WHERE a.menu_id IN ($in)";

		$ret = Db::queryRow("SELECT COUNT(*) FROM articles_".Lang::getLang()." AS l JOIN articles AS a ON
				l.id = a.id $where");

		if (!$ret)
			return false;
		return $ret['COUNT(*)'];
	}

	/**
	 * Modify article
	 *
	 * @param int $id
	 * @param string $name
	 * @param string $description
	 * @param string $keywords
	 * @param string $content
	 * @param int $category
	 *
	 * @return boolean true if succeed
	 */
	public static function modify($id, $name, $description, $keywords, $content, $category) {
		if (!self::existsIgnoreLang($id)) {
			Message::add(Lang::get("NO_ARTICLE"));
			return false;
		}

		if (!self::_check($name, $description, $keywords))
			return false;

		$url = self::_prepareLink($name);

		/* this translation doesn't exist */
		if (!self::exists($id)) {
			if (!self::_addTranslation($id, $url, $name, $description, $keywords, $content)) {
				Message::add(Lang::get("DB_UNABLE_SAVE"));
				return false;
			}
		} else {
			//I'd like to check return value, but if data are the same, it returns 0, so it's a problem
			Db::update("articles_".Lang::getLang(), array('id' => $id), array('title' => $name,
						'url' => $url,
						'description' => $description,
						'keywords' => $keywords,
						'content' => $content));
		}

		//same problem
		Db::update("articles", array('id' => $id), array('menu_id' => $category,
					'date_modified' => date('Y-m-d H:i:s')));

		Message::add(Lang::get("SAVED"));
		return true;
	}

	/**
	 * Add new article
	 *
	 * @param string $name
	 * @param string $description
	 * @param string $keywords
	 * @param string $content
	 * @param int $category
	 * @param int $permissions for comments (@see Comments::setPermissions)
	 *
	 * @return mixed false or new article id
	 */
	public static function add($name, $description, $keywords, $content, $category, $permissions) {
		if (!self::_check($name, $description, $keywords))
			return false;

		$url = self::_prepareLink($name);

		$affected = Db::insert("articles", array('menu_id' => $category));
		if (!$affected) {
			Message::add(Lang::get("DB_UNABLE_SAVE"));
			return false;
		}

		$id = Db::queryRow("SELECT id FROM articles ORDER BY id DESC LIMIT 1");
		if ($id == false) {
			Message::add(Lang::get("DB_UNABLE_SAVE"));
			//TODO should delete last row, but how?
			return false;
		}
		$id = $id['id'];

		if (!self::_addTranslation($id, $url, $name, $description, $keywords, $content)) {
			Message::add(Lang::get("DB_UNABLE_SAVE"));
			return false;
		}

		Comments::setPermissions($id, $permissions);
		Files::mvDir(UPLOAD_ARTICLE_TMP, UPLOAD_ARTICLE.$id."/", true);
		Rss::gen();

		Message::add(Lang::get("SAVED"));
		return $id;
	}

	/**
	 * Delete article from database
	 */
	public static function remove($id) {
		if (!self::exists($id)) {
			Message::add(Lang::get("NO_ARTICLE"));
			return false;
		}

		Db::remove("articles_".Lang::getLang(), array('id' => $id));
		if (!self::translAvailable($id))
			Db::remove("articles", array('id' => $id));

		Files::remove(UPLOAD_ARTICLE.$id);
		Message::add(Lang::get("ARTICLE_DELETED"));
		return true;
	}

	/**
	 * Generate list of ids of menu_id and submenus
	 *
	 * @param mixed $cat_id if not false, get only ids
	 *		of given menu_id and its submenus
	 * @return mixed false or array
	 */
	private static function _getIn($cat_id) {
		if ($cat_id) {
			$id = Db::queryRow("SELECT parent_id FROM menu WHERE id=?", array($cat_id));
			if (!$id)
				return false;

			$id = $id['parent_id'];
			if ($id != 0)
				$id = -1;
			else
				$id = $cat_id;

			$res = Db::query("SELECT id FROM menu WHERE parent_id=? OR id=?", array($id, $cat_id));
			if (!$res)
				return false;

			$n = '';
			foreach ($res as $r) {
				$n .= $r['id'].",";
			}
			$n = substr($n, 0, -1);
			return $n;
		}
		return false;
	}

	/**
	 * Check variables to add them to database
	 *
	 * @param string $name
	 * @param string $description
	 * @param string $keywords
	 * @return boolean true if correct
	 */
	private static function _check($name, $description, $keywords) {
		$err = true;
		if (strlen($name) > TITLE_LENGTH_MAX) {
			Message::add(Lang::get("TITLE_LONG"));
			$err = false;
		} else if (strlen($name) < TITLE_LENGTH_MIN) {
			Message::add(Lang::get("TITLE_SHORT"));
			$err = false;
		}
		if (strlen($description) > DESC_LENGTH) {
			Message::add(Lang::get("DESCRIPTION_LONG"));
			$err = false;
		}
		if (strlen($keywords) > KEYWORDS_LENGTH) {
			Message::add(Lang::get("KEYWORDS_LONG"));
			$err = false;
		}

		return $err;
	}

	/**
	 * Add article translation to database
	 *
	 * @param int $id
	 * @param string $url
	 * @param string $name
	 * @param string $description
	 * @param string $keywords
	 * @param string $content
	 *
	 * @return boolean true if succeed
	 */
	private static function _addTranslation($id, $url, $name, $description, $keywords, $content) {
		return Db::insert("articles_".Lang::getLang(), array('id' => $id,
					'url' => $url,
					'title' => $name,
					'description' => $description,
					'keywords' => $keywords,
					'content' => $content));
	}

	/**
	 * Expand [file] to upload_dir/file
	 *
	 * @param int $id article id
	 * @param string $string
	 * @return string
	 */
	private static function _expandPaths($id, $string) {
		$path = Url::getBase()."/".UPLOAD_ARTICLE."$id";

		$string = preg_replace_callback("/(?<=<pre>).*?(?=<\/pre>)/is", function($m) { return htmlspecialchars($m[0]);}, $string);
		return preg_replace("/(\[(.*?)\])(?!>|[^<>]*<\/pre)/", "$path/$2", $string);
	}

	/**
	 * Prepare link to add to database
	 *
	 * Delete all non alphanumeric characters and replace spaces with _
	 *
	 * @param string $string
	 * @return mixed NULL or string
	 */
	private static function _prepareLink($string) {
		$text = self::_strtr_utf8($string, 'áäčďéěëíµňôóöŕřšťúůüýžÁÄČĎÉĚËÍĄŇÓÖÔŘŔŠŤÚŮÜÝŽ', 'aacdeeeilnooorrstuuuyzaacdeeelinooorrstuuuyz');
		$text = preg_replace("/[^a-zA-z0-9\s]/", "", $text);
		return preg_replace("/[^a-zA-Z0-9]/", "_", $text);
	}

	/**
	 * Strtr utf8 function found somewhere on the internet
	 */
	private static function _strtr_utf8($str, $from, $to) {
		$keys = array();
		$values = array();
		preg_match_all('/./u', $from, $keys);
		preg_match_all('/./u', $to, $values);
		$mapping = array_combine($keys[0], $values[0]);
		return strtr($str, $mapping);
	}
}
