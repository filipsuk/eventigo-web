<?php declare(strict_types=1);

namespace App\Modules\Core\Model\EventSources;

use App\Modules\Core\Model\Entity\Event;
use Nette\Http\Url;
use Throwable;


abstract class EventSource
{
	public static function isSource($url): bool
	{
		try {
			$host = (new Url($url))->getHost();
			return in_array($host, static::URLS, true);

		} catch (Throwable $throwable) {
			return false;
		}
	}


	/**
	 * @return Event[]
	 */
	abstract public function getEvents(string $source): array;
}
