<?php
/**
 * Created by PhpStorm.
 * User: benni
 * Date: 04.09.16
 * Time: 15:34
 */

namespace ProcessWire\Migrations\Command;


use ProcessWire\Migrations\ConsoleEnhancements\MigrationsStyle;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CreateModuleCommand extends CreateCommand
{
	const TEMPLATE_TYPE = 'module';
	use MigrationsDependent;

	public function __construct ($name = null, $migrations)
	{
		parent::__construct($name);
		$this->setMigrations($migrations);
	}

	/**
	 * @param $configuration
	 */
	protected function configureArgsAndOptions ($configuration)
	{
		$configuration
			->addArgument(
				'moduleName',
				InputOption::VALUE_REQUIRED,
				'The name of the template to be constructed.'
			)
			->addArgument(
				'description',
				InputOption::VALUE_REQUIRED,
				'Alternative description for migration.'
			)
		;
	}

	/**
	 * @param InputInterface $input
	 * @return array
	 */
	protected function parseArgumentsToOptions (InputInterface $input)
	{
		$name = $input->getArgument('moduleName');
		$desc = $input->getArgument('description');
		if (strlen($name) && !strlen($desc)) $desc = "Create template $name";

		$options = [
			'moduleName'   => $name,
			'description' => $desc
		];

		return $options;
	}

}