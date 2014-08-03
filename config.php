<?php
/*
 * Config file
 */

//no direct access
defined("IN_CMS") or die("Unauthorized access");

//recoveable errors logging
//possible values: none, simple, all
define("ERROR_LOGGING", "all");

//local language, change to desired one
define("LOCAL_LANG", "cs");
define("TIMEZONE", "Europe/Prague");

//administrator's email
define("ADMIN_MAIL", "admin@example.org");

//admin user and pass
define("ADMIN_USER", "kajus");
define("ADMIN_PASS", "mcmsa");

//db config
define("DB_HOST", "localhost");
define("DB_NAME", "cms");
define("DB_USER", "cms");
define("DB_PASS", "cms");

define("PER_PAGE", 10);
define("ADMIN_PER_PAGE", 40);
define("NAV_MAX_COUNT", 10);
define("RSS_COUNT", 10);

//all supported language versions of Page title
define("TITLE_CS", "Kajusovy stránečky");
define("TITLE_EN", "Kajus's pages");

define("DESCRIPTION_CS", "Stránky o Linuxu, robotice, programování...");
define("DESCRIPTION_EN", "Pages about Linuxu, robotics, programming...");
//uncomment to show all possible errors
define("DEBUG", true);
//uncomment to profile db queries
//define("DB_PROFILING", true);
