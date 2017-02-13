<?php declare(strict_types=1);

namespace App\Modules\Front\Components\Settings;

use App\Modules\Core\Components\BaseControl;
use App\Modules\Core\Components\Form\Form;
use App\Modules\Core\Model\UserModel;
use Kdyby\Translation\Translator;
use Nette\Security\User;


class Settings extends BaseControl
{
	/** @var array */
	public $onChange = [];

	/** @var User */
	private $user;

	/** @var UserModel */
	private $userModel;


	public function __construct(Translator $translator,
	                            UserModel $userModel,
	                            User $user)
	{
		parent::__construct($translator);
		$this->user = $user;
		$this->userModel = $userModel;
	}


	public function render()
	{
		$this['form']->render();
	}


	public function createComponentForm()
	{
		$form = new Form;
		$form->setTranslator($this->translator->domain('front.profile.settings.main'));
		$form->getElementPrototype()->addClass('ajax');

		$form->addCheckbox('newsletter', 'newsletter');

		$form->onSuccess[] = [$this, 'processForm'];

		return $form;
	}


	public function processForm(Form $form)
	{
		$values = $form->getValues();

		$this->userModel->getAll()->wherePrimary($this->user->getId())->update([
			'newsletter' => (bool)$values->newsletter,
		]);

		$this->onChange();
	}
}
