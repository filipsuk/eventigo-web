<?php declare(strict_types=1);

namespace App\Modules\Api\V1\Model;

use App\Modules\Core\Model\Entity\Event;
use App\Modules\Core\Model\EventModel;
use Nette\Application\LinkGenerator;
use Nette\Utils\DateTime;

final class EventApiService
{
    /**
     * @var EventModel
     */
    private $eventModel;

    /**
     * @var LinkGenerator
     */
    private $linkGenerator;

    public function __construct(EventModel $eventModel, LinkGenerator $linkGenerator)
    {
        $this->eventModel = $eventModel;
        $this->linkGenerator = $linkGenerator;
    }

    /**
     * @return Event[]
     */
    public function getEvents(): array
    {
        $events = [];

        foreach ($this->eventModel->getAllWithDates([], new DateTime) as $eventRow) {
            $event = Event::createFromRow($eventRow);
            $eventTags = [];
            foreach ($this->eventModel->getEventTags($eventRow) as $eventTag) {
                $eventTags[] = [
                    'code' => $eventTag->getTag()->getCode(),
                    'name' => $eventTag->getTag()->getName(),
                    'rate' => $eventTag->getRate()
                ];
            };
            $events[] = [
                'id' => $event->getHash(),
                'name' => $event->getName(),
                'description' => $event->getDescription(),
                'url' => $this->linkGenerator->link('Front:Redirect:', [$event->getOriginUrl()]),
                'start' => $event->getStart()->jsonSerialize(),
                'end' => $event->getEnd() ? $event->getEnd()->jsonSerialize() : null,
                'venue' => $event->getVenue(),
                'country' => $event->getCountryCode(),
                'image' => $event->getImage(),
                'tags' => $eventTags
            ];
        }

        return $events;
    }
}
