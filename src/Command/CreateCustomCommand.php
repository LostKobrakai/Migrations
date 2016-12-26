<?php
/**
 * Created by PhpStorm.
 * User: benni
 * Date: 25.12.16
 * Time: 17:33
 */

namespace ProcessWire\Migrations\Command;


use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;

class CreateCustomCommand extends CreateCommand
{
	const TEMPLATE_TYPE = 'custom';

	/**
	 * @param $configuration
	 */
	protected function configureArgsAndOptions ($configuration)
	{
		$configuration
			->addArgument(
				'description',
				InputOption::VALUE_REQUIRED,
				'Alternative description for migration.'
			)
			->addOption(
				'type',
				't',
				InputOption::VALUE_REQUIRED,
				'Alternative type of migrations',
				'default'
			)
		;
	}

	/**
	 * @param InputInterface $input
	 * @return array
	 */
	protected function parseArgumentsToOptions (InputInterface $input)
	{
		$desc = $input->getArgument('description');
		$options = [
			'description' => $desc
		];
		return $options;
	}

	protected function getTemplateType (InputInterface $input)
	{
		return $input->getOption('type');
	}

}