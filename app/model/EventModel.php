<?php

namespace App\Model;

use Nette\Database\Table\IRow;


class EventModel extends BaseModel
{
	const TABLE_NAME = 'events';


	public function getEventTags(IRow $event) : array
	{
		$eventTags = [];

		foreach ($event->related('events_tags') as $eventTag) {
			$eventTags[] = 'tag-' . $eventTag->tag->code;
		}

		return $eventTags;
	}


	public function getRates(IRow $event) : array
	{
		$rates = [
			'event' => $event->rate,
		];

		foreach ($event->related('events_tags') as $eventTag) {
			$rates[$eventTag->tag->code] = $eventTag->rate;
		}

		return $rates;
	}
}