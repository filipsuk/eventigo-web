<?php declare(strict_types=1);

namespace App\Modules\Core\Model;

use App\Modules\Core\Model\Entity\Event;
use App\Modules\Core\Model\Entity\EventTag;
use Nette\Database\Table\IRow;
use Nette\Utils\DateTime;

final class EventModel extends AbstractBaseModel
{
    /**
     * @var string
     */
    public const STATE_APPROVED = 'approved';

    /**
     * @var string
     */
    public const STATE_NOT_APPROVED = 'not-approved';

    /**
     * @var string
     */
    public const STATE_SKIP = 'skip';

    /**
     * @var string[]
     */
    public const STATES = [
        self::STATE_APPROVED,
        self::STATE_NOT_APPROVED,
        self::STATE_SKIP,
    ];
    /**
     * @var string
     */
    protected const TABLE_NAME = 'events';

    /**
     * @return EventTag[]
     */
    public function getEventTags(IRow $event): array
    {
        /** @var EventTag[] */
        $eventTags = [];
        foreach ($event->related('events_tags') as $tag) {
            $eventTags[] = EventTag::createFromRow($tag);
        }

        return $eventTags;
    }

    /**
     * @return int[]
     */
    public function getRates(IRow $event): array
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
     * @return mixed[]
     * @refactor Not SRP, too much parameters, hell
     */
    public function getAllWithDates(
        array $tagsIds,
        ?DateTime $from = null,
        ?DateTime $to = null,
        ?DateTime $lastAccess = null,
        bool $showAbroad = true
    ): array {
        $calculateFrom = $from ?: new DateTime;
        $selection = $this->getAll()
            ->select('*')
            ->select('TIMESTAMPDIFF(HOUR, ?, start) AS hours', $calculateFrom)
            ->select('DATEDIFF(start, ?) - 1 AS days', $calculateFrom)
            ->select('WEEKOFYEAR(start) = WEEKOFYEAR(?) AS thisWeek', $calculateFrom)
            ->select('MONTH(start) = MONTH(?) AS thisMonth', $calculateFrom)
            ->select('MONTH(start) = MONTH(?) AS nextMonth', $calculateFrom->modifyClone('+1 MONTH'))
            ->select('CURDATE() = DATE(start)
				OR CURDATE() BETWEEN DATE(start) AND DATE(end) AS todayEvent')
            ->select('DATE(approved) BETWEEN (CURDATE() - INTERVAL 2 DAY) AND CURDATE() AS recentlyAddedEvent');
        if ($from) {
            $selection->where('(end IS NOT NULL AND end >= ?) OR (end IS NULL AND DATE(start) >= ?)',
                $from, $from->setTime(0, 0));
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

        if (! $showAbroad) {
            $selection->where(['country_id = ? OR country_id IS NULL' => 'CZ']);
        }

        // Return selected events ordered by start time and size (bigger first)
        return $selection->order('start, rate DESC')
            ->fetchPairs('id');
    }

    /**
     * @return bool|mixed|\Nette\Database\Table\IRow
     */
    public function findExistingEvent(Event $event)
    {
        $url = $event->getOriginUrl();

        return $this->getAll()
            ->where('origin_url = ? OR origin_url =	 ?
					OR REPLACE(origin_url, ?, "//") = ?
					OR REPLACE(origin_url, ?, "//") = ?',
                $url = substr($url, -1) === '/' ? substr($url, 0, -1) : $url,
                $urlSlash = $url . '/',
                '//www.', str_replace('//www.', '//', $url),
                '//www.', str_replace('//www.', '//', $urlSlash)
            )->fetch();
    }

    /**
     * Find previous event in series with image.
     */
    public function findPreviousEvent(int $eventSeriesId): ?IRow
    {
        return $this->getAll()
            ->where('event_series_id', $eventSeriesId)
            ->where('image IS NOT NULL OR image <> \'\'')
            ->order('end DESC')
            ->fetch() ?: null;
    }

    /**
     * @return Event[]
     */
    public function getApprovedEventsByDate(DateTime $from): array
    {
        $selection = $this->getAll()
            ->where('approved >= ?', $from)
            ->order('rate DESC, start ASC');

        /** @var Event[] $events */
        $events = [];
        foreach ($selection->fetchAll() as $event) {
            $events[] = Event::createFromRow($event);
        }

        return $events;
    }
}
