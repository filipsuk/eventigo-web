<?php

namespace App\Modules\Admin\Presenters;

use App\Modules\Admin\Components\EventForm\EventFormFactory;
use App\Modules\Core\Model\EventModel;
use Nette\Utils\DateTime;


class EventsPresenter extends BasePresenter
{
	/** @var EventFormFactory @inject */
	public $eventFormFactory;

	/** @var EventModel @inject */
	public $eventModel;


	public function actionUpdate($id)
	{
		$event = $this->eventModel->getAll()->wherePrimary($id)->fetch();

		$defaults = $event->toArray();
		$defaults['start'] = DateTime::from($defaults['start'])->format(\App\Modules\Core\Utils\DateTime::DATETIME_FORMAT);
		$defaults['end'] = DateTime::from($defaults['end'])->format(\App\Modules\Core\Utils\DateTime::DATETIME_FORMAT);
		$defaults['tags'] = [];
		foreach ($event->related('events_tags') as $eventTag) {
			$defaults['tags'][] = [
				'code' => $eventTag->tag->code,
				'rate' => $eventTag->rate,
			];
		}
		$this['eventForm-form']->setDefaults($defaults);
	}


	public function renderUpdate()
	{
		$this->template->setFile(__DIR__ . '/templates/Events/create.latte');
	}
	

	public function createComponentEventForm()
	{
		$control = $this->eventFormFactory->create();

		$control->onCreate[] = function() {
			$this->flashMessage($this->translator->translate('admin.eventForm.success'));
			$this->redirect('Events:create');
		};

		$control->onUpdate[] = function() {
			$this->flashMessage($this->translator->translate('admin.eventForm.success'));
			$this->redirect('Events:update', ['id' => $this->getParameter('id')]);
		};

		return $control;
	}
}
