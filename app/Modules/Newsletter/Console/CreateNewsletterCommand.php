<?php declare(strict_types=1);

namespace App\Modules\Newsletter\Console;

use App\Modules\Newsletter\Model\NewsletterService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class CreateNewsletterCommand extends Command
{
	/**
	 * @var NewsletterService
	 */
	private $newsletterService;

	public function __construct(NewsletterService $newsletterService)
	{
		parent::__construct();
		$this->newsletterService = $newsletterService;
	}

	protected function configure(): void
	{
		$this->setName('newsletters:create')
			->setDescription('Creates default newsletter');
	}

	protected function execute(InputInterface $input, OutputInterface $output): int
	{
		$result = $this->newsletterService->createDefaultNewsletter();
		if ($result) {
			$output->writeln('<info>New newsletter id: ' . $result . '</info>');

			return 0;
		}
			$output->writeln('<error>Could not create newsletter </error>');

			return 1;
	}
}
