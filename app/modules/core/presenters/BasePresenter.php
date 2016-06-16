<?php

namespace App\Modules\Core\Presenters;

use Nette;
use Nette\Application\BadRequestException;
use Nette\Security\Identity;
use Nette\Utils\DateTime;
use Tracy\Debugger;
use Tracy\ILogger;


/**
 * Base presenter for all application presenters.
 */
abstract class BasePresenter extends Nette\Application\UI\Presenter
{
	/** @var \Kdyby\Translation\Translator @inject */
	public $translator;
	
	protected function createTemplate()
	{
		$template = parent::createTemplate();

		$template->addFilter('datetime', function (DateTime $a, DateTime $b = null) {
			\App\Modules\Core\Utils\DateTime::setTranslator($this->translator);
			return \App\Modules\Core\Utils\DateTime::eventsDatetimeFilter($a, $b);
		});

		$template->addFilter('username', function (Nette\Security\Identity $identity) {
			return $identity->fullname ?: $identity->email ?: $this->translator->translate('front.nav.user');
		});

		return $template;
	}

	protected function beforeRender()
	{
		parent::beforeRender();

		$this->template->parameters = $this->context->getParameters();
	}

	/**
	 * Log in the user by token (usually provided in url)
	 *
	 * @param $token
	 * @throws BadRequestException
	 */
	protected function loginWithToken($token)
	{
		if (!$this->getUser()->isLoggedIn()) {
			if ($token === null || ($user = $this->userModel->getAll()
					->where('token', $token)->fetch()) === false
			) {
				Debugger::log("Invalid user token. Can't login. (token: $token)");
				throw new BadRequestException();
			}
			try {
				$this->getUser()->login(new Identity($user->id, null, $user->toArray()));
			} catch (Nette\Security\AuthenticationException $e) {
				Debugger::log($e->getMessage(), ILogger::EXCEPTION);
			}
		}
	}

}
