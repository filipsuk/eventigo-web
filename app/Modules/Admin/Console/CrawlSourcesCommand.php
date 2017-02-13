<?php declare(strict_types=1);

namespace App\Modules\Admin\Console;

use App\Modules\Admin\Model\SourceService;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


class CrawlSourcesCommand extends Command
{
	protected function configure()
	{
		$this->setName('admin:crawlSources')
			->setDescription('Crawl events from sources');
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		/** @var SourceService $sourceService */
		$sourceService = $this->getHelper('container')->getByType(SourceService::class);

		$addedEvents = $sourceService->crawlSources();

		if ($addedEvents) {
			$output->writeLn($addedEvents . ' new events has been added to be approved');
		} else {
			$output->writeLn('No new events has been added');
		}

		return 0;
	}
}