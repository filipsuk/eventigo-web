<?php declare(strict_types=1);

namespace App\Modules\Front\Components\Subscription;

use App\Modules\Core\Components\BaseControl;
use App\Modules\Core\Components\Form\Form;
use App\Modules\Core\Model\UserModel;
use App\Modules\Front\Model\Exceptions\Subscription\EmailExistsException;
use Kdyby\Translation\Translator;
use Nette\Database\Table\IRow;


class Subscription extends BaseControl
{
	/**
	 * @var UserModel
	 */
	private $userModel;

	/**
	 * @var array
	 */
	public $onEmailExists = [];

	/**
	 * @var array
	 */
	public $onSuccess = [];


	public function __construct(Translator $translator, UserModel $userModel)
	{
		parent::__construct($translator);
		$this->userModel = $userModel;
	}


	protected function createComponentForm(): Form
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