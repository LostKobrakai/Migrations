<?php
/**
 * Created by PhpStorm.
 * User: benni
 * Date: 25.12.16
 * Time: 17:36
 */

namespace ProcessWire\Migrations\Command;


use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;

class CreateTemplateCommand extends CreateCommand
{
	const TEMPLATE_TYPE = 'template';

	/**
	 * @param $configuration
	 */
	protected function configureArgsAndOptions ($configuration)
	{
		$configuration
			->addArgument(
				'templateName',
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
		$name = $input->getArgument('templateName');
		$desc = $input->getArgument('description');
		if (strlen($name) && !strlen($desc)) $desc = "Create template $name";
		$options = [
			'templateName'   => $name,
			'description' => $desc
		];
		return $options;
	}
}