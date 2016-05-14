<?php
/**
 * Created by PhpStorm.
 * User: filipsuk
 * Date: 12.05.16
 * Time: 20:22
 */

namespace App\Modules\Core\Model\EventSources\Facebook;


use App\Modules\Core\Model\Entity\Event;
use App\Modules\Core\Model\EventSources\IEventSource;
use Kdyby\Facebook\Facebook;
use Kdyby\Facebook\FacebookApiException;
use Nette\Http\Url;
use Nette\Utils\DateTime;
use Tracy\Debugger;

class FacebookEventSource implements IEventSource
{
	const EVENT_FIELDS = 'cover,end_time,start_time,name,description,interested_count,attending_count';

	/** @var \Kdyby\Facebook\Facebook*/
	public $facebook;

	/**
	 * FacebookEventSource constructor.
	 * @param \Kdyby\Facebook\Facebook $facebook
	 */
	public function __construct(Facebook $facebook)
	{
		$this->facebook = $facebook;
	}


	/**
	 * Get event by platform specific ID
	 *
	 * @param $id
	 * @return Event
	 * @throws \Kdyby\Facebook\FacebookApiException
	 */
	public function getEventById($id) : Event
	{
		try {
			$response = $this->facebook->api(
				'/' . $id,
				'GET',
				['fields' => self::EVENT_FIELDS]
			);

			$e = new Event();
			$e->setName($response->name);
			$e->setDescription($response->description ?? '');
			$e->setStart(isset($response->start_time) ? DateTime::createFromFormat(DATE_ISO8601, $response->start_time) : null);
			$e->setEnd(isset($response->end_time) ? DateTime::createFromFormat(DATE_ISO8601, $response->end_time) : null);
			$e->setOriginUrl('https://facebook.com/events/' . $id . '/');
			$e->setImage($response->cover->source ?? null);
			$e->setRateByAttendeesCount($response->interested_count + $response->attending_count);
			return $e;

		} catch (FacebookApiException $e) {
			Debugger::log($e, Debugger::EXCEPTION);
			throw $e;
		}
	}


	/**
	 * Get upcoming events of the page
	 * @param string|int $pageId
	 * @return Event[]
	 * @throws FacebookApiException
	 */
	public function getPageEvents($pageId)
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
				$start = isset($event->start_time) ? DateTime::createFromFormat(DATE_ISO8601, $event->start_time) : null;
				if ($start && $start > new DateTime) {
					$e = new Event;
					$e->setName($event->name);
					$e->setDescription($event->description ?? '');
					$e->setStart($start);
					$e->setEnd(isset($event->end_time) ? DateTime::createFromFormat(DATE_ISO8601, $event->end_time) : null);
					$e->setOriginUrl('https://facebook.com/events/' . $event->id . '/');
					$e->setImage($event->cover->source ?? null);
					$e->setRateByAttendeesCount($event->interested_count + $event->attending_count);
					$events[] = $e;
				}
			}
		}

		return $events;
	}


	public static function isFacebook($url) : bool
	{

		try {
			$host = (new Url($url))->getHost();
			return $host === 'www.facebook.com' || $host === 'facebook.com';

		} catch (\Exception $e) {
			return false;
		}
	}
}
