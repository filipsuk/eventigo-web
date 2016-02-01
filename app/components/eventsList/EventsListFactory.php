<?php

namespace App\Components\EventsList;


interface EventsListFactory
{
	/**
	 * @param array $events
	 * @return EventsList
	 */
	public function create(array $events);
}
