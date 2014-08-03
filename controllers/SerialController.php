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
 * Serial controller
 *
 * Show navigation if article is part of some serial
 */
class SerialController extends Controller
{
	/**
	 * @param array $param serial_id article_id
	 */
	public function __construct($param) {
		if (count($param) != 2 || !is_numeric($param[0]) || !is_numeric($param[1]) || !$param[0])
			return;

		$serial = Serial::getSerials($param[0]);
		if (!$serial || count($serial) <= 1)
			return;

		$this->data['name'] = Serial::getName($param[0]);

		if ($this->data['name'] == "")
			return;

		$this->view = "serial";
		$this->data['serial'] = Lang::get('SERIAL');

		$tmp = Url::getTemp("article", "%d", "%s");
		foreach($serial as & $s) {
			$s['link'] = Url::getFrom($tmp, $s['id'], $s['url']);	
		}
		$this->data['parts'] = $serial;
		$this->data['skip'] = $param[1];
	}
}
