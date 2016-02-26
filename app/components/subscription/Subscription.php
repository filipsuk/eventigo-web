<?php

namespace App\Components\Subscription;

use App\Components\BaseControl;
use App\Model\Exceptions\Subscription\EmailExistsException;
use App\Model\UserModel;
use Kdyby\Translation\Translator;
use Nette\Application\UI\Form;
use Nette\Database\Table\IRow;
use Nette\Forms\Controls\SubmitButton;
use Nette\Utils\ArrayHash;


class Subscription extends BaseControl
{
	/** @var UserModel */
	private $userModel;

	/** @var array */
	public $onEmailExists = [];

	/** @var array */
	public $onSuccess = [];


	public function __construct(Translator $translator, UserModel $userModel)
	{
		parent::__construct($translator);
		$this->userModel = $userModel;
	}


	public function createComponentForm()
	{
		$form = new Form;
		$form->setTranslator($this->translator->domain('front.subscription.form'));

		$form->addText('email', NULL, NULL, 254)
			->setAttribute('placeholder', 'email.placeholder')
			->setAttribute('type', 'email')
			->addCondition(Form::FILLED)
				->addRule(Form::EMAIL, 'email.validation');

		$form->addSubmit('subscribe', 'subscribe.label');

		$form->onSuccess[] = [$this, 'processForm'];

		return $form;
	}


	/**
	 * @throws EmailExistsException
	 */
	public function processForm(Form $form)
	{
		$values = $form->getValues();
		$user = $this->subscribe($values->email);
		$this->onSuccess($user->email);
	}


	/**
	 * @param string $email
	 * @return IRow|null
	 * @throws EmailExistsException
	 * @throws \PDOException
	 */
	protected function subscribe($email)
	{
		try {
			$user = $this->userModel->subscribe($email);

			if ($this->reflection->name === __CLASS__) {
				$this->onSuccess($user->email);
			} else {
				return $user;
			}

		} catch (EmailExistsException $e) {
			$this->onEmailExists($email);
		}
	}
}