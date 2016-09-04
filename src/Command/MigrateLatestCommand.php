<?php
namespace ProcessWire\Migrations\Command;

use ProcessWire\Migrations\ConsoleEnhancements\MigrationsStyle;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;


class MigrateLatestCommand extends Command
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
			->setName('migrate:latest')
			->setDescription('Migrate the lastest migrations.')
			->setHelp("Run all migrations starting from the latest migrated one to the newest.")
			->setDefinition(
				new InputDefinition(array(
					new InputOption('backup', 'b'),
				))
			);
		;
	}

	protected function execute (InputInterface $input, OutputInterface $output)
	{
		$io = new MigrationsStyle($input, $output);
		$toRun = $this->migrations->getLatestToMigrate();

		if(!$count = count($toRun)){
			$io->note('Nothing to migrate');
			return;
		}

		$io->successNotice("Found $count new migrations. Starting...");

		if(!$output->isQuiet()){
			$this->renderTable($io, $toRun);
		}

		foreach ($toRun as $file) {

			if(!$this->migrations->isMigrated($file)) {
				$this->migrations->migrate($file);
			}

			if(!$output->isQuiet()) {
				$output->write("\x0D");
				$output->write("\x1B[2K");
				$output->write(str_repeat("\x1B[1A\x1B[2K", count($toRun) + 5));
				$this->renderTable($io, $toRun);
			}
		}

		$io->successNotice("Successfully migrated all new migrations.");
	}

}