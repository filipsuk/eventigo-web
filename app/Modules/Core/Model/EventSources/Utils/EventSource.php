<?php declare(strict_types=1);

namespace App\Modules\Core\Model\EventSources\Utils;

use Nette\Http\Url;
use Throwable;

class EventSource
{
	/**
	 * @var string[]
	 */
	const SOURCES = [
		'facebook.com',
		'www.facebook.com',
		'srazy.info',
		'meetup.com',
		'www.meetup.com',
	];


	public static function isCrawlable(string $url): bool
	{
		try {
			$host = (new Url($url))->getHost();

			return in_array($host, self::SOURCES, true);

		} catch (Throwable $throwable) {
			return false;
		}
	}
}
