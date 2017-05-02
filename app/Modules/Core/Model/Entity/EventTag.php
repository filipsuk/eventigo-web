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

	public function __construct(Event $event, Tag $tag, int $rate = null)
	{
		$this->event = $event;
		$this->tag = $tag;
		$this->rate = $rate;
	}

	public static function createFromRow(ActiveRow $eventTagRow): EventTag
	{
		return new EventTag(
			Event::createFromRow($eventTagRow->ref('events', 'event_id')),
			Tag::createFromRow($eventTagRow->ref('tags', 'tag_id')),
			$eventTagRow['rate']
		);
	}

	/**
	 * @return Event
	 */
	public function getEvent(): Event
	{
		return $this->event;
	}

	/**
	 * @return Tag
	 */
	public function getTag(): Tag
	{
		return $this->tag;
	}

	/**
	 * @return int
	 */
	public function getRate(): int
	{
		return $this->rate;
	}

}
