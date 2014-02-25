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
	"en_US.utf8",
	"en"
);

$lang = array(
		'DATABASE_CON_ERR'	=> "An error occured, unable to connect to database.",
		'UNEXCEPTED_ERR'	=> "An unexpected error occured.<br/>",
		'ERR_SOLUTION'		=> "Try to reload page, or visit <a href='./'>index page</a><br/>. If it still doesn't work,
						contact <a href='mailto:".ADMIN_MAIL."'>admin</а>.",
		'NO_CATEGORY'		=> "Required category doesn't exist",
		'CATEGORY_EMPTY'	=> "Requied category doesn't contain any articles",
		'NO_ARTICLE'		=> "Required article doesn't exist.",
		'NO_TRANSL'		=> "This article isn't available in English.",
		'NOT_FOUND'		=> "Page not found",

		'TITLE_NOT_FOUND'	=> "Page not found",
		'TITLE_ADD_COM'		=> "Add comment",
		'TITLE_ERROR'		=> "Error",

		'NICKNAME'		=> "Nickname",
		'COMMENT'		=> "Comment",
		'REPLY'			=> "Reply",
		'ADD_COMMENT'		=> "Add comment",
		'SEND'			=> "Send",
		'QUESTION_ANTISPAM' 	=> "Antispam - current year: ",
		'NICK_SHORT'		=> "Your nickname must have at least %d characters",
		'NICK_LONG'		=> "Your nickname can't have more than %d characters",
		'TEXT_SHORT'		=> "Your comment must have at least %d characters",
		'TEXT_LONG'		=> "Your comment is too long, maximal length is %d characters",
		'INCORRECT_ANTISPAM'	=> "Antispam answer is incorrect",
		'COMMENT_SEND'		=> "Your comment has been sent",
		'COMMENTS_NOT_ALLOWED'	=> "Adding new comments for this article isn't permitted",
		'UNABLE_ADD_COM'	=> "Unable to send the comment, try it later",
);
