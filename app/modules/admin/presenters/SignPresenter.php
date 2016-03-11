<?php

namespace App\Modules\Admin\Presenters;

use App\Modules\Admin\Components\SignInFormFactory;


class SignPresenter extends \Nette\Application\UI\Presenter
{
	/** @var \Kdyby\Translation\Translator @inject */
	public $translator;

	/** @var SignInFormFactory @inject */
	public $signInFormFactory;


	public function actionIn()
	{
		if ($this->getUser()->isLoggedIn()) {
			$this->redirect('Dashboard:');
		}
	}


	public function createComponentSignInForm()
	{
		$control = $this->signInFormFactory->create();

		$control->onLoggedIn[] = function() {
			$this->redirect('Dashboard:');
		};

		$control->onIncorrectLogIn[] = function() {
			$this->flashMessage($this->translator->translate('admin.signInForm.incorrectLogIn'), 'danger');
			$this->redirect('this');
		};

		return $control;
	}


	public function actionOut()
	{
		$this->getUser()->logout(true);
		$this->redirect('in');
	}
}
