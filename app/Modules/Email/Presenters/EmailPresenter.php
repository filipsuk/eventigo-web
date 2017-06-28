<?php declare(strict_types=1);

namespace App\Modules\Email\Presenters;

use App\Modules\Core\Presenters\AbstractBasePresenter;
use App\Modules\Email\Model\EmailService;
use Latte\Loaders\StringLoader;
use Nette\Application\UI\ITemplate;
use Nette\Bridges\ApplicationLatte\Template;

/**
 * EmailPresenter is used for rendering different types of emails.
 */
final class EmailPresenter extends AbstractBasePresenter
{
    /**
     * @var string
     */
    public const BASIC_EMAIL_TEMPLATE_FILE = self::TEMPLATE_DIR . DIRECTORY_SEPARATOR . 'basic.latte';

    /**
     * @var string
     */
    private const TEMPLATE_DIR = __DIR__ . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . 'Email';

    /**
     * @var EmailService
     */
    private $emailService;

    public function __construct(EmailService $emailService)
    {
        $this->emailService = $emailService;
    }

    /**
     * Renders login email with provided user token.
     *
     * @throws \InvalidArgumentException
     * @throws \Nette\Application\UI\InvalidLinkException
     * @throws \Nette\InvalidArgumentException
     */
    public function renderLogin(string $token): void
    {
        /** @var ITemplate|Template $template */
        $template = $this->getTemplate();
        $template->getLatte()->setLoader(new StringLoader); // @todo: what is this for?
        $template->setFile($this->emailService->renderLoginEmail($token));
    }
}
