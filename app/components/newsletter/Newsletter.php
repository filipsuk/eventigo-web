<?php

namespace App\Components\Newsletter;

use App\Components\BaseControl;
use App\Components\EventsList\EventsListFactory;
use Kdyby\Translation\Translator;


class Newsletter extends BaseControl
{
	/** @var array */
	private $events;

	/** @var \App\Components\EventsList\EventsListFactory */
	private $eventsListFactory;


	public function __construct(Translator $translator,
								EventsListFactory $eventsListFactory,
	                            array $events)
	{
		parent::__construct($translator);
		$this->events = $events;
		$this->eventsListFactory = $eventsListFactory;
	}


	public function render()
	{
		$this->template->render();
	}


	public function createComponentEventsList()
	{
		return $this->eventsListFactory->create($this->events);
	}
}