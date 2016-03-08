<?php

namespace App\Modules\Newsletter\Console;

use App\Modules\Core\Model\UserNewsletterModel;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


class SendNewslettersCommand extends Command
{
	protected function configure()
	{
		$this->setName('newsletters:send')
			->setDescription('Send newsletters');
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		/** @var UserNewsletterModel $userNewsletterModel */
		$userNewsletterModel = $this->getHelper('container')->getByType(UserNewsletterModel::class);

		$usersNewslettersHashes = $userNewsletterModel->getAll()
			->select('hash')
			->where('sent IS NULL')
			->fetchPairs(NULL, 'hash');

		$userNewsletterModel->sendNewsletters($usersNewslettersHashes);

		$output->writeLn('Newsletters have been sent');
		return 0;
	}
}