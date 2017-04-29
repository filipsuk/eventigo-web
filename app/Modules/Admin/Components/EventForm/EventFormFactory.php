<?php declare(strict_types=1);

namespace App\Modules\Admin\Components\EventForm;

interface EventFormFactory
{
	public function create(): EventForm;
}