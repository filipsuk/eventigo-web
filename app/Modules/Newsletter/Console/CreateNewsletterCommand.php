<?php declare(strict_types=1);

namespace App\Modules\Newsletter\Console;

use App\Modules\Newsletter\Model\NewsletterService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


class CreateNewsletterCommand extends Command
{
	protected function configure()
	{
		$this->setName('newsletters:create')
			->setDescription('Creates default newsletter');
	}

	protected function execute(InputInterface $input, OutputInterface $output): int
	{
		/** @var NewsletterService $newsletterService */
		$newsletterService = $this->getHelper('container')->getByType(NewsletterService::class);

		$result = $newsletterService->createDefaultNewsletter();
		$output->writeln('Result: ' . $result);
		return 0;
	}
}
