<?php declare(strict_types=1);

namespace App\Modules\Newsletter\Console;

use App\Modules\Core\Model\UserModel;
use App\Modules\Newsletter\Model\NewsletterService;
use App\Modules\Newsletter\Model\NoEventsFoundException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


class RenderNewslettersCommand extends Command
{
	protected function configure()
	{
		$this->setName('newsletters:render')
			->setDescription('Render users\' newsletters prepared to send');
	}

	protected function execute(InputInterface $input, OutputInterface $output): int
	{
		/** @var NewsletterService $newsletterService */
		$newsletterService = $this->getHelper('container')->getByType(NewsletterService::class);
		/** @var UserModel $userModel */
		$userModel = $this->getHelper('container')->getByType(UserModel::class);

		$users = $userModel->getAll()->where('newsletter', true)->fetchAll();
		$createdCount = 0;
		foreach($users as $user) {
			try {
				$newsletterService->createUserNewsletter($user->id);
				$createdCount++;
			} catch (NoEventsFoundException $e) {
				$output->writeln($e->getMessage());
			}
		}

		$output->writeln($createdCount . ' newsletters have been created');
		return 0;
	}
}
