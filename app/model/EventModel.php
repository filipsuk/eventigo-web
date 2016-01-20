<?php

namespace App\Model;

use Nette\Database\Table\IRow;
use Nette\Utils\DateTime;


class EventModel extends BaseModel
{
	const TABLE_NAME = 'events';

	/** Number of events per list */
	const EVENTS_LIMIT = 10;


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


	public function getAllWithDates(array $tags, DateTime $dateTime) : array
	{
		$selection = $this->getAll()
			->select('*')
			->select('TIMESTAMPDIFF(HOUR, ?, start) AS hours', $dateTime)
			->select('DATEDIFF(start, ?) - 1 AS days', $dateTime)
			->select('WEEKOFYEAR(start) = WEEKOFYEAR(?) AS thisWeek', $dateTime)
			->select('MONTH(start) = MONTH(?) AS thisMonth', $dateTime)
			->select('MONTH(start) = MONTH(?) AS nextMonth', $dateTime->modifyClone('+1 MONTH'))
			->where('end >= ?', $dateTime);

		if ($tags) {
			$selection->where(':events_tags.tag_id', $tags);
		}

		return $selection->order('start')
			->limit(self::EVENTS_LIMIT)
			->fetchAll();
	}
}