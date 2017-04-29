<?php declare(strict_types=1);

namespace App\Modules\Front\Components\EventsList;

use App\Modules\Core\Components\BaseControl;
use App\Modules\Front\Model\EventsIterator;
use Kdyby\Translation\Translator;


class EventsList extends BaseControl
{
	/**
	 * @var array
	 */
	private $events;


	public function __construct(Translator $translator, array $events)
	{
		parent::__construct($translator);
		$this->events = $events;
	}


	public function render()
	{
		$this->template->events = new EventsIterator($this->events);
		$this->template->render();
	}
}