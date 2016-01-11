<?php

namespace App\Presenters;

use App\Components\SubscriptionTags\ISubscriptionTagsFactory;
use App\Model\EventModel;
use App\Model\TagModel;


class HomepagePresenter extends BasePresenter
{
	/** @var EventModel @inject */
	public $eventModel;

	/** @var TagModel @inject */
	public $tagModel;

	/** @var ISubscriptionTagsFactory @inject */
	public $subscriptionTags;


	public function renderDefault()
	{
		$this->template->events = $this->eventModel->getAll()->fetchAll();
		$this->template->eventModel = $this->eventModel;
		$this->template->tags = $this->tagModel->getAll();

		// Get array of all tags
		$allTags = [];
		foreach ($this->template->tags as $tag) {
			$allTags[] = $tag->code;
		}
		$this->template->allTags = $allTags;

		$this->template->eventsMaxCount = 10;
	}


	public function createComponentSubscriptionTags()
	{
		$control = $this->subscriptionTags->create();
		$control->onExists[] = function (string $email) {
			$this->flashMessage($this->translator->translate('front.subscription.message.emailExists', ['email' => $email]));
			$this->redirect('this');
		};
		$control->onSuccess[] = function (string $email) {
			$this->flashMessage($this->translator->translate('front.subscription.message.success', ['email' => $email]));
			$this->redirect('this');
		};
		return $control;
	}
}
