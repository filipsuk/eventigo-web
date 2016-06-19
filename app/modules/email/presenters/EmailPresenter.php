<?php

namespace App\Modules\Email\Presenters;

use App\Modules\Core\Model\UserModel;
use App\Modules\Core\Presenters\BasePresenter;
use App\Modules\Email\Model\Entity\BasicEmail;
use Nette\Http\Url;


/**
 * EmailPresenter is used for rendering different types of emails.
 * @package App\Modules\Email\Presenters
 */
class EmailPresenter extends BasePresenter
{
	/** @var UserModel @inject */
	public $userModel;

	const TEMPLATE_DIR = __DIR__ . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . 'Email';
	const BASIC_EMAIL_TEMPLATE_FILE = self::TEMPLATE_DIR . DIRECTORY_SEPARATOR . 'basic.latte';

	/**
	 * Renders login email with provided user token.
	 *
	 * @param $token string User's token used to log in the user.
	 * @throws \InvalidArgumentException
	 * @throws \Nette\Application\UI\InvalidLinkException
	 * @throws \Nette\InvalidArgumentException
	 */
	public function renderLogin($token)
	{
		$email = new BasicEmail();
		$email->setIntroText($this->translator->trans('email.login.text'));
		$email->setButtonText($this->translator->trans('email.login.loginButton'));
		$email->setButtonUrl(new Url($this->link('//:Front:Homepage:default',  null, $token)));
		$email->setFooterText($this->translator->trans('email.login.footerText'));

		$this->template->setFile(self::BASIC_EMAIL_TEMPLATE_FILE);
		$this->template->email = $email;
	}

}
