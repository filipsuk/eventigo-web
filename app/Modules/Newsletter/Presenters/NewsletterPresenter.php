<?php declare(strict_types=1);

namespace App\Modules\Newsletter\Presenters;

use App\Modules\Core\Model\UserModel;
use App\Modules\Core\Presenters\BasePresenter;
use App\Modules\Newsletter\Model\NewsletterService;
use App\Modules\Newsletter\Model\UserNewsletterModel;
use Nette\Database\Table\ActiveRow;
use Nette\Http\IResponse;


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

		if (!$this->userNewsletter) {
			$this->error(null, IResponse::S404_NOT_FOUND);
		}
	}


	public function renderDefault()
	{
		$newsletter = $this->userNewsletter;
		$this->template->userNewsletter = NewsletterService::inlineCss($newsletter->toArray());
	}


	public function actionUnsubscribe(string $hash)
	{
		$userNewsletter = $this->userNewsletterModel->getAll()->where(['hash' => $hash])->fetch();
		if ($userNewsletter) {
			$this->userModel->getAll()->wherePrimary($userNewsletter->user_id)->update([
				'newsletter' => false,
			]);

			$this->template->email = $userNewsletter->user->email;
		}
	}

	public function actionDynamic()
	{
		// Allow newsletter preview only for admins
		if (!$this->getUser()->isLoggedIn() || !$this->getUser()->isInRole('admin')) {
			$this->redirect(':Admin:Sign:in');
		}
	}

	public function renderDynamic(int $userId)
	{
		$newsletter = $this->newsletterService->buildArrayForTemplate((int)$userId);
		$this->template->newsletter = NewsletterService::inlineCss($newsletter);
	}
	
}
