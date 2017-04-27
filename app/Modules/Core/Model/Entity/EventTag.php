<?php declare(strict_types=1);

namespace App\Modules\Core\Model\Entity;

use Nette\Database\Table\ActiveRow;

final class EventTag
{
	/** @var Event */
	private $event;

	/** @var Tag */
	private $tag;

	/** @var int */
	private $rate;

	public static function createFromRow(ActiveRow $eventTagRow)
	{
		$eventTag = new EventTag();
		$eventTag
			->setTag(Tag::createFromRow($eventTagRow->ref('tags', 'tag_id')))
			->setEvent(Event::createFromRow($eventTagRow->ref('events', 'event_id')))
			->setRate($eventTagRow['rate']);
		return $eventTag;
	}

	/**
	 * @return Event
	 */
	public function getEvent(): Event
	{
		return $this->event;
	}

	/**
	 * @param Event $event
	 * @return EventTag
	 */
	public function setEvent(Event $event): EventTag
	{
		$this->event = $event;
		return $this;
	}

	/**
	 * @return Tag
	 */
	public function getTag(): Tag
	{
		return $this->tag;
	}

	/**
	 * @param Tag $tag
	 * @return EventTag
	 */
	public function setTag(Tag $tag): EventTag
	{
		$this->tag = $tag;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getRate(): int
	{
		return $this->rate;
	}

	/**
	 * @param int $rate
	 * @return EventTag
	 */
	public function setRate(int $rate): EventTag
	{
		$this->rate = $rate;
		return $this;
	}

}
