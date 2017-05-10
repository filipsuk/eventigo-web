<?php declare(strict_types=1);

namespace App\Modules\Front\Components\Settings;

use App\Modules\Core\Components\AbstractBaseControl;
use App\Modules\Core\Components\Form\Form;
use App\Modules\Core\Model\UserModel;
use Kdyby\Translation\Translator;
use Nette\Security\User;

final class Settings extends AbstractBaseControl
{
    /**
     * @var callable[]
     */
    public $onChange = [];

    /**
     * @var User
     */
    private $user;

    /**
     * @var UserModel
     */
    private $userModel;

    public function __construct(Translator $translator, UserModel $userModel, User $user)
    {
        parent::__construct($translator);
        $this->user = $user;
        $this->userModel = $userModel;
    }

    public function render(): void
    {
        $this['form']->render();
    }

    public function processForm(Form $form): void
    {
        $values = $form->getValues();

        $this->userModel->getAll()->wherePrimary($this->user->getId())->update([
            'newsletter' => (bool) $values->newsletter,
        ]);

        $this->onChange();
    }

    protected function createComponentForm(): Form
    {
        $form = new Form;
        $form->setTranslator($this->translator->domain('front.profile.settings.main'));
        $form->getElementPrototype()->addClass('ajax');

        $form->addCheckbox('newsletter', 'newsletter');

        $form->onSuccess[] = [$this, 'processForm'];

        return $form;
    }
}
