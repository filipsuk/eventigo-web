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
use Nette\Utils\DateTime;

class FacebookEventSource implements IEventSource
{

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
	 */
	public function getEventById($id) : Event
	{
		try {
			$response = $this->facebook->api(
				'/' . $id,
				'GET',
				['fields' => 'cover,end_time,start_time,name,description,interested_count,attending_count']
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
			return false;
		}
	}
}
