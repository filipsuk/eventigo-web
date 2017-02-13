<?php declare(strict_types=1);

namespace App\Modules\Email\Presenters;

use App\Modules\Core\Model\UserModel;
use App\Modules\Core\Presenters\BasePresenter;
use App\Modules\Email\Model\EmailService;
use App\Modules\Email\Model\Entity\BasicEmail;
use Latte\Loaders\StringLoader;
use Nette\Http\Url;


/**
 * EmailPresenter is used for rendering different types of emails.
 * @package App\Modules\Email\Presenters
 */
class EmailPresenter extends BasePresenter
{
	const TEMPLATE_DIR = __DIR__ . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . 'Email';
	const BASIC_EMAIL_TEMPLATE_FILE = self::TEMPLATE_DIR . DIRECTORY_SEPARATOR . 'basic.latte';

	/** @var UserModel @inject */
	public $userModel;

	/** @var EmailService @inject */
	public $emailService;


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
		$this->template->getLatte()->setLoader(new StringLoader);
		$this->template->setFile($this->emailService->renderLoginEmail($token));
	}

}
