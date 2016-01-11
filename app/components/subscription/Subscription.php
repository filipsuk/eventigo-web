<?php

namespace App\Components\Subscription;

use App\Components\BaseControl;
use App\Model\Exceptions\Subscription\EmailExistsException;
use App\Model\UserModel;
use Kdyby\Translation\Translator;
use Nette\Application\UI\Form;
use Nette\Database\Table\IRow;


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


	/**
	 * @throws EmailExistsException
	 */
	public function processForm(Form $form) : IRow
	{
		$values = $form->getValues();

		if ($this->userModel->emailExists($values->email)) {
			if ($this->reflection->name == __CLASS__) {
				$this->onExists($values->email);
			} else {
				throw new EmailExistsException;
			}

		} else {
			$user = $this->userModel->insert([
				'email' => $values->email,
			]);

			if ($this->reflection->name == __CLASS__) {
				$this->onSuccess($values->email);
			}
		}

		return $user;
	}
}