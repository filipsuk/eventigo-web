<?php

namespace App\Components\Subscription;

use App\Components\BaseControl;
use App\Model\UserModel;
use Kdyby\Translation\Translator;
use Nette\Application\UI\Form;


class Subscription extends BaseControl
{
	/** @var UserModel */
	private $userModel;

	/** @var array */
	public $onExists = [];

	/** @var array */
	public $onSuccess = [];


	public function __construct(Translator $translator, UserModel $userModel)
	{
		parent::__construct($translator);
		$this->userModel = $userModel;
	}


	public function createComponentForm() : Form
	{
		$form = new Form;
		$form->setTranslator($this->translator->domain('front.subscription.form'));

		$form->addText('email', NULL, NULL, 254)
			->setAttribute('placeholder', 'email.placeholder')
			->setRequired('email.required')
			->addRule(Form::EMAIL, 'email.validation');
		$form->addSubmit('subscribe', 'subscribe.label');

		$form->onSuccess[] = [$this, 'processForm'];

		return $form;
	}


	public function processForm(Form $form)
	{
		$values = $form->getValues();

		if ($this->userModel->emailExists($values->email)) {
			$this->onExists($values->email);
		} else {
			$this->userModel->insert([
				'email' => $values->email,
			]);
			$this->onSuccess($values->email);
		}
	}
}