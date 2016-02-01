<?php

namespace App\Presenters;

use App\Components\EventsList\EventsListFactory;
use App\Components\SubscriptionTags\ISubscriptionTagsFactory;
use App\Model\EventModel;
use App\Model\TagModel;
use Nette\Utils\DateTime;
use Nette\Utils\Html;


class HomepagePresenter extends BasePresenter
{
	/** @var EventModel @inject */
	public $eventModel;

	/** @var TagModel @inject */
	public $tagModel;

	/** @var ISubscriptionTagsFactory @inject */
	public $subscriptionTags;

	/** @var EventsListFactory @inject */
	public $eventsListFactory;


	/**
	 * @param string[] $tags
	 */
	public function renderDefault(array $tags)
	{
		if (!$tags) {
			$section = $this->getSession('subscriptionTags');
			$tags = $section->tags;
		}

		$this->template->eventModel = $this->eventModel;
		$this->template->tags = $this->tagModel->getAll();

		// Get array of all tags
		$allTags = [];
		foreach ($this->template->tags as $tag) {
			$allTags[] = $tag->code;
		}
		$this->template->allTags = $allTags;

		$this['subscriptionTags']['form']->setDefaults(['tags' => $tags]);
	}


	public function createComponentSubscriptionTags()
	{
		$control = $this->subscriptionTags->create();

		$control->onEmailExists[] = function (string $email) {
			$this['eventsList']->redrawControl();

			$this->flashMessage($this->translator->translate('front.subscription.message.emailExists',
				['email' => Html::el('strong')->setText($email)]));
			$this->redrawControl('flash-messages');
		};

		$control->onSuccess[] = function (string $email) {
			$this['eventsList']->redrawControl();

			$this->flashMessage($this->translator->translate('front.subscription.message.success',
				['email' => Html::el('strong')->setText($email)]));
			$this->redrawControl('flash-messages');
		};

		$control->onChange[] = function () {
			$this['eventsList']->redrawControl();
			$this->redrawControl('flash-messages');
		};

		return $control;
	}


	public function createComponentEventsList()
	{
		$section = $this->getSession('subscriptionTags');
		$tags = $section->tags;

		$tagsIds = $this->tagModel->getAll()->where('code', $tags)->fetchPairs(null, 'id');
		$events = $this->eventModel->getAllWithDates($tagsIds, new DateTime);
		return $this->eventsListFactory->create($events);
	}
}
