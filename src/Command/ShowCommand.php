<?php
/**
 * Created by PhpStorm.
 * User: benni
 * Date: 26.12.16
 * Time: 14:03
 */

namespace ProcessWire\Migrations\Command;


use ProcessWire\Migrations\Colors\CliStyles;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ShowCommand extends Command
{
	use MigrationsModule;
	use MigrationsTable;

	protected function configure ()
	{
		$this
			->setName('show')
			->setDescription('Show')
			->setHelp("Show the list of migrations")
			->addOption(
				'migrated',
				'm',
				InputOption::VALUE_NONE,
				'Only migrated',
				null
			)
			->addOption(
				'unmigrated',
				'u',
				InputOption::VALUE_NONE,
				'Only not migrated',
				null
			)
		;
	}

	protected function execute (InputInterface $input, OutputInterface $output)
	{
		$io = new CliStyles($input, $output);
		$files = $this->migrations->getMigrations();

		if($input->hasParameterOption(['-m', '--migrated'])) $files->filter('migrated=1');
		if($input->hasParameterOption(['-u', '--unmigrated'])) $files->filter('migrated=0');

		$io->art();
		$io->writeln(sprintf(
			'Found %1$d migrations – %2$d already migrated.',
			$files->count,
			$files->find("migrated=1")->count
		));
		$this->renderTable($io, $files);
	}
}