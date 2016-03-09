<?php

namespace App\Modules\Admin\Model;


use App\Modules\Core\Model\EventModel;
use App\Modules\Core\Model\EventTagModel;
use App\Modules\Core\Model\TagModel;
use Nette\Utils\DateTime;

class EventService
{
	/** @var EventModel */
	private $eventModel;

	/** @var TagModel */
	private $tagModel;

	/** @var EventTagModel */
	private $eventTagModel;


	public function __construct(EventModel $eventModel,
	                            TagModel $tagModel,
	                            EventTagModel $eventTagModel)
	{
		$this->eventModel = $eventModel;
		$this->tagModel = $tagModel;
		$this->eventTagModel = $eventTagModel;
	}


	public function addEvent($values)
	{
		// Create event
		$event = $this->eventModel->insert([
			'name' => $values->name,
			'description' => $values->description ?: null,
			'origin_url' => $values->origin_url ?: null,
			'start' => DateTime::createFromFormat(\App\Modules\Core\Utils\DateTime::DATETIME_FORMAT, $values->start),
			'end' => $values->end
				? DateTime::createFromFormat(\App\Modules\Core\Utils\DateTime::DATETIME_FORMAT, $values->end)
				: null,
			'image' => $values->image ?: null,
			'rate' => $values->rate,
		]);

		// Add tags for event
		foreach ($values->tags as $tagValues) {
			if (!$tagValues->code) {
				continue;
			}

			$tag = $this->tagModel->getAll()->where(['code' => $tagValues->code])->fetch();
			$this->eventTagModel->insert([
				'event_id' => $event->id,
				'tag_id' => $tag->id,
				'rate' => $tagValues->rate,
			]);
		}
	}
}