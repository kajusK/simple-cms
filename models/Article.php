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
	 * @return mixed false or array of article content
	 */
	public function getArticle($id) {
		return Db::queryRow("SELECT a.id,l.title,l.description,l.content,l.keywords,l.url,a.date_created as date FROM
		       	  articles_".Lang::getLang()." AS l JOIN articles AS a ON l.id=a.id WHERE l.id=?", array($id));
	}

	/**
	 * Get article name
	 *
	 * @param int $id article id
	 * @return mixed false or string
	 */
	public function getName($id) {
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
	public function getCategory($id) {
		$ret = Db::queryRow("SELECT menu_id FROM articles WHERE id=?", array($id));
		if (!$ret)
			return false;

		return $ret['menu_id'];
	}

	/**
	 * Does this article exists?
	 *
	 * @param int $id article id
	 * @return boolean true if exists
	 */
	public function exists($id) {
		$ret = Db::queryRow("SELECT COUNT(*) FROM articles_".Lang::getLang()." WHERE id=?", array($id));
		if (!$ret)
			return false;

		$count = $ret['COUNT(*)'];
		if ($count > 0)
			return true;
	}

	/**
	 * Get article translations
	 *
	 * @param int $id article id
	 * @return array of language names
	 */
	public function translAvailable($id) {
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
	public function getPage($from, $items, $cat_id = false) {
		$where = "";

		$in = self::_getIn($cat_id);
		if ($in)
			$where = "WHERE a.menu_id IN ($in)";

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
	public function countAll($cat_id = false) {
		$where = "";
		$in = self::_getIn($cat_id);
		if ($in)
			$where = "WHERE a.menu_id IN ($in)";

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
	public function modify($id, $name, $description, $keywords, $content, $category) {
		if (!self::exists($id)) {
			Message::add(Lang::get("NO_ARTICLE"));
			return false;
		}

		if (!self::_check($name, $description, $keywords))
			return false;

		//I'd like to check return value, but if data are the same, it returns 0, so it's a problem
		Db::update("articles_".Lang::getLang(), array('id' => $id), array('title' => $name,
					'description' => $description,
					'keywords' => $keywords,
					'content' => $content));

		//same problem
		Db::update("articles", array('id' => $id), array('menu_id' => $category,
					'date_modified' => date ('Y-m-d H:m')));

		Message::add(Lang::get("SAVED"));
		return true;
	}

	public function add($name, $description, $keywords, $content, $category) {
		if (!self::_check($name, $description, $keywords))
			return false;

		$url = htmlspecialchars($name);
		//regular expression TODO:
		if (strlen($url) > URL_LENGTH)
			$url = substr($url, 0, URL_LENGTH);

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

		$affected = Db::insert("articles_".Lang::getLang(), array('id' => $id,
					'url' => $url,
					'title' => $name,
					'description' => $description,
					'keywords' => $keywords,
					'content' => $content));
		if (!$affected) {
			Message::add(Lang::get("DB_UNABLE_SAVE"));
			return false;
		}

		Message::add(Lang::get("SAVED"));
		return true;
	}

	/**
	 * Delete article from database
	 */
	public function remove($id) {
		if (!self::exists($id)) {
			Message::add(Lang::get("NO_ARTICLE"));
			return false;
		}

		Db::remove("articles_".Lang::getLang(), array('id' => $id));
		if (!self::translAvailable($id))
			Db::remove("articles", array('id' => $id));

		Message::add("ARTICLE_DELETED");
		return true;
	}

	/**
	 * Generate list of ids of menu_id and submenus
	 *
	 * @param mixed $cat_id if not false, get only ids
	 *		of given menu_id and its submenus
	 * @return mixed false or array
	 */
	private function _getIn($cat_id) {
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
	private function _check($name, $description, $keywords) {
		$err = true;
		if (strlen($name) > TITLE_LENGTH) {
			Message::add(Lang::get("TITLE_LONG"));
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
}
