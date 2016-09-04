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

class CreateFieldCommand extends CreateCommand
{
	const TEMPLATE_TYPE = 'field';
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
				'fieldName',
				InputOption::VALUE_REQUIRED,
				'The name of the field to be constructed.'
			)
			->addArgument(
				'description',
				InputOption::VALUE_REQUIRED,
				'Alternative description for migration.'
			)
			->addOption(
				'type',
				't',
				InputOption::VALUE_REQUIRED,
				'Alternative type for migration. (Fieldtype… suffix optional)',
				'FieldtypeText'
			)
		;
	}

	/**
	 * @param InputInterface $input
	 * @return array
	 */
	protected function parseArgumentsToOptions (InputInterface $input)
	{
		$name = $input->getArgument('fieldName');
		$desc = $input->getArgument('description');
		$type = $input->getOption('type');
		if (strlen($name) && !strlen($desc)) $desc = "Create field $name";
		if (strlen($type) && strpos($type, 'Fieldtype') !== 0) $type = "Fieldtype$type";

		$options = [
			'fieldName'   => $name,
			'description' => $desc,
			'fieldType'   => $type
		];

		return $options;
	}

}