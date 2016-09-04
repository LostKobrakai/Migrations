<?php
/**
 * Created by PhpStorm.
 * User: benni
 * Date: 04.09.16
 * Time: 14:19
 */

namespace ProcessWire\Migrations\Command;


use ProcessWire\Migrations\ConsoleEnhancements\MigrationsStyle;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class RollbackCommand extends Command
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
			->setName('rollback')
			->setDescription('Rollback migrations.')
			->setHelp("Rollback migrations, by default only the latest one.")
			->addOption(
				'number',
				null,
				InputOption::VALUE_REQUIRED,
				'How many times should the message be printed?',
				1
			);
		;
	}

	protected function execute (InputInterface $input, OutputInterface $output)
	{
		$io = new MigrationsStyle($input, $output);
		$number = (int) $input->getOption('number');
		$toRun = $this->migrations->getMigrations();

		if(!$count = count($toRun)){
			$io->note('Nothing to migrate');
			return;
		}

		$io->successNotice("Found $count new migrations. Starting...");

		$rollback = array();

		foreach (array_reverse($toRun) as $key => $file) {

			if(!$this->migrations->isMigrated($file)) continue;
			$rollback[] = $file;
			if(count($rollback) == $number) break;
		}

		if(!$output->isQuiet()){
			$this->renderTable($io, $rollback);
		}

		foreach ($rollback as $file) {
			if(!$this->migrations->isMigrated($file)) continue;
			$this->migrations->rollback($file);

			if(!$output->isQuiet()) {
				$output->write("\x0D");
				$output->write("\x1B[2K");
				$output->write(str_repeat("\x1B[1A\x1B[2K", count($toRun) + 5));
				$this->renderTable($io, $rollback);
			}
		}

		$io->successNotice(sprintf('Successfully rolled back %d migrations.', count($rollback)));
	}

}