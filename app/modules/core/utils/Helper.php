<?php

namespace App\Modules\Core\Utils;

use Pelago\Emogrifier;
use Tracy\Debugger;

class Helper
{
	/**
	 * Converts UTF-8 chars to HTML entities.
	 * Note: String is surrounded by <p> tag.
	 * 
	 * @param string $input
	 * @return string
	 */
	public static function utfToHtmlEntities(string $input)
	{
		$emogrifier = new Emogrifier();

		try {
			$emogrifier->setHtml($input);
			return $emogrifier->emogrifyBodyContent();
		} catch (\BadMethodCallException $e) {
			Debugger::log($e->getMessage());
			return $input;
		}
	}
}
