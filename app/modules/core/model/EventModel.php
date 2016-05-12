<?php

namespace App\Modules\Core\Model;

use Nette\Database\Table\IRow;
use Nette\Utils\DateTime;


class EventModel extends BaseModel
{
	const TABLE_NAME = 'events';

	/** Number of events per list */
	const EVENTS_LIMIT = 10;


	/**
	 * @param \Nette\Database\Table\IRow $event
	 * @return string[]
	 */
	public function getEventTags(IRow $event)
	{
		$eventTags = [];

		foreach ($event->related('events_tags') as $eventTag) {
			$eventTags[] = 'tag-' . $eventTag->tag->code;
		}

		return $eventTags;
	}


	/**
	 * @param \Nette\Database\Table\IRow $event
	 * @return array
	 */
	public function getRates(IRow $event)
	{
		$rates = [
			'event' => $event->rate,
		];

		foreach ($event->related('events_tags') as $eventTag) {
			$rates[$eventTag->tag->code] = $eventTag->rate;
		}

		return $rates;
	}


	/**
	 * @param int[] $tagsIds
	 * @param \Nette\Utils\DateTime|NULL $from
	 * @param \Nette\Utils\DateTime|NULL $to
	 * @param \Nette\Utils\DateTime|NULL $lastAccess
	 * @return array
	 */
	public function getAllWithDates(array $tagsIds, DateTime $from = NULL, DateTime $to = NULL, DateTime $lastAccess = null)
	{
		$calculateFrom = $from ?: new DateTime;
		$selection = $this->getAll()
			->select('*')
			->select('TIMESTAMPDIFF(HOUR, ?, start) AS hours', $calculateFrom)
			->select('DATEDIFF(start, ?) - 1 AS days', $calculateFrom)
			->select('WEEKOFYEAR(start) = WEEKOFYEAR(?) AS thisWeek', $calculateFrom)
			->select('MONTH(start) = MONTH(?) AS thisMonth', $calculateFrom)
			->select('MONTH(start) = MONTH(?) AS nextMonth', $calculateFrom->modifyClone('+1 MONTH'));
		if ($from) {
			$selection->where('(end IS NOT NULL AND end >= ?) OR (end IS NULL AND start >= ?)', $from, $from);
		}
		if ($from && $to) {
			$selection->where('start <= ?', $to);
		}
		if ($lastAccess) {
			$selection->select('created > ? AS newEvent', $lastAccess);
		} else {
			$selection->select('FALSE AS newEvent');
		}

		// Filter events by tags (if user has some subscribed), otherwise return all events
		if (count($tagsIds) > 0) {
			$eventsTags = $this->database->table('events_tags')
				->select('DISTINCT(event_id)')
				->where('tag_id', $tagsIds)
				->order('rate DESC')
				->fetchAssoc('[]=event_id');

			$selection->where('id', $eventsTags);
		}

		// Return selected events ordered by start time and size (bigger first)
		return $selection->order('start, rate DESC')
			->fetchPairs('id');
	}
}
