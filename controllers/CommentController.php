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
 * Comments controller
 */
class CommentController extends Controller
{
	/**
	 * @param array $param if first is number, take it as article_id and print comments
	 *		else check for mode (add...)
	 */
	public function __construct($param) {
		if (count($param) == 1 && is_numeric($param[0])) {
			$this->_show($param[0]);
			return;
		}

		if (count($param) == 2 && $param[0] == "add") {
			$this->_add($param[1]);
			return;
		}

		$this->_notFound();
	}

	/**
	 * Add new comment
	 *
	 * @param int $article id
	 */
	private function _add($article_id) {
		$this->head = array('title' => Lang::get("ADD_COMMENT"),
			       	'keywords' => "", 'description' => "");

		if (Comments::allowed($article_id) <= 0) {
			Message::add(Lang::get("COMMENTS_NOT_ALLOWED"));
			$this->view = false;
			$this->redirect(Url::get("article", $article_id), 3);
			return;
		}

		$this->view = "comment_add";
		$this->data = array("action" => Url::get("comment", "add", $article_id),
				"nickname" => Lang::get("NICKNAME"),
				"comment" => Lang::get("COMMENT"),
				"antispam" => Lang::get("QUESTION_ANTISPAM"),
				"send" => Lang::get("SEND"),
				"article_name" => Article::getName($article_id));

		$this->data['nick'] = isset($_POST['nick']) ? $_POST['nick'] : '';
		$this->data['text'] = isset($_POST['text']) ? $_POST['text'] : '';

		if (isset($_POST['nick'])) {
			if (Comments::add($article_id, $_POST['nick'], $_POST['text'], $_POST['aq'])) {
				$this->view = false;
				$this->redirect(Url::get("article", $article_id), 2);
				return;
			}
		}
	}

	/**
	 * Show all comments
	 *
	 * @param int $article_id
	 */
	private function _show($article_id) {
		$this->data['link_add'] = Url::get("comment", "add", $article_id);
		$this->data['add_comment'] = Lang::get("ADD_COMMENT");
		$this->data['not_allowed'] = Lang::get("COMMENTS_NOT_ALLOWED");

		$this->data['allowed'] = $allowed =  Comments::allowed($article_id);
		if ($allowed)
			$this->data['comments'] = Comments::getAll($article_id);

		$this->view = "comments";
	}
}
