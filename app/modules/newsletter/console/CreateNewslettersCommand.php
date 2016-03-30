<?php

namespace App\Modules\Newsletter\Console;

use App\Modules\Core\Model\UserModel;
use App\Modules\Newsletter\Model\NewsletterService;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


class CreateNewslettersCommand extends Command
{
	protected function configure()
	{
		$this->setName('newsletters:create')
			->setDescription('Create newsletters')
			->addArgument(
				'newsletterId',
				InputArgument::REQUIRED,
				'newsletter template id'
			);
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$newsletterId = (int)$input->getArgument('newsletterId');

		/** @var NewsletterService $newsletterService */
		$newsletterService = $this->getHelper('container')->getByType(NewsletterService::class);
		/** @var UserModel $userModel */
		$userModel = $this->getHelper('container')->getByType(UserModel::class);

		$users = $userModel->getAll()->fetchAll();
		foreach($users as $user) {
			$newsletterService->createNewsletter($newsletterId, $user->id);
		}

		$output->writeLn('Newsletters have been created');
		return 0;
	}
}