<?php declare(strict_types=1);

namespace App\Modules\Core\Model\EventSources\Srazy;

use App\Modules\Core\Model\Entity\Event;
use App\Modules\Core\Model\EventSources\AbstractEventSource;
use Nette\Utils\DateTime;
use Webuni\Srazy\Client;

final class SrazyEventSource extends AbstractEventSource
{
	/**
	 * @var string[]
	 */
	protected const URLS = [
		'srazy.info',
		'www.srazy.info',
	];

	/**
	 * Get upcoming events of the page.
	 * @return Event[]
	 */
	public function getEvents(string $series): array
	{
		$client = new Client;
		/** @var \Webuni\Srazy\Model\Event[] $srazyEvents */
		$srazyEvents = $client->series()->get($series)->getEvents();

		$events = [];
		foreach ($srazyEvents as $event) {
			$start = $event->getStart() ? DateTime::from($event->getStart()) : null;
			if ($start && $start > new DateTime) {
				$events[] = new Event(
					null,
					$event->getName() ?? '',
					$event->getDescription() ?? '',
					$event->getUri(),
					$start,
					$event->getEnd() ? DateTime::from($event->getEnd()) : null,
					null, // TODO $e->setImage() Get image from community page
					Event::calculateRateByAttendeesCount(
						count($event->getConfirmedAttendees()) + count($event->getUnconfirmedAttendees())
					)
				);
			}
		}

		return $events;
	}
}
