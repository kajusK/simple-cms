<?php
/**
 * Yet another simple CMS
 *
 * @copyright 2014 Jakub Kaderka
 * @license GNU General Public License, version 2; see LICENSE.txt
 */

//no direct access
defined("IN_CMS") or die("Unauthorized access");

$locale = array(
	"cs_CZ.utf8",
	"cs"
);

$lang = array(
		'DATABASE_CON_ERR'	=> "Došlo k chybě, nemohu se připojit k databázi.",
		'UNEXCEPTED_ERR'	=> "Došlo k nečekané chybě, událost byla zaznamenána.<br/>",
		'ERR_SOLUTION'		=> "Zkuste stránku znovu načíst, nebo navšivte <a href='./'>úvodní stránku</a><br/>Pokud potže přetrvávají,
						kontaktuje <a href='mailto:".ADMIN_MAIL."'>administátora</а>.",
		'NO_CATEGORY'		=> "Požadovaná kategorie neexistuje",
		'CATEGORY_EMPTY'	=> "Kategorie neobsahuje žádný článek",
		'NO_ARTICLE' 		=> "Požadovaný článek neexistuje.",
		'NO_TRANSL'		=> "Omlouváme se, tento článek nebyl do českého jazyka přeložen.",
		'NOT_FOUND'		=> "Stránka nenalezena",

		'TITLE_NOT_FOUND'	=> "Stránka nenalezena",
		'TITLE_ADD_COM'		=> "Přidat komentář",
		'TITLE_ERROR'		=> "Chyba",

		'NICKNAME'		=> "Přezdívka",
		'COMMENT'		=> "Komentář",
		'REPLY'			=> "Odpovědět",
		'ADD_COMMENT'		=> "Přidat komentář",
		'SEND'			=> "Odeslat",
		'QUESTION_ANTISPAM' 	=> "Antispam - zadejte aktuální rok: ",
		'NICK_SHORT'		=> "Přezdívka musí mít alespoň %d znaky",
		'NICK_LONG'		=> "Přezdívka může mít maximálně %d znaků",
		'TEXT_SHORT'		=> "Komentář musí být alespoň %d znaky dlouhý",
		'TEXT_LONG'		=> "Komentář je příliš dlouhý, maximální délka je %d znaků",
		'INCORRECT_ANTISPAM'	=> "Špatně zodpovězená antispamová otázka",
		'COMMENT_SEND'		=> "Komentář byl odeslán",
		'COMMENTS_NOT_ALLOWED'	=> "U tohoto článku není přidávání komentářů dovoleno",
		'UNABLE_ADD_COM'	=> "Komentář nelze odeslat, zkuste to prosím za chvíli",
);
