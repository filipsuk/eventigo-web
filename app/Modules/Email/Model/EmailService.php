<?php declare(strict_types=1);

namespace App\Modules\Email\Model;

use App\Modules\Core\Utils\Filters;
use App\Modules\Email\Model\Entity\BasicEmail;
use App\Modules\Email\Presenters\EmailPresenter;
use App\Modules\Newsletter\Model\Exception;
use Kdyby\Translation\Translator;
use Nette\Application\LinkGenerator;
use Nette\Application\UI\ITemplateFactory;
use Nette\Bridges\ApplicationLatte\Template;
use Nette\Http\Url;
use SendGrid;
use SendGrid\Email;
use Throwable;


final class EmailService
{
	/** @var Translator @inject */
	public $translator;

	/** @var LinkGenerator @inject */
	public $linkGenerator;

	/** @var ITemplateFactory @inject */
	public $templateFactory;

	/** @var SendGrid @inject */
	public $sendGrid;


	public function sendLogin(string $emailTo, string $token)
	{
		$to = new Email(null, $emailTo);
		$from = new Email('Eventigo.cz', 'prihlaseni@eventigo.cz');
		$subject = $this->translator->translate('email.login.subject');
		$content = new SendGrid\Content('text/plain', $this->renderLoginEmail($token));

        $mail = new SendGrid\Mail($from, $subject, $to, $content);

        // ->setCategory('emailLogin') @todo, what is this for?

		try {
			$this->sendGrid->client->mail()->send()->post($mail);

		} catch (Throwable $throwable) {
			// TODO log unsuccessful email send
		}
	}


	public function renderLoginEmail(string $token): string
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
