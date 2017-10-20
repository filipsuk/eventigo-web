<?php declare(strict_types=1);

namespace App\Modules\Core\Presenters;

use App\Modules\Core\Model\UserModel;
use App\Modules\Core\Utils\Filters;
use Nette\Application\BadRequestException;
use Nette\Application\UI\Presenter;
use Nette\Bridges\ApplicationLatte\Template;
use Nette\Security\AuthenticationException;
use Nette\Security\Identity;
use Tracy\Debugger;
use Tracy\ILogger;

abstract class AbstractBasePresenter extends Presenter
{
    /**
     * @var \Kdyby\Translation\Translator
     * @inject
     */
    public $translator;

    /**
     * @var UserModel
     * @inject
     */
    public $userModel;

    protected function createTemplate(): Template
    {
        /** @var Template $template */
        $template = parent::createTemplate();

        Filters::setTranslator($this->translator);
        $template->addFilter(null, [Filters::class, 'loader']);

        return $template;
    }

    protected function beforeRender(): void
    {
        parent::beforeRender();

        $this->template->parameters = $this->context->getParameters();
    }

    /**
     * Log in the user by token (usually provided in url).
     *
     * @throws BadRequestException
     */
    protected function loginWithToken(?string $token = null): void
    {
        if (! $this->getUser()->isLoggedIn()) {
            if ($token === null || ($user = $this->userModel->getAll()->where('token', $token)->fetch()) === false
            ) {
                Debugger::log(sprintf(
                    'Invalid user token "%s". Can not login.',
                    $token
                ));

                throw new BadRequestException;
            }

            try {
                $this->getUser()->login(new Identity($user->id, null, $user->toArray()));
            } catch (AuthenticationException $e) {
                Debugger::log($e->getMessage(), ILogger::EXCEPTION);
            }
        }
    }
}
