<?php declare(strict_types=1);

namespace App\Modules\Email\Model;

use App\Modules\Core\Utils\Filters;
use App\Modules\Email\Model\Entity\BasicEmail;
use App\Modules\Email\Presenters\EmailPresenter;
use Kdyby\Translation\Translator;
use Nette\Application\LinkGenerator;
use Nette\Application\UI\ITemplateFactory;
use Nette\Bridges\ApplicationLatte\Template;
use Nette\Http\Url;
use SendGrid;
use SendGrid\Content;
use SendGrid\Email;
use SendGrid\Mail;
use Throwable;

final class EmailService
{
    /**
     * @var Translator
     */
    private $translator;

    /**
     * @var LinkGenerator
     */
    private $linkGenerator;

    /**
     * @var ITemplateFactory
     */
    private $templateFactory;

    /**
     * @var SendGrid
     */
    private $sendGrid;

    public function __construct(
        SendGrid $sendGrid,
        ITemplateFactory $templateFactory,
        LinkGenerator $linkGenerator,
        Translator $translator
    ) {
        $this->sendGrid = $sendGrid;
        $this->templateFactory = $templateFactory;
        $this->linkGenerator = $linkGenerator;
        $this->translator = $translator;
    }

    public function sendLogin(string $emailTo, string $token): void
    {
        $to = new Email(null, $emailTo);
        $from = new Email('Eventigo.cz', 'prihlaseni@eventigo.cz');
        $subject = $this->translator->translate('email.login.subject');
        $content = new Content('text/html', $this->renderLoginEmail($token));

        $mail = new Mail($from, $subject, $to, $content);
        $mail->addCategory('emailLogin');

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
