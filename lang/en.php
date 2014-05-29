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
		'NO_CATEGORY'		=> "Required category doesn't exist",
		'CATEGORY_EMPTY'	=> "Requied category doesn't contain any articles",
		'NO_ARTICLE'		=> "Required article doesn't exist.",
		'NO_TRANSL'		=> "This article isn't available in English.",
		'NOT_FOUND'		=> "Page not found",

		'TITLE_NOT_FOUND'	=> "Page not found",
		'TITLE_ADD_COM'		=> "Add comment",
		'TITLE_ERROR'		=> "Error",
		'TITLE_ADMIN'		=> "Administration",

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
		'CANT_NICK_ADMIN'	=> "This nickname is reserved for page owner, please choose different",

		'USERNAME'		=> "Username",
		'PASS'			=> "Password",
		'ADMIN_LOGIN'		=> "Administration login",
		'WRONG_LOGIN'		=> "Username or password is incorrect",
		'EDIT_ARTICLE'		=> "Edit article",
		'EDIT_TITLE'		=> "Title",
		'EDIT_DESCRIPTION'	=> "Description",
		'EDIT_KEYWORDS'		=> "Keywords",
		'EDIT_CONTENT'		=> "Content",
		'YES'			=> "Yes",
		'NO'			=> "No",
		'CONFIRM_DELETE'	=> "Confirm",
		'ARTICLE_DELETE'	=> "Delete article: %s",
		'CATEGORY'		=> "Category",
		'EDIT_MENU'		=> "Edit menu",
		'MENU_MAIN'		=> "Main item",
		'MENU_PARENT'		=> "Parent item",
		'MENU_NAME'		=> "Name",
		'MENU_DELETE'		=> "Delete item: %s",
		'MENU_DELETE'		=> "Delete item",
		'MENU_ADD'		=> "Add item",
		'MENU_EMPTY'		=> "Menu is empty, add some item to menu first",

		'TITLE_LONG'		=> "Title too long, maximal length is ".TITLE_LENGTH_MAX,
		'TITLE_SHORT'		=> "Title too short, minimal length is ".TITLE_LENGTH_MIN,
		'DESCRIPTION_LONG'	=> "Description too long, maximal length is ".DESC_LENGTH,
		'KEYWORDS_LONG'		=> "Keywords too long, maximal length is ".KEYWORDS_LENGTH,
		'MENU_SHORT'		=> "Name too short",
		'MENU_LONG'		=> "Name too long",
		'MENU_NO_PARENT'	=> "There is no such parent menu",
		'MENU_NO_ITEM'		=> "No such item",
		'MENU_DELETED'		=> "Item deleted",
		'SAVED'			=> "Saved",
		'DB_UNABLE_SAVE'	=> "Unable to save this record",
		'ARTICLE_DELETED'	=> "Article deleted",
		'COM_ADDING_ALLOWED'	=> "Adding allowed",
		'COM_ADDING_DISABLED'	=> "Adding disabled",
		'COM_DISABLED'		=> "Disabled",
		'COM_SETTINGS'		=> "Comments",
		'EDIT_FILES'		=> "Edit files",
		'UPLOAD_FILES'		=> "Upload",
		'FILENAME'		=> "Filename",
		'DELETE'		=> "Delete",
		'FILE_EXISTS'		=> "File \"%s\" already exists",
		'UPLOAD_FINISHED'	=> "Uploading finished",
		'FILE_TUTORIAL'		=> "Text like [path] will be expanded to uploaded_files/path",

		'ARTICLE'		=> "Article",
		'MENU'			=> "Menu",
		'EDIT'			=> "Edit",
		'ARTICLE_ADD'		=> "Add article",
		'LOGOUT'		=> "Logout",
);
