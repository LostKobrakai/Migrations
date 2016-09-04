<?php
namespace ProcessWire\Migrations;
use ProcessWire\Migrations\Command\CreateCommand;
use ProcessWire\Migrations\Command\CreateFieldCommand;
use ProcessWire\Migrations\Command\CreateModuleCommand;
use ProcessWire\Migrations\Command\CreateTemplateCommand;
use ProcessWire\Migrations\Command\MigrateLatestCommand;
use ProcessWire\Migrations\Command\MigrateListCommand;
use ProcessWire\Migrations\Command\RollbackCommand;
use ProcessWire\Migrations\ConsoleEnhancements\EnhancedOutputFormattedStyle;
use ProcessWire\Migrations\ConsoleEnhancements\OutputFormatter;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Output\ConsoleOutput;

class CLI
{
	private function __construct ($wire)
	{
		$application = new Application('Migrations CLI', '0.0.2');

		$application->add(new MigrateListCommand(null, $wire->modules->get('Migrations')));
		$application->add(new MigrateLatestCommand(null, $wire->modules->get('Migrations')));
		$application->add(new RollbackCommand(null, $wire->modules->get('Migrations')));
		$application->add(new CreateCommand(null, $wire->modules->get('Migrations')));
		$application->add(new CreateFieldCommand(null, $wire->modules->get('Migrations')));
		$application->add(new CreateTemplateCommand(null, $wire->modules->get('Migrations')));
		$application->add(new CreateModuleCommand(null, $wire->modules->get('Migrations')));

		$formatter = new OutputFormatter();
		$formatter->setStyle('muted', new EnhancedOutputFormattedStyle(null, null, array('dim')));
		$formatter->setStyle('success', new EnhancedOutputFormattedStyle('green'));
		$formatter->setStyle('failure', new EnhancedOutputFormattedStyle('red'));
		$formatter->setStyle('headline', new EnhancedOutputFormattedStyle('light_blue'));
		$output = new ConsoleOutput(null, null, $formatter);
		$application->run(null, $output);
	}

	public static function run ($wire)
	{
		return new static($wire);
	}
}