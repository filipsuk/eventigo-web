<?php declare(strict_types=1);

namespace App\Modules\Admin\Components\SignIn;

use App\Modules\Core\Components\AbstractBaseControl;
use App\Modules\Core\Components\Form\Form;
use App\Modules\Core\Model\UserModel;
use Nette\Security\AuthenticationException;

final class SignInForm extends AbstractBaseControl
{
    /**
     * @var callable[]
     */
    public $onLoggedIn = [];

    /**
     * @var callable[]
     */
    public $onIncorrectLogIn = [];

    public function render(): void
    {
        $this['form']->render();
    }

    public function processForm(Form $form): void
    {
        $values = $form->getValues();

        try {
            $this->getPresenter()->getUser()->login(UserModel::ADMIN_LOGIN, $values->email, $values->password);
        } catch (AuthenticationException $e) {
            $this->onIncorrectLogIn();
        }

        $this->onLoggedIn();
    }

    protected function createComponentForm(): Form
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

        return $form;
    }
}
