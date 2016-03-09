<?php

namespace App\Modules\Admin\Presenters;

use App\Modules\Admin\Components\EventForm\EventFormFactory;
use App\Modules\Core\Presenters\BasePresenter;


class EventsPresenter extends BasePresenter
{
	/** @var EventFormFactory @inject */
	public $eventFormFactory;


	public function createComponentEventForm()
	{
		$control = $this->eventFormFactory->create();

		$control->onSuccess[] = function() {
			$this->flashMessage($this->translator->translate('admin.eventForm.success'));
			$this->redirect('Events:create');
		};

		return $control;
	}
}
