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
 * Article controller
 *
 * Process parsed url and show required output
 */
class ArticleController extends Controller
{
	protected $comments;
	protected $view = "article";

	/**
	 * Show given article and its comments
	 *
	 * @param array $param first field must be article_id
	 */
	public function __construct($param) {
		if (count($param) == 0 || count($param) > 2) {
			$this->_notFound();
			return;
		}

		$res = Article::getArticle($param[0]);
		if (!$res) {
			$this->__notFound($param[0]);
			return;
		}

		//nice url do not match, redirect to correct one
		if (!isset($param[1]) || $res['url'] != $param[1]) {
			$this->statusCode(301);
			$this->redirect(Url::get("article", $res['id'], $res['url']), 0);
		}

		$this->head = array('title' => $res['title'], 'keywords' => $res['keywords'], 'description' => $res['description']);

		$keys = array('title', 'description', 'date', 'content');
		$this->data =  array_intersect_key($res, array_flip($keys));

		$this->comments = new CommentController($param[0]);
	}

	/**
	 * Article not found
	 *
	 * @param int $id article id
	 */
	protected function __notFound($id) {
		$res = Article::translAvailable($id);
		if (count($res) != 0)
			$this->_notFound(Lang::get("NO_TRANSL"));
		else
			$this->_notFound(Lang::get("NO_ARTICLE"));

	}
}
