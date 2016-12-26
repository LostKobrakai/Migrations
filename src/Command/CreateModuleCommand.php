<?php
/**
 * Created by PhpStorm.
 * User: benni
 * Date: 25.12.16
 * Time: 17:34
 */

namespace ProcessWire\Migrations\Command;


use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;

class CreateModuleCommand extends CreateCommand
{
	const TEMPLATE_TYPE = 'module';

	/**
	 * @param $configuration
	 */
	protected function configureArgsAndOptions ($configuration)
	{
		$configuration
			->addArgument(
				'moduleName',
				InputOption::VALUE_REQUIRED,
				'The name of the module to be constructed.'
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
		if (strlen($name) && !strlen($desc)) $desc = "Install module $name";

		$options = [
			'moduleName'   => $name,
			'description' => $desc
		];

		return $options;
	}
}