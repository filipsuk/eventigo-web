<?php

namespace App\Presenters;

use App\Model\EventModel;
use App\Model\TagModel;


class HomepagePresenter extends BasePresenter
{
	/** @var EventModel @inject */
	public $eventModel;

	/** @var TagModel @inject */
	public $tagModel;


	public function renderDefault()
	{
		$this->template->events = $this->eventModel->getAll()->fetchAll();
		$this->template->eventModel = $this->eventModel;
		$this->template->tags = $this->tagModel->getAll();

		// Get array of all tags
		$allTags = [];
		foreach ($this->template->tags as $tag) {
			$allTags[] = $tag->code;
		}
		$this->template->allTags = $allTags;

		$this->template->eventsMaxCount = 10;
	}

}
