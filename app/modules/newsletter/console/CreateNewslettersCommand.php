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
				'from',
				InputArgument::OPTIONAL,
				'Email from'
			);
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$from = $input->getArgument('from') ?: 'filip';

		/** @var NewsletterService $newsletterService */
		$newsletterService = $this->getHelper('container')->getByType(NewsletterService::class);
		/** @var UserModel $userModel */
		$userModel = $this->getHelper('container')->getByType(UserModel::class);

		$users = $userModel->getAll()->where('newsletter', true)->fetchAll();
		foreach($users as $user) {
			$newsletterService->createNewsletter($user->id, $from . '@eventigo.cz', '', '');
		}

		$output->writeLn(count($users) . ' newsletters have been created');
		return 0;
	}
}