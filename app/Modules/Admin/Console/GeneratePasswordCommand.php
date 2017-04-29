<?php declare(strict_types=1);

namespace App\Modules\Admin\Console;

use App\Modules\Core\Model\UserModel;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


final class GeneratePasswordCommand extends Command
{
	protected function configure(): void
	{
		$this->setName('admin:generatePassword')
			->setDescription('Generate admin password')
			->addArgument(
				'password',
				InputArgument::REQUIRED,
				'Raw password'
			);
	}

	protected function execute(InputInterface $input, OutputInterface $output): int
	{
		$password = (string) $input->getArgument('password');

		/** @var UserModel $userModel */
		$userModel = $this->getHelper('container')->getByType(UserModel::class);

		$hash = $userModel->hashAndEncrypt($password);

		$output->writeln($hash);
		return 0;
	}
}
