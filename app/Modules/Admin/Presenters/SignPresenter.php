<?php declare(strict_types=1);

namespace App\Modules\Admin\Presenters;

use App\Modules\Admin\Components\SignIn\SignInFormFactoryInterface;
use Nette\Application\UI\Presenter;


final class SignPresenter extends Presenter
{
	/**
	 * @var \Kdyby\Translation\Translator @inject
	 */
	public $translator;

	/**
	 * @var SignInFormFactoryInterface @inject
	 */
	public $signInFormFactory;


	public function actionIn()
	{
		if ($this->user->isLoggedIn() && $this->user->isInRole('admin')) {
			$this->redirect('Dashboard:');
		}
	}


	protected function createComponentSignInForm()
	{
		$control = $this->signInFormFactory->create();

		$control->onLoggedIn[] = function () {
			$this->redirect('Dashboard:');
		};

		$control->onIncorrectLogIn[] = function () {
			$this->flashMessage($this->translator->translate('admin.signInForm.incorrectLogIn'), 'danger');
			$this->redirect('this');
		};

		return $control;
	}


	public function actionOut()
	{
		if (!$this->user->isLoggedIn()) {
			$this->redirect('in');
		}

		if ($this->user->isInRole('admin')) {
			$this->user->logout(true);
		}

		$this->redirect('in');
	}
}
