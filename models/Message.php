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
 * Showing errors, messages, etc.
 */
class Message
{
	private static $messages = array();

	/**
	 * Add message to show
	 *
	 * @param string $message message to add
	 * @param string $type optional message type (use definitions above)
	 */
	public static function add($message) {
		self::$messages[] = $message;
	}

	/**
	 * Get all messages
	 *
	 * @param string $message message to add
	 * @param string $type optional message type (use definitions above)
	 */
	public static function getMessages() {
		return self::$messages;
	}
}
