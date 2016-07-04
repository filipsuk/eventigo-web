<?php

namespace App\Modules\Email\Model;

use App\Modules\Core\Utils\Filters;
use App\Modules\Core\Utils\Helper;
use App\Modules\Email\Model\Entity\BasicEmail;
use App\Modules\Email\Presenters\EmailPresenter;
use Kdyby\Translation\Translator;
use Nette\Application\LinkGenerator;
use Nette\Application\UI\ITemplateFactory;
use Nette\Bridges\ApplicationLatte\Template;
use Nette\Http\Url;
use SendGrid;
use SendGrid\Email;


class EmailService
{
	/** @var Translator @inject */
	public $translator;

	/** @var LinkGenerator @inject */
	public $linkGenerator;

	/** @var ITemplateFactory @inject */
	public $templateFactory;

	/** @var string */
	private $apiKey;


	public function setApiKey(string $apiKey) : self
	{
		$this->apiKey = $apiKey;
		return $this;
	}


	public function sendLogin(string $emailTo, string $token)
	{
		$sendGrid = new SendGrid($this->apiKey);
		$email = new Email;

		$content = $this->renderLoginEmail($token);

		$email->addTo($emailTo)
			->setFrom('prihlaseni@eventigo.cz')
			->setFromName('Eventigo.cz')
			->setSubject($this->translator->translate('email.login.subject'))
			->setCategory('emailLogin')
			->setHtml($content);

		try {
			$sendGrid->send($email);

		} catch (SendGrid\Exception $e) {
			// TODO log unsuccessful email send
		}
	}


	public function renderLoginEmail(string $token) : string
	{
		$email = new BasicEmail;
		$email->setIntroText($this->translator->translate('email.login.text'));
		$email->setButtonText($this->translator->translate('email.login.loginButton'));
		$email->setButtonUrl(new Url($this->linkGenerator->link('Front:Homepage:default', ['token' => $token])));
		$email->setFooterText($this->translator->translate('email.login.footerText'));

		/** @var Template $template */
		$template = $this->templateFactory->createTemplate();
		Filters::setTranslator($this->translator);
		$template->addFilter(null, [Filters::class, 'loader']);
		return $template->getLatte()->renderToString(EmailPresenter::BASIC_EMAIL_TEMPLATE_FILE, [
			'email' => $email,
		]);
	}
}
