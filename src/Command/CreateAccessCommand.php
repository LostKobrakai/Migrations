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

class CreateAccessCommand extends CreateCommand
{
	const TEMPLATE_TYPE = 'access';

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
		;
	}

	/**
	 * @param InputInterface $input
	 * @return array
	 */
	protected function parseArgumentsToOptions (InputInterface $input)
	{
		$desc = $input->getArgument('description');
		if (!strlen($desc)) $desc = "Update template access rules";

		$options = [
			'description' => $desc
		];

		return $options;
	}
}