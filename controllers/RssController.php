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
 * Rss controller - prints feed end EXIT()!!!!!
 */
class RssController extends Controller
{
	public function __construct($param) {
		if (count($param) != 0 || (!Rss::feedExists() && !Rss::gen())) {
			$this->_notFound();
			return;
		}
		header("Content-Type: text/xml");
		Rss::output();
		exit();
	}

}
