<?php
namespace ProcessWire\Migrations\Command;

use ProcessWire\Migrations\ConsoleEnhancements\MigrationsStyle;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;


class MigrateListCommand extends Command
{
	use MigrationsDependent;
	use StatusTable;

	public function __construct ($name = null, $migrations)
	{
		parent::__construct($name);
		$this->setMigrations($migrations);
	}

	protected function configure ()
	{
		$this
			->setName('migrate:list')
			->setDescription('Show migrations')
			->setHelp("Show all migrations.")
		;
	}

	protected function execute (InputInterface $input, OutputInterface $output)
	{
		$io = new MigrationsStyle($input, $output);
		$toRun = $this->migrations->getMigrations();
		$migrated = $this->migrations->getRunMigrations();

		$io->title('Migrations CLI');

		$io->notice(sprintf('Found %d migrations, %d of them are already migrated.', count($toRun), count($migrated)));

		if(!$output->isQuiet()){
			$this->renderTable($io, $toRun);
		}
	}

}