<?php
namespace ProcessWire\Migrations;


use ProcessWire\Migrations\Command\CreateAccessCommand;
use ProcessWire\Migrations\Command\CreateCommand;
use ProcessWire\Migrations\Command\CreateCustomCommand;
use ProcessWire\Migrations\Command\CreateFieldCommand;
use ProcessWire\Migrations\Command\CreateModuleCommand;
use ProcessWire\Migrations\Command\CreateTemplateCommand;
use ProcessWire\Migrations\Command\InstallCommand;
use ProcessWire\Migrations\Command\MigrateCommand;
use ProcessWire\Migrations\Command\RollbackCommand;
use ProcessWire\Migrations\Command\ShowCommand;
use Symfony\Component\Console\Application;

class CLI
{
	public static function run ($wire)
	{
		$application = new Application('Migrations CLI', '0.3.0');

		$migrations = $wire->modules->get('Migrations');

		$commandClasses = [
			CreateCommand::class,
			CreateFieldCommand::class,
			CreateModuleCommand::class,
			CreateTemplateCommand::class,
			CreateAccessCommand::class,
			MigrateCommand::class,
			RollbackCommand::class,
			CreateCustomCommand::class,
			ShowCommand::class,
		];

		foreach ($commandClasses as $classname) {
			$command = new $classname();
			$command->setMigrations($migrations);
			$application->add($command);
		}

		$installCommand = new InstallCommand();
		$installCommand->setWire($wire);
		$application->add($installCommand);

		return $application->run();
	}
}