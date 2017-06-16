<?php declare(strict_types=1);

namespace App\Modules\Newsletter\Presenters;

use App\Modules\Core\Model\UserModel;
use App\Modules\Core\Presenters\AbstractBasePresenter;
use App\Modules\Newsletter\Model\NewsletterService;
use App\Modules\Newsletter\Model\UserNewsletterModel;
use Nette\Database\Table\ActiveRow;
use Nette\Http\IResponse;

final class NewsletterPresenter extends AbstractBasePresenter
{
    /**
     * @var UserNewsletterModel
     */
    private $userNewsletterModel;

    /**
     * @var UserModel
     */
    private $userModel;

    /**
     * @var NewsletterService
     */
    private $newsletterService;

    /**
     * @var ActiveRow
     */
    private $userNewsletter;

    public function __construct(
        NewsletterService $newsletterService,
        UserModel $userModel,
        UserNewsletterModel $userNewsletterModel
    ) {
        $this->newsletterService = $newsletterService;
        $this->userModel = $userModel;
        $this->userNewsletterModel = $userNewsletterModel;
    }

    public function actionDefault(string $hash): void
    {
        $this->userNewsletter = $this->userNewsletterModel->getAll()->where([
            'hash' => $hash,
        ])->fetch();

        if (! $this->userNewsletter) {
            $this->error(null, IResponse::S404_NOT_FOUND);
        }
    }

    public function renderDefault(): void
    {
        $newsletter = $this->userNewsletter;
        $this->template->userNewsletter = NewsletterService::inlineCss($newsletter->toArray());
    }

    public function actionUnsubscribe(string $hash): void
    {
        $userNewsletter = $this->userNewsletterModel->getAll()->where(['hash' => $hash])->fetch();
        if ($userNewsletter) {
            $this->userModel->getAll()->wherePrimary($userNewsletter->user_id)->update([
                'newsletter' => false,
            ]);

            $this->template->email = $userNewsletter->user->email;
        }
    }

    public function actionDynamic(): void
    {
        // Allow newsletter preview only for admins
        if (! $this->getUser()->isLoggedIn() || ! $this->getUser()->isInRole('admin')) {
            $this->redirect(':Admin:Sign:in');
        }
    }

    public function renderDynamic(int $userId): void
    {
        $newsletter = $this->newsletterService->buildArrayForTemplate((int) $userId);
        $this->template->newsletter = NewsletterService::inlineCss($newsletter);
    }
}
