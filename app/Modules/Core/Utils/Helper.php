<?php declare(strict_types=1);

namespace App\Modules\Core\Utils;

use Pelago\Emogrifier;
use Tracy\Debugger;

final class Helper
{
	/**
	 * Converts UTF-8 chars to HTML entities.
	 * Note: String is surrounded by <p> tag.
	 */
	public static function utfToHtmlEntities(string $input): string
	{
		$emogrifier = new Emogrifier;

		try {
			$emogrifier->setHtml($input);
			return $emogrifier->emogrifyBodyContent();
		} catch (\BadMethodCallException $e) {
			Debugger::log($e->getMessage());
			return $input;
		}
	}

	/**
	 * Returns utm params from array of parameters.
	 *
	 * @param string[] $params
	 * @return string[]
	 */
	public static function extractUtmParameters(array $params): array
	{
		$utmParams = ['utm_source', 'utm_campaign', 'utm_medium'];
		return array_intersect_key($params, array_flip($utmParams));
	}
}
