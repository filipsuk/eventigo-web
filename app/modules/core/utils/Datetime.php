<?php

namespace App\Modules\Core\Utils;


use Kdyby\Translation\Translator;

class DateTime
{
	/** @var \Kdyby\Translation\Translator */
	public static $translator;
	
	const DATETIME_FORMAT = 'd. m. Y H:i';
	
	public static function setTranslator(Translator $t)
	{
		self::$translator = $t;
	}

	public static function eventsDatetimeFilter (\Nette\Utils\DateTime $a, \Nette\Utils\DateTime $b = null) {
		// Translate name of day
		$aDayName = self::$translator->translate('front.datetime.' . strtolower(strftime('%A', $a->getTimestamp())));

		// Two day event
		if ($b && ($a->format('dmy') !== $b->format('dmy'))) {
			$result = $aDayName . $a->format(' j. n. ') . '&nbsp;&ndash;&nbsp;' . $b->format('j. n. Y');
		}
		// One day event
		else {
			$result = $aDayName . $a->format(' j. n. Y');
			// Add Hour:minute time if its not 00:00
			if ((int)$a->format('G') > 0) {
				$result .= $a->format(' G:i');
			}
		}
		return $result;
	}
}
