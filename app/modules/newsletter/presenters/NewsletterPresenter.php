<?php

namespace App\Modules\Newsletter\Presenters;

use App\Modules\Core\Model\UserModel;
use App\Modules\Core\Presenters\BasePresenter;
use App\Modules\Newsletter\Model\NewsletterService;
use App\Modules\Newsletter\Model\UserNewsletterModel;
use Nette\Database\Table\ActiveRow;
use Pelago\Emogrifier;
use TijsVerkoyen\CssToInlineStyles\CssToInlineStyles;
use Tracy\Debugger;


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

	/** Path to css file used for css inline of newsletter texts html */
	const CSS_FILE_PATH = __DIR__ . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . 'Newsletter' . DIRECTORY_SEPARATOR . 'build.css';

	public function actionDefault($hash)
	{
		$this->userNewsletter = $this->userNewsletterModel->getAll()->where([
			'hash' => $hash,
		])->fetch();
	}


	public function renderDefault()
	{
		$this->template->userNewsletter = $this->userNewsletter;
		$this->inlineCss();
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
		$this->inlineCss();
	}

	/**
	 * Inline CSS styles of intro and outro text in newsletter
	 * TODO: Move this to admin when saving new newsletter
	 */
	private function inlineCss() {
		$css = file_get_contents(self::CSS_FILE_PATH);
		$newsletter =& $this->template->newsletter;
		$emogrifier = new Emogrifier();
		$emogrifier->setCss($css);

		try {
			// Inline CSS of intro text
			if (!empty($newsletter['intro_text'])) {
				$emogrifier->setHtml($newsletter['intro_text']);
				$newsletter['intro_text'] = $emogrifier->emogrifyBodyContent();
			}

			// Inline CSS of outro text
			if (!empty($newsletter['outro_text'])) {
				$emogrifier->setHtml($newsletter['outro_text']);
				$newsletter['outro_text'] = $emogrifier->emogrifyBodyContent();
			}

		} catch (\BadMethodCallException $e) {
			Debugger::log($e->getMessage());
		}

	}
}
