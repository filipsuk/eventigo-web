<?php

namespace App\Modules\Newsletter\Presenters;

use App\Modules\Core\Model\UserModel;
use App\Modules\Core\Presenters\BasePresenter;
use App\Modules\Newsletter\Model\UserNewsletterModel;
use Nette\Database\Table\ActiveRow;


class NewsletterPresenter extends BasePresenter
{
	/** @var UserNewsletterModel @inject */
	public $userNewsletterModel;

	/** @var UserModel @inject */
	public $userModel;

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
}
