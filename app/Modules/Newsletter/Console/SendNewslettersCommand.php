<?php declare(strict_types=1);

namespace App\Modules\Newsletter\Console;

use App\Modules\Newsletter\Model\NewsletterService;
use App\Modules\Newsletter\Model\UserNewsletterModel;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class SendNewslettersCommand extends Command
{
    /**
     * @var NewsletterService
     */
    private $newsletterService;

    /**
     * @var UserNewsletterModel
     */
    private $userNewsletterModel;

    public function __construct(NewsletterService $newsletterService, UserNewsletterModel $userNewsletterModel)
    {
        $this->newsletterService = $newsletterService;
        $this->userNewsletterModel = $userNewsletterModel;
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName('newsletters:send')
            ->setDescription('Send newsletters');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $usersNewslettersIds = $this->userNewsletterModel->getAll()
            ->where('sent', null)
            ->fetchPairs(null, 'id');

        $this->newsletterService->sendNewsletters($usersNewslettersIds);

        $output->writeln(count($usersNewslettersIds) . ' newsletters have been sent');

        return 0;
    }
}
