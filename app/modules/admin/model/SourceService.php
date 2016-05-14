<?php

namespace App\Modules\Admin\Model;

use App\Modules\Core\Model\EventModel;
use App\Modules\Core\Model\EventSources\Facebook\FacebookEventSource;
use Nette\Http\Url;


class SourceService
{
	/** @var SourceModel @inject */
	public $sourceModel;

	/** @var FacebookEventSource @inject */
	public $fbSource;

	/** @var EventModel @inject */
	public $eventModel;


	public function crawlSources() : int
	{
		$addedEvents = 0;

		// TODO Check sources by frequency
		$sources = $this->sourceModel->getAll();
		foreach($sources as $source) {
			$sourceUrl = new Url($source->url);
			// TODO make this universal
			if ($sourceUrl->getHost() === 'www.facebook.com') {
				$pageId = trim($sourceUrl->getPath(), '/');
				$events = $this->fbSource->getPageEvents($pageId);

				foreach ($events as $event) {
					$existingEvent = $this->eventModel->findExistingEvent($event);

					if (!$existingEvent) {
						$this->eventModel->insert([
							'name' => $event->getName(),
							'description' => $event->getDescription(),
							'start' => $event->getStart(),
							'end' => $event->getEnd(),
							'origin_url' => $event->getOriginUrl(),
							'image' => $event->getImage(),
							'rate' => $event->getRate(),
						]);

						$addedEvents++;
					}
				}
			}
		}

		return $addedEvents;
	}
}