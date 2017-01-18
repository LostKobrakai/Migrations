<?php
/**
 * Created by PhpStorm.
 * User: benni
 * Date: 25.12.16
 * Time: 17:39
 */

namespace ProcessWire\Migrations\Command;


use ProcessWire\Migrations\Colors\CliStyles;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class MigrateCommand extends Command
{
	use MigrationsModule;
	use MigrationsTable;

	protected function configure ()
	{
		$this
			->setName('run')
			->setDescription('Migrate')
			->setHelp("Run all migrations starting from the latest migrated one to the newest.")
			->addArgument('what', InputArgument::OPTIONAL, 'What to migrate?')
			->addOption(
				'latest',
				'l',
				InputOption::VALUE_NONE,
				'Migrate only files newer than the latest migrated file',
				null
			);
		;
	}

	protected function execute (InputInterface $input, OutputInterface $output)
	{
		$io = new CliStyles($input, $output);
		$io->header();

		$files = $this->migrations->selectMigrateMigrations(
			$input->getArgument('what'),
			$input->hasParameterOption(['-l', '--latest'])
		);

		if(!$files->count)
			return $io->writeln('Nothing to migrate.');

		$io->writeln(sprintf('%d files to migrate. Starting…', $files->count));

		foreach ($files as $file) {
			$this->renderTable($io, $files);

			$this->migrations->migrate($files->find("filename=$file->filename"));

			$io->clear($files->count + 5);
		}

		$this->renderTable($io, $files);
	}
}