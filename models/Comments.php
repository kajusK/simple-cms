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
 * Comments manager
 */
class Comments
{
	/**
	 * Get all comments for given article
	 *
	 * @param int $article_id
	 * @return mixed array of comments or false
	 */
	public static function getAll($article_id) {
		return Db::query("SELECT id, nickname, text, date, article_id FROM comments WHERE
				article_id=? AND visible=TRUE", array($article_id));
	}

	/**
	 * Get one comments
	 *
	 * @param int $comment_id
	 * @return mixed comment array or false
	 */
	public static function getOne($comment_id) {
		return Db::queryRow("SELECT id, nickname, text, date, article_id FROM comments WHERE
				visible=TRUE AND id=?", array($comment_id));
	}

	/**
	 * Add comment to database
	 *
	 * @param int $article_id
	 * @param string $nick
	 * @param string $text
	 * @param string $year must be current year
	 * @return boolean true if suceed
	 */
	public static function add($article_id, $nick, $text, $year) {
		if (self::allowed($article_id) <= 0) {
			Message::add(Lang::get("COMMENTS_NOT_ALLOWED"));
			return true;
		}

		$err = false;
		if ($year != date("Y")) {
			Message::add(Lang::get("INCORRECT_ANTISPAM"));
			$err = true;
		}

		if (strlen($nick) < 3) {
			Message::add(Lang::get("NICK_SHORT", 3));
			$err = true;
		} else if (strlen($nick) > 20) {
			Message::add(Lang::get("NICK_LONG", 20));
			$err = true;
		}

		if (strlen($text) < 2) {
			Message::add(Lang::get("TEXT_SHORT", 2));
			$err = true;
		} else if (strlen($text) > 10000) {
			Message::add(Lang::get("TEXT_SHORT", 10000));
			$err = true;
		}

		if (strtoupper($nick) == strtoupper(ADMIN_USER) && !Login::isLogged()) {
			Message::add(Lang::get("CANT_NICK_ADMIN"));
			$err = true;
		}

		if ($err)
			return false;

		$ret = Db::query("INSERT INTO `comments` (`article_id`,`nickname`,`text`,`ip`) VALUES (?,?,?,INET_ATON(?))",
			array($article_id, $nick, $text, Logging::getIP()), true);
		if ($ret == 0) {
			Message::add(Lang::get("UNABLE_ADD_COM"));
			return false;
		}

		Message::add(Lang::get("COMMENT_SEND"));
		return true;
	}

	/**
	 * Are comments allowed
	 *
	 * @param int $article_id
	 * @return int 1 adding allowed and visible
	 *		-1 if visible and adding not allowed
	 *		false elsewhere
	 */
	public static function allowed($article_id) {
		$res = Db::queryRow("SELECT com_allowed, com_show FROM articles WHERE id=?", array($article_id));
		if ($res == false)
			return false;

		if ($res['com_allowed'] && $res['com_show'])
			return 1;
		if ($res['com_show'])
			return -1;

		return false;
	}

	/**
	 * Set comments permission for given article
	 *
	 * @param int $article_id
	 * @param int $permission 1 adding allowed and visible
	 *			 -1 adding not allowed and visible
	 *			 anything else - comments not permitted
	 * @return boolean
	 */
	public static function setPermissions($article_id, $permission) {
		$params = array('com_allowed' => true, 'com_show' => true);
		if ($permission == -1) {
			$params['com_allowed'] = false;
		}
		else if ($permission != 1) {
			$params['com_allowed'] = false;
			$params['com_show'] = false;
		}

		if (!Db::update("articles", array('id' => $article_id), $params))
			return false;
		return true;

	}
}
