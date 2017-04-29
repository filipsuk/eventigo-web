<?php declare(strict_types=1);

namespace App\Modules\Core\Model\EventSources\Meetup;

use App\Modules\Core\Model\Entity\Event;
use App\Modules\Core\Model\EventSources\AbstractEventSource;
use DMS\Service\Meetup\MeetupKeyAuthClient;
use Nette\Utils\DateTime;

final class MeetupEventSource extends AbstractEventSource
{
	/**
	 * @var string[]
	 */
	protected const URLS = [
		'meetup.com',
		'www.meetup.com',
	];

	/**
	 * @var string
	 */
	private $apiKey;

	public function setApiKey(string $apiKey): void
	{
		$this->apiKey = $apiKey;
	}

	/**
	 * Get upcoming events of the page.
	 * @return Event[]
	 */
	public function getEvents(string $group): array
	{
		$client = MeetupKeyAuthClient::factory(['key' => $this->apiKey]);
		$groupEvents = $client->getGroupEvents(['urlname' => $group])->getData();

		$groupData = $client->getGroup(['urlname' => $group])->getData();
		$groupPhoto = isset($groupData['group_photo']) ? $groupData['group_photo']['photo_link'] : null;

		$events = [];
		foreach ($groupEvents as $event) {
			if ($event['status'] === 'upcoming') {
				$events[] = new Event(
					null,
					$event['name'],
					$event['description'] ?? '',
					$event['link'],
					DateTime::from($event['time'] / 1000),
					null,
					$groupPhoto ?? null,
					Event::calculateRateByAttendeesCount($event['yes_rsvp_count'] + $event['waitlist_count'])
				);
			}
		}

		return $events;
	}
}
