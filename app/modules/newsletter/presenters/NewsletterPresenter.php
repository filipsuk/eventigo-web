<?php

namespace App\Modules\Newsletter\Presenters;

use App\Modules\Core\Model\UserModel;
use App\Modules\Core\Presenters\BasePresenter;
use App\Modules\Newsletter\Model\NewsletterService;
use App\Modules\Newsletter\Model\UserNewsletterModel;
use Nette\Database\Table\ActiveRow;


class NewsletterPresenter extends BasePresenter
{
	/** @var UserNewsletterModel @inject */
	public $userNewsletterModel;

	/** @var UserModel @inject */
	public $userModel;
	
	/** @var NewsletterService @inject */
	public $newsletterService;

	/** @var ActiveRow */
	private $userNewsletter;


	public function actionDefault($hash)
	{
		$this->userNewsletter = $this->userNewsletterModel->getAll()->where([
			'hash' => $hash,
		])->fetch();
	}


	public function renderDefault()
	{
		$this->template->userNewsletter = $this->userNewsletter;
	}


	public function actionUnsubscribe($hash)
	{
		$userNewsletter = $this->userNewsletterModel->getAll()->where(['hash' => $hash])->fetch();
		if ($userNewsletter) {
			$this->userModel->getAll()->wherePrimary($userNewsletter->user_id)->update([
				'newsletter' => false,
			]);

			$this->template->email = $userNewsletter->user->email;
		}
	}
	
	public function renderDynamic($userId)
	{
		$this->template->newsletter = $this->newsletterService->buildArrayForTemplate((int)$userId);
		// TODO Convert utf8 chars to html entities. Probably for email clients not respecting charset header. mb_convert_encoding($string, 'HTML-ENTITIES', 'UTF-8');
	}
}
