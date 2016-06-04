<?php

namespace App\Modules\Core\Model;

use App\Modules\Core\Model\Entity\Event;
use Nette\Database\Table\IRow;
use Nette\Utils\DateTime;


class EventModel extends BaseModel
{
	const TABLE_NAME = 'events';

	/** Number of events per list */
	const EVENTS_LIMIT = 10;

	const STATE_APPROVED = 'approved';
	const STATE_NOT_APPROVED = 'not-approved';
	const STATE_SKIP = 'skip';

	const STATES = [
		self::STATE_APPROVED,
		self::STATE_NOT_APPROVED,
		self::STATE_SKIP,
	];


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
			->select('MONTH(start) = MONTH(?) AS nextMonth', $calculateFrom->modifyClone('+1 MONTH'))
			->select('CURDATE() BETWEEN DATE(start) AND DATE(end) AS todayEvent');
		if ($from) {
			$selection->where('(end IS NOT NULL AND end >= ?) OR (end IS NULL AND start >= ?)', $from, $from);
		}
		if ($from && $to) {
			$selection->where('start <= ?', $to);
		}
		if ($lastAccess) {
			$selection->select('approved > ? AS newEvent', $lastAccess);
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

		$selection->where('state', self::STATE_APPROVED);

		// Return selected events ordered by start time and size (bigger first)
		return $selection->order('start, rate DESC')
			->fetchPairs('id');
	}


	/**
	 * @param Event $event
	 * @return bool|mixed|\Nette\Database\Table\IRow
	 */
	public function findExistingEvent(Event $event)
	{
		$url = $event->getOriginUrl();

		return $this->getAll()
			->where('origin_url = ? OR origin_url =     ?
					OR REPLACE(origin_url, ?, "//") = ?
					OR REPLACE(origin_url, ?, "//") = ?',
				$url = substr($url, -1) === '/' ? substr($url, 0, -1) : $url,
				$urlSlash = $url . '/',
				'//www.', str_replace('//www.', '//', $url),
				'//www.', str_replace('//www.', '//', $urlSlash)
			)->fetch();
	}
}
