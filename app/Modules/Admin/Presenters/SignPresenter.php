<?php declare(strict_types=1);

namespace App\Modules\Admin\Presenters;

use App\Modules\Admin\Components\SignIn\SignInForm;
use App\Modules\Admin\Components\SignIn\SignInFormFactoryInterface;
use Kdyby\Translation\Translator;
use Nette\Application\UI\Presenter;

final class SignPresenter extends Presenter
{
    /**
     * @var Translator
     */
    private $translator;

    /**
     * @var SignInFormFactoryInterface
     */
    private $signInFormFactory;

    public function __construct(SignInFormFactoryInterface $signInFormFactory, Translator $translator)
    {
        $this->signInFormFactory = $signInFormFactory;
        $this->translator = $translator;
    }

    public function actionIn(): void
    {
        if ($this->user->isLoggedIn() && $this->user->isInRole('admin')) {
            $this->redirect('Dashboard:');
        }
    }

    public function actionOut(): void
    {
        if (! $this->user->isLoggedIn()) {
            $this->redirect('in');
        }

        if ($this->user->isInRole('admin')) {
            $this->user->logout(true);
        }

        $this->redirect('in');
    }

    protected function createComponentSignInForm(): SignInForm
    {
        $control = $this->signInFormFactory->create();

        $control->onLoggedIn[] = function (): void {
            $this->redirect('Dashboard:');
        };

        $control->onIncorrectLogIn[] = function (): void {
            $this->flashMessage($this->translator->translate('admin.signInForm.incorrectLogIn'), 'danger');
            $this->redirect('this');
        };

        return $control;
    }
}
