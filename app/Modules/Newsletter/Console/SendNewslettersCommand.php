<?php declare(strict_types=1);

namespace App\Modules\Newsletter\Console;

use App\Modules\Newsletter\Model\NewsletterService;
use App\Modules\Newsletter\Model\UserNewsletterModel;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


final class SendNewslettersCommand extends Command
{
	private $newsletterService;
	private $userNewsletterModel;

	public function __construct(NewsletterService $newsletterService, UserNewsletterModel $userNewsletterModel)
	{
		parent::__construct();
		$this->newsletterService = $newsletterService;
		$this->userNewsletterModel = $userNewsletterModel;
	}

	protected function configure()
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
