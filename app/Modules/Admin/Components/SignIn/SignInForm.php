<?php

namespace App\Modules\Admin\Components\SignIn;

use App\Modules\Core\Components\BaseControl;
use App\Modules\Core\Components\Form\Form;
use App\Modules\Core\Model\UserModel;
use Nette\Security\AuthenticationException;


class SignInForm extends BaseControl
{
	/** @var array */
	public $onLoggedIn = [];

	/** @var array */
	public $onIncorrectLogIn = [];


	public function render()
	{
		$this['form']->render();
	}


	public function createComponentForm()
	{
		$form = new Form;
		$form->setTranslator($this->translator->domain('admin.signInForm'));

		$form->addText('email', 'email')
			->setAttribute('placeholder', 'email.placeholder')
			->setRequired('email.required');
		$form->addPassword('password', 'password')
			->setAttribute('placeholder', 'password.placeholder')
			->setRequired('password.required');

		$form->addSubmit('signIn', 'signIn')->setAttribute('class', 'btn btn-success');
		$form->onSuccess[] = [$this, 'processForm'];
		
		return  $form;
	}


	public function processForm(Form $form)
	{
		$values = $form->getValues();

		try {
			$this->getPresenter()->getUser()->login(UserModel::ADMIN_LOGIN, $values->email, $values->password);

		} catch (AuthenticationException $e) {
			$this->onIncorrectLogIn();
		}

		$this->onLoggedIn();
	}
}