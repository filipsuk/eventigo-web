<?php

namespace App\Presenters;

use App\Components\Newsletter\NewsletterFactory;
use App\Model\EventModel;
use App\Model\TagModel;
use App\Model\UserNewsletterModel;
use App\Model\UserTagModel;
use Nette\Database\Table\ActiveRow;
use Nette\Utils\DateTime;
use Nette\Utils\Json;


class NewsletterPresenter extends BasePresenter
{
	/** @var NewsletterFactory @inject */
	public $newsletterFactory;

	/** @var TagModel @inject */
	public $tagModel;

	/** @var EventModel @inject */
	public $eventModel;

	/** @var UserNewsletterModel @inject */
	public $userNewsletterModel;

	/** @var UserTagModel @inject */
	public $userTagModel;

	/** @var ActiveRow */
	private $userNewsletter;


	/**
	 * @param string $hash
	 */
	public function actionDefault($hash)
	{
		$this->userNewsletter = $this->userNewsletterModel->getAll()->where([
			'hash' => $hash,
		])->fetch();
	}


	public function createComponentNewsletter()
	{
		if ($this->userNewsletter->variables) {
			$tagsCodes = Json::decode($this->userNewsletter->variables, Json::FORCE_ARRAY);
			$tagsIds = $this->tagModel->getAll()
				->where(['code' => $tagsCodes])
				->fetchPairs(NULL, 'id');
		} else {
			$tagsIds = $this->userNewsletter->user->related('users_tags')->fetchPairs(NULL, 'tag_id');
		}

		$from = $this->userNewsletter->sent ?: new DateTime;
		$to = $this->userNewsletter->sent;
		$events = $this->eventModel->getAllWithDates($tagsIds, $from, $to);
		return $this->newsletterFactory->create($events);
	}
}
