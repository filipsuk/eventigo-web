<?php

namespace App\Modules\Core\Utils;


class DateTime
{
	const DATETIME_FORMAT = 'd. m. Y H:i';


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
}