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
 * Rss generator
 */
class Rss
{
	/**
	 * Generate rss feed
	 *
	 * @return boolean
	 */
	public static function gen() {
		$f = fopen(RSS_FEED."feed_".Lang::getLang().".rss", "w");
		if (!$f)
			return false;

		$ret = Article::getPage(0, RSS_COUNT);
		if (!$ret) {
			fclose($f);
			return false;
		}

		fwrite($f, "<?xml version='1.0' encoding='utf-8'?>\n<rss version='2.0'>\n");
		fwrite($f, "<channel>\n");
		fwrite($f, "<title>".constant("TITLE_".strtoupper(Lang::getLang()))."</title>\n");
		fwrite($f, "<link>".Url::get(false)."</link>\n");
		fwrite($f, "<description>".constant("DESCRIPTION_".strtoupper(Lang::getLang()))."</description>\n");
		fwrite($f, "<language>".Lang::getLang()."</language>\n");

		foreach ($ret as $a)
			self::_getItem($f, $a);

		fwrite($f, "</channel>\n</rss>");
		fclose($f);

		return true;
	}

	/**
	 * @return boolean
	 */
	public static function feedExists() {
		return is_file(RSS_FEED."feed_".Lang::getLang().".rss");
	}

	/**
	 * Output rss feed to stdout
	 */
	public static function output() {
		readfile(RSS_FEED."feed_".Lang::getLang().".rss");
	}

	/**
	 * Generate rss item
	 *
	 * @param resource $f file descriptior opened for writing
	 * @param array $article
	 */
	private static function _getItem($f, $article) {
		fwrite($f, "<item>\n<title>".$article['title']."</title>\n");
		fwrite($f, "<link>".Url::get("article", $article['id'], $article['url'])."</link>\n");
		fwrite($f, "<description>".$article['description']."</description>\n");
		fwrite($f, "<pubDate>".$article['date']."</pubDate>\n");
		fwrite($f, "</item>\n");
	}
}
