<?php

namespace App\Modules\Admin\Model;

use App\Modules\Core\Model\EventModel;
use App\Modules\Core\Model\EventSources\Facebook\FacebookEventSource;
use Nette\Database\Table\ActiveRow;
use Nette\Database\UniqueConstraintViolationException;
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
			$addedEvents += $this->crawlSource($source);
		}

		return $addedEvents;
	}


	public function crawlSource(ActiveRow $source) : int
	{
		$addedEvents = 0;

		$sourceUrl = new Url($source->url);
		// TODO make this universal
		if ($sourceUrl->getHost() === 'www.facebook.com') {
			$pageId = trim($sourceUrl->getPath(), '/');
			$events = $this->fbSource->getPageEvents($pageId);

			foreach ($events as $event) {
				$existingEvent = $this->eventModel->findExistingEvent($event);

				if (!$existingEvent) {
					try {
						$this->eventModel->insert([
							'name' => $event->getName(),
							'description' => $event->getDescription(),
							'start' => $event->getStart(),
							'end' => $event->getEnd(),
							'origin_url' => $event->getOriginUrl(),
							'image' => $event->getImage(),
							'rate' => $event->getRate(),
							'event_series_id' => $source->event_series_id,
						]);
					} catch (UniqueConstraintViolationException $e) {
						continue;
					}

					$addedEvents++;
				}
			}
		}

		return $addedEvents;
	}
}