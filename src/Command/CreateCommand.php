<?php
/**
 * Created by PhpStorm.
 * User: benni
 * Date: 25.12.16
 * Time: 17:19
 */

namespace ProcessWire\Migrations\Command;


use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class CreateCommand extends Command
{
	use MigrationsModule;

	const TEMPLATE_TYPE = 'default';

	protected function configure ()
	{
		$configuration = $this
			->setName('create' . (static::TEMPLATE_TYPE === 'default' ? '' : ':' . static::TEMPLATE_TYPE))
			->setDescription(sprintf('Create new %s migrations', static::TEMPLATE_TYPE))
			->setHelp(sprintf('Create new %s migrations.', static::TEMPLATE_TYPE))
		;
		$this->configureArgsAndOptions($configuration);
	}
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
				'Alternative template type',
				'default'
			)
		;
	}

	/**
	 * @param InputInterface  $input
	 * @param OutputInterface $output
	 * @return void
	 */
	protected function execute (InputInterface $input, OutputInterface $output)
	{
		if(!$this->isEnabled())
			throw new RuntimeException('Migrations Module not installed.');

		$io = new SymfonyStyle($input, $output);
		$options = $this->parseArgumentsToOptions($input);

		try{
			$file = $this->migrations->createNew($this->getTemplateType($input), $options);
			$io->success("Created new migration " . basename($file));
		} catch (\WireException $e){
			$io->error($e->getMessage());
		} catch (\ProcessWire\WireException $e){
			$io->error($e->getMessage());
		}
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

	/**
	 * @param InputInterface $input
	 * @return string
	 */
	protected function getTemplateType (InputInterface $input)
	{
		return static::TEMPLATE_TYPE;
	}
}