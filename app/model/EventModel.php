<?php

namespace App\Model;

use Nette\Database\Table\IRow;
use Nette\Utils\DateTime;


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


	public function getAllWithDates(DateTime $dateTime) : array
	{
		return $this->getAll()
			->select('*')
			->select('TIMESTAMPDIFF(HOUR, ?, start) AS hours', $dateTime)
			->select('DATEDIFF(start, ?) - 1 AS days', $dateTime)
			->select('WEEKOFYEAR(start) = WEEKOFYEAR(?) AS thisWeek', $dateTime)
			->select('MONTH(start) = MONTH(?) AS thisMonth', $dateTime)
			->select('MONTH(start) = MONTH(?) AS nextMonth', $dateTime->modifyClone('+1 MONTH'))
			->where('end >= ?', $dateTime)
			->order('start')
			->fetchAll();
	}
}