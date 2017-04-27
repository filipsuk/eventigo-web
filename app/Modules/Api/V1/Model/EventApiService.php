<?php declare(strict_types=1);

namespace App\Modules\Api\V1\Model;

use App\Modules\Core\Model\Entity\Event;
use App\Modules\Core\Model\EventModel;
use Nette\Application\LinkGenerator;
use Nette\Utils\DateTime;

class EventApiService
{
	/** @var EventModel */
	private $eventModel;

	/** @var LinkGenerator */
	private $linkGenerator;

	public function __construct(EventModel $eventModel, LinkGenerator $linkGenerator)
	{
		$this->eventModel = $eventModel;
		$this->linkGenerator = $linkGenerator;
	}

	public function getEvents()
	{
		$events = [];

		foreach ($this->eventModel->getAllWithDates([], new DateTime()) as $eventRow) {
			$event = Event::createFromRow($eventRow);
			$eventTags = [];
			foreach ($this->eventModel->getEventTags($eventRow) as $eventTag) {
				$eventTags[] = [
					'id' => $eventTag->getTag()->getId(),
					'rate' => $eventTag->getRate(),
					'name' => $eventTag->getTag()->getName(),
					'code' => $eventTag->getTag()->getCode(),
				];
			};
			$events[] = [
				'id' => $event->getId(),
				'name' => $event->getName(),
				'description' => $event->getDescription(),
				'url' => $this->linkGenerator->link("Front:Redirect:", [$event->getOriginUrl()]),
				'start' => $event->getStart()->jsonSerialize(),
				'end' => $event->getEnd() ? $event->getEnd()->jsonSerialize() : null,
				'image' => $event->getImage(),
				'tags' => $eventTags
			];
		}

		return $events;
	}
}
