<?php

namespace App\Modules\Admin\Presenters;

use App\Modules\Admin\Components\EventForm\EventFormFactory;
use App\Modules\Admin\Components\NotApprovedEventsTable\NotApprovedEventsTableFactory;
use App\Modules\Admin\Model\SourceService;
use App\Modules\Core\Model\EventModel;
use Nette\Utils\DateTime;


class EventsPresenter extends BasePresenter
{
	/** @var EventFormFactory @inject */
	public $eventFormFactory;

	/** @var EventModel @inject */
	public $eventModel;

	/** @var SourceService @inject */
	public $sourceService;

	/** @var NotApprovedEventsTableFactory @inject */
	public $notApprovedEventsTableFactory;


	public function actionUpdate($id)
	{
		$event = $this->eventModel->getAll()->wherePrimary($id)->fetch();

		$defaults = $event->toArray();
		$defaults['start'] = DateTime::from($defaults['start'])->format(\App\Modules\Core\Utils\DateTime::DATETIME_FORMAT);
		$defaults['end'] = $defaults['end']
			? DateTime::from($defaults['end'])->format(\App\Modules\Core\Utils\DateTime::DATETIME_FORMAT)
			: null;
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
			$this->flashMessage($this->translator->translate('admin.eventForm.success'), 'success');
			$this->redirect('Events:create');
		};

		$control->onUpdate[] = function() {
			$this->flashMessage($this->translator->translate('admin.eventForm.success'), 'success');
			$this->redirect('Events:update', ['id' => $this->getParameter('id')]);
		};

		return $control;
	}


	public function handleCrawlSources()
	{
		$addedEvents = $this->sourceService->crawlSources();

		if ($addedEvents > 0) {
			$this->flashMessage($this->translator->translate('admin.events.crawlSources.success',
				$addedEvents, ['events' => $addedEvents]), 'success');
		} else {
			$this->flashMessage($this->translator->translate('admin.events.crawlSources.noEvents'));
		}

		$this->redirect('this');
	}


	public function actionApprove($id)
	{
		$this->forward('update', $id);
	}


	public function createComponentNotApprovedEventsTable()
	{
		return $this->notApprovedEventsTableFactory->create(
			$this->eventModel->getAll()->where('state', EventModel::STATE_NOT_APPROVED)
		);
	}
}
