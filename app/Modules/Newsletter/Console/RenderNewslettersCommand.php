<?php declare(strict_types=1);

namespace App\Modules\Newsletter\Console;

use App\Modules\Core\Model\UserModel;
use App\Modules\Newsletter\Model\NewsletterService;
use App\Modules\Newsletter\Model\NoEventsFoundException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class RenderNewslettersCommand extends Command
{
    /**
     * @var NewsletterService
     */
    private $newsletterService;

    /**
     * @var UserModel
     */
    private $userModel;

    public function __construct(NewsletterService $newsletterService, UserModel $userModel)
    {
        parent::__construct();
        $this->newsletterService = $newsletterService;
        $this->userModel = $userModel;
    }

    protected function configure(): void
    {
        $this->setName('newsletters:render')
            ->setDescription('Render users newsletters prepared to send');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $users = $this->userModel->getAll()->where('newsletter', true)->fetchAll();
        $createdCount = 0;
        foreach ($users as $user) {
            try {
                $this->newsletterService->createUserNewsletter($user->getPrimary());
                ++$createdCount;
            } catch (NoEventsFoundException $e) {
                $output->writeln($e->getMessage());
            }
        }

        $output->writeln($createdCount . ' newsletters have been created');

        return 0;
    }
}
