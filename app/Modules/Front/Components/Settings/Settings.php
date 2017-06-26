<?php declare(strict_types=1);

namespace App\Modules\Front\Components\Settings;

use App\Modules\Core\Components\AbstractBaseControl;
use App\Modules\Core\Components\Form\Form;
use App\Modules\Core\Model\UserModel;
use Nette\Security\User;
use Nette\Utils\Html;

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

    public function __construct(UserModel $userModel, User $user)
    {
        parent::__construct();
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
            'abroad_events' => (bool) $values->abroadEvents,
        ]);

        $this->onChange();
    }

    protected function createComponentForm(): Form
    {
        $form = new Form;
        $form->setTranslator($this->translator->domain('front.profile.settings.main'));
        $form->getElementPrototype()->addClass('ajax');

        // fa-envelope fa-envelope-o
        $form->addCheckbox(
            'newsletter',
            Html::el('span class="label-icon-end"')
                ->addText($this->translator->translate('front.profile.settings.main.newsletter'))
                ->addHtml(Html::el('i class="fa fa-envelope"'))
        );
        // fa-globe
        $form->addCheckbox('abroadEvents',
            Html::el('span class="label-icon-end"')
                ->addText($this->translator->translate('front.profile.settings.main.abroad'))
                ->addHtml(Html::el('i class="fa fa-globe"'))
        );

        $form->onSuccess[] = [$this, 'processForm'];

        return $form;
    }
}
