<?php
/**
 * Created by PhpStorm.
 * User: benni
 * Date: 04.09.16
 * Time: 16:07
 */

namespace ProcessWire\Migrations\Command;


use ProcessWire\Migrations\ConsoleEnhancements\MigrationsStyle;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CreateCommand extends Command
{
	const TEMPLATE_TYPE = 'default';
	use MigrationsDependent;

	public function __construct ($name = null, $migrations)
	{
		parent::__construct($name);
		$this->setMigrations($migrations);
	}

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
			);
	}

	protected function execute (InputInterface $input, OutputInterface $output)
	{
		$io = new MigrationsStyle($input, $output);

		$options = $this->parseArgumentsToOptions($input);

		try{
			$file = $this->migrations->createNew(static::TEMPLATE_TYPE, $options);
			$io->successNotice("Created new migration " . basename($file));
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
}