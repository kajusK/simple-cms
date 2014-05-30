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
 * Simple paging
 */
class Paging
{
	/**
	 * Generate pages navigation
	 *
	 * @param int $count number of all items
	 * @param int $page
	 * @param int $per_page items per one page
	 * @param string $url_first first page url
	 * @param string $url_prep prepared url to insert pagenumber
	 *
	 * @return mixed false or array of nav items
	 */
	public static function genNav($count, $page, $per_page, $url_first, $url_prep) {
		$nav = false;
		$pages = ceil($count / $per_page);
		if ($pages <= 1)
			return;

		if ($page != 1) {
			$nav['first'] = $url_first;
			$nav['prev'] = Url::getFrom($url_prep, $page-1);
		}

		//calculate minimum page number to show
		$first = $page - (int) NAV_MAX_COUNT/2;
		if ($first < 1)
			$first = 1;

		//if close to the end, show NAV_MAX_COUNT pages
		$diff = $first + NAV_MAX_COUNT - $pages; //pages over the last page
		if ($diff > 0)
			$first = ($first - $diff > 0) ? $first - $diff + 1 : 1;

		for ($i = $first; $i < $first + NAV_MAX_COUNT && $i <= $pages; $i++)
			$nav['num'][$i] = $i == $page ? false : Url::getFrom($url_prep, $i);

		if ($page < $pages) {
			$nav['next'] = Url::getFrom($url_prep, $page+1);
			$nav['last'] = Url::getFrom($url_prep, $pages);
		}
		return $nav;
	}

	/**
	 * Get first entry to show
	 *
	 * @param int $page number of page
	 * @param int $per_page items per one page
	 * @param int $count number of all items
	 *
	 * @return false or int number of first entry
	 */
	public static function getFrom($page, $per_page, $count)
	{
		$from = ($page - 1)*$per_page;
		if ($from < 0 || $from > $count - 1) {
			return false;
		}
		return $from;
	}
}
