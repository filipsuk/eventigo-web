<?php

namespace App\Modules\Newsletter\Presenters;

use App\Modules\Core\Presenters\BasePresenter;
use App\Modules\Newsletter\Model\UserNewsletterModel;
use Nette\Database\Table\ActiveRow;


class NewsletterPresenter extends BasePresenter
{
	/** @var UserNewsletterModel @inject */
	public $userNewsletterModel;

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


	public function renderDefault()
	{
		$this->template->userNewsletter = $this->userNewsletter;
	}
}
