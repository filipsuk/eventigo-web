<?php declare(strict_types=1);

namespace App\Modules\Core\Model\EventSources\Facebook;

use App\Modules\Core\Model\Entity\Event;
use App\Modules\Core\Model\EventSources\EventSource;
use Kdyby\Facebook\Facebook;
use Kdyby\Facebook\FacebookApiException;
use Nette\Utils\DateTime;
use Tracy\Debugger;


class FacebookEventSource extends EventSource
{
	const EVENT_FIELDS = 'cover,end_time,start_time,name,description,interested_count,attending_count';

	const URLS = [
		'facebook.com',
		'www.facebook.com',
	];

	/** @var \Kdyby\Facebook\Facebook*/
	public $facebook;


	public function __construct(Facebook $facebook)
	{
		$this->facebook = $facebook;
	}


	public function getEventById(string $id): Event
	{
		try {
			$response = $this->facebook->api(
				'/' . $id,
				'GET',
				['fields' => self::EVENT_FIELDS]
			);

			return new Event(
				null,
				$response->name,
				$response->description ?? '',
				'https://facebook.com/events/' . $id . '/',
				isset($response->start_time) ? DateTime::createFromFormat(DATE_ISO8601, $response->start_time) : null,
				isset($response->end_time) ? DateTime::createFromFormat(DATE_ISO8601, $response->end_time) : null,
				$response->cover->source ?? null,
				Event::calculateRateByAttendeesCount($response->interested_count + $response->attending_count)
			);

		} catch (FacebookApiException $e) {
			Debugger::log($e, Debugger::EXCEPTION);
			throw $e;
		}
	}


	/**
	 * Get upcoming events of the page
	 * @return Event[]
	 */
	public function getEvents(string $pageId): array
	{
		$response = $this->facebook->api(
			'/' . $pageId, 'GET', [
				'fields' => 'events{' . self::EVENT_FIELDS . '}'
			]
		);

		$events = [];
		if (isset($response->events)) {
			$fbEvents = $response->events->data;
			foreach ($fbEvents as $event) {
				$start = isset($event->start_time) ? $this->createIsoDateTime($event->start_time) : null;
				if ($start && $start > new DateTime) {
<<<<<<< HEAD
					$events[] = new Event(
						null,
						$event->name,
						$event->description ?? '',
						'https://facebook.com/events/' . $event->id . '/',
						$start,
						isset($event->end_time) ? DateTime::createFromFormat(DATE_ISO8601, $event->end_time) : null,
						$event->cover->source ?? null,
						Event::calculateRateByAttendeesCount($event->interested_count + $event->attending_count)
					);
=======
					$e = new Event;
					$e->setName($event->name);
					$e->setDescription($event->description ?? '');
					$e->setStart($start);
					$e->setEnd(isset($event->end_time) ? $this->createIsoDateTime($event->end_time) : null);
					$e->setOriginUrl('https://facebook.com/events/' . $event->id . '/');
					$e->setImage($event->cover->source ?? null);
					$e->setRateByAttendeesCount($event->interested_count + $event->attending_count);
					$events[] = $e;
>>>>>>> fix cs: line limit
				}
			}
		}

		return $events;
	}

	/**
	 * @return DateTime
	 */
	private function createIsoDateTime($time)
	{
		return DateTime::createFromFormat(DATE_ISO8601, $time);
	}
}