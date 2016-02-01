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


	public function getAllWithDates(array $tagsIds, DateTime $dateTime) : array
	{
		$eventsTags = $this->database->table('events_tags')
			->select('DISTINCT(event_id)')
			->where('tag_id', $tagsIds)
			->order('rate DESC')
			->limit(EventModel::EVENTS_LIMIT)
			->fetchAssoc('[]=event_id');

		$selection = $this->getAll()
			->select('*')
			->select('TIMESTAMPDIFF(HOUR, ?, start) AS hours', $dateTime)
			->select('DATEDIFF(start, ?) - 1 AS days', $dateTime)
			->select('WEEKOFYEAR(start) = WEEKOFYEAR(?) AS thisWeek', $dateTime)
			->select('MONTH(start) = MONTH(?) AS thisMonth', $dateTime)
			->select('MONTH(start) = MONTH(?) AS nextMonth', $dateTime->modifyClone('+1 MONTH'))
			->where('end >= ?', $dateTime);

		if ($tagsIds) {
			$selection->where('id', $eventsTags);
		}

		return $selection->order('start')
			->limit(self::EVENTS_LIMIT)
			->fetchPairs('id');
	}
}