<?php

namespace App\Modules\Core\Utils;


use Kdyby\Translation\Translator;

class DateTime
{
	/** @var \Kdyby\Translation\Translator */
	public static $translator;
	
	const DATETIME_FORMAT = 'd. m. Y H:i';
	const DATE_FORMAT = 'd. m. Y';
	const NO_ZERO_DATE_FORMAT = 'j. n. Y';
	const W3C_DATE = 'Y-m-d';


	/**
	 * Get maximum of given datetimes
	 * @param DateTime|null
	 * @return \Nette\Utils\DateTime|null
	 */
	public static function max()
	{
		$dateTimes = array_filter(func_get_args());

		$max = reset($dateTimes) ?: null;
		foreach ($dateTimes as $dateTime) {
			if ($max < $dateTime) {
				$max = $dateTime;
			}
		}
		return $max;
	}
	
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
