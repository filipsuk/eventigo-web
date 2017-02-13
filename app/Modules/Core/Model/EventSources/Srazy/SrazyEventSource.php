<?php declare(strict_types=1);

namespace App\Modules\Core\Model\EventSources\Srazy;

use App\Modules\Core\Model\Entity\Event;
use App\Modules\Core\Model\EventSources\EventSource;
use Nette\Utils\DateTime;
use Webuni\Srazy\Api\SeriesApi;
use Webuni\Srazy\Client;
use Webuni\Srazy\Page\SeriesPage;


class SrazyEventSource extends EventSource
{
	const URLS = [
		'srazy.info',
		'www.srazy.info',
	];


	/**
	 * Get upcoming events of the page
	 * @param string $series
	 * @return Event[]
	 */
	public function getEvents(string $series)
	{
		$client = new Client();
		/** @var \Webuni\Srazy\Model\Event[] $srazyEvents */
		$srazyEvents = $client->series()->get($series)->getEvents();

		$events = [];
		foreach ($srazyEvents as $event) {
			$start = $event->getStart() ? DateTime::from($event->getStart()) : null;
			if ($start && $start > new DateTime) {
				$e = new Event;
				$e->setName($event->getName());
				$e->setDescription($event->getDescription() ?? '');
				$e->setStart($start);
				$e->setEnd($event->getEnd() ? DateTime::from($event->getEnd()) : null);
				$e->setOriginUrl($event->getUri());
				// TODO $e->setImage() Get image from community page
				$e->setRateByAttendeesCount(count($event->getConfirmedAttendees()) + count($event->getUnconfirmedAttendees()));
				$events[] = $e;
			}
		}

		return $events;
	}
}
