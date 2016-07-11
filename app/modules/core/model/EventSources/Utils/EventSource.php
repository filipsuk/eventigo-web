<?php

namespace App\Modules\Core\Model\EventSources\Utils;

use Nette\Http\Url;


class EventSource
{
	const SOURCES = [
		'facebook.com',
		'www.facebook.com',
		'srazy.info',
		'meetup.com',
		'www.meetup.com',
	];


	public static function isCrawlable(string $url) : bool
	{
		try {
			$host = (new Url($url))->getHost();
			return in_array($host, self::SOURCES, true);

		} catch (\Exception $e) {
			return false;
		}
	}
}
