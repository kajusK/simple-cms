<?php
/**
 * Yet another simple CMS
 *
 * @copyright 2014 Jakub Kaderka
 * @license GNU General Public License, version 2; see LICENSE.txt
 */

//no direct access
defined("IN_CMS") or die("Unauthorized access");

define("ERROR_LOG_FILE", "files/error.log");
define("UPLOAD_ARTICLE", "files/articles/");
define("UPLOAD_ARTICLE_TMP", "files/articles/tmp/");
define("RSS_FEED", "files/rss/");

define("TITLE_LENGTH_MAX", 50);
define("TITLE_LENGTH_MIN", 2);
define("DESC_LENGTH", 250);
define("KEYWORDS_LENGTH", 50);
define("URL_LENGTH", 50);
define("MENU_LENGTH_MIN", 2);
define("MENU_LENGTH_MAX", 15);
