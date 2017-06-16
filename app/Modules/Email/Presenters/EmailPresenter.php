<?php declare(strict_types=1);

namespace App\Modules\Email\Presenters;

use App\Modules\Core\Presenters\AbstractBasePresenter;
use App\Modules\Email\Model\EmailService;
use Latte\Loaders\StringLoader;

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
        $this->template->getLatte()->setLoader(new StringLoader); // @todo: what is this for?
        $this->template->setFile($this->emailService->renderLoginEmail($token));
    }
}
