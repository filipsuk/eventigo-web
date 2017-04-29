<?php declare(strict_types=1);

namespace App\Modules\Front\Components\EventsList;

interface EventsListFactory
{
	public function create(array $events): EventsList;
}
