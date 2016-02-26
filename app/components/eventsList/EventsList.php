<?php

namespace App\Components\EventsList;

use App\Components\BaseControl;
use App\Model\EventsIterator;
use Kdyby\Translation\Translator;


class EventsList extends BaseControl
{
	/** @var array */
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