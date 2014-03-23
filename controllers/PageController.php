<?php
/**
 * Yet another simple CMS
 *
 * @copyright 2014 Jakub Kaderka
 * @license GNU General Public License, version 2; see LICENSE.txt
 */

//no direct access
defined("IN_CMS") or die("Unauthorized access");

class PageController extends Controller
{
	protected $view = "page";
	protected $comments;

	/**
	 * @param array $param id, url
	 */
	public function __construct($param) {
		if (count($param) == 0 || count($param) > 2) {
			$this->_notFound();
			return;
		}

		$res = Article::getArticle($param[0]);
		if (!$res) {
			$this->_notFound();
			return;
		}

		//nice url do not match, redirect to correct one
		if (!isset($param[1]) || $res['url'] != $param[1]) {
			$this->statusCode(301);
			$this->redirect(Url::get("page", $res['id'], $res['url']), 0);
		}

		$this->head = array('title' => $res['title'], 'keywords' => $res['keywords'], 'description' => $res['description']);

		$this->data['title'] = $res['title'];
		$this->data['content'] = $res['content'];

		$this->comments = new CommentController($param[0]);
	}
}
