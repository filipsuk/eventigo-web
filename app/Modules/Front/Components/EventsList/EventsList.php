<?php declare(strict_types=1);

namespace App\Modules\Front\Components\EventsList;

use App\Modules\Core\Components\AbstractBaseControl;
use App\Modules\Front\Model\EventsIterator;

final class EventsList extends AbstractBaseControl
{
    /**
     * @var mixed[]
     */
    private $events = [];

    /**
     * @param mixed[] $events
     */
    public function __construct(array $events)
    {
        $this->events = $events;
    }

    public function render(): void
    {
        $this->template->events = new EventsIterator($this->events);
        $this->template->render();
    }
}
