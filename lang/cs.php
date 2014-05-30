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
		'NO_CATEGORY'		=> "Požadovaná kategorie neexistuje",
		'CATEGORY_EMPTY'	=> "Kategorie neobsahuje žádný článek",
		'NO_ARTICLE' 		=> "Požadovaný článek neexistuje.",
		'NO_TRANSL'		=> "Omlouváme se, tento článek nebyl do českého jazyka přeložen.",
		'NOT_FOUND'		=> "Stránka nenalezena",

		'TITLE_NOT_FOUND'	=> "Stránka nenalezena",
		'TITLE_ADD_COM'		=> "Přidat komentář",
		'TITLE_ERROR'		=> "Chyba",
		'TITLE_ADMIN'		=> "Administrace",

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
		'CANT_NICK_ADMIN'	=> "Tuto přezdívku nelze použít, je vyhrazena vlastníkovi stránky",

		'USERNAME'		=> "Uživatelské jméno",
		'PASS'			=> "Heslo",
		'ADMIN_LOGIN'		=> "Přihlášení do administrace",
		'WRONG_LOGIN'		=> "Uživatelské jméno nebo heslo není správně",
		'EDIT_ARTICLE'		=> "Editace článku",
		'EDIT_TITLE'		=> "Titulek",
		'EDIT_DESCRIPTION'	=> "Popis",
		'EDIT_KEYWORDS'		=> "Klíčová slova",
		'EDIT_CONTENT'		=> "Obsah",
		'YES'			=> "Ano",
		'NO'			=> "Ne",
		'CONFIRM_DELETE'	=> "Potvrdit smazání",
		'ARTICLE_DELETE'	=> "Smazat článek: %s",
		'CATEGORY'		=> "Kategorie",
		'EDIT_MENU'		=> "Editace menu",
		'MENU_MAIN'		=> "Hlavní položka",
		'MENU_PARENT'		=> "Nadmenu",
		'MENU_NAME'		=> "Jméno",
		'MENU_DELETE_ITEM'	=> "Smazat položku: %s",
		'MENU_DELETE'		=> "Smazat položku",
		'MENU_ADD'		=> "Přidat položku",
		'MENU_EMPTY'		=> "Menu je prázné, nejdříve přidejte nějakou položku do menu",

		'TITLE_LONG'		=> "Titulek je příliš dlouhý, maximální délka je ".TITLE_LENGTH_MAX,
		'TITLE_SHORT'		=> "Titulek je příliš krátký, minimální délka je ".TITLE_LENGTH_MIN,
		'DESCRIPTION_LONG'	=> "Popis přílíš dlouhý, maximální délka je ".DESC_LENGTH,
		'KEYWORDS_LONG'		=> "Klíčová slova příliš dlouhá, maximální délka je ".KEYWORDS_LENGTH,
		'MENU_SHORT'		=> "Název je příliš krátký",
		'MENU_LONG'		=> "Název je příliš dlouhý",
		'MENU_NO_PARENT'	=> "Takové hlavní menu neexistuje",
		'MENU_NO_ITEM'		=> "Položka neexistuje",
		'MENU_DEL_ERR'		=> "Odstranění nebylo úspěšné",
		'MENU_DELETED'		=> "Položka odstraněna",
		'SAVED'			=> "Uloženo",
		'DB_UNABLE_SAVE'	=> "Záznam se nepodařilo uložit",
		'ARTICLE_DELETED'	=> "Článek smazán",
		'COM_ADDING_ALLOWED'	=> "Přidávání povoleno",
		'COM_ADDING_DISABLED'	=> "Přidávání zakázáno",
		'COM_DISABLED'		=> "Zakázány",
		'COM_SETTINGS'		=> "Komentáře",
		'EDIT_FILES'		=> "Upravit soubory",
		'UPLOAD_FILES'		=> "Nahrát soubory",
		'FILENAME'		=> "Jméno",
		'DELETE'		=> "Smazat",
		'FILE_EXISTS'		=> "Soubor \"%s\" již existuje",
		'UPLOAD_FINISHED'	=> "Nahrávání dokončeno",
		'FILE_TUTORIAL'		=> "Text umístěný v [cesta] bude rozvinut do adresář_nahraných_souborů/cesta",

		'ARTICLE'		=> "Článek",
		'MENU'			=> "Menu",
		'EDIT'			=> "Editovat",
		'ARTICLE_ADD'		=> "Přidat článek",
		'LOGOUT'		=> "Odhlásit",

		'COUNTER'		=> "Návštěv",
		'COUNT_TOTAL'		=> "Celkem",
		'COUNT_TODAY'		=> "Dnes",
);
