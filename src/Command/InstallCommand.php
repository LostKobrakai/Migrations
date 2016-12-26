<?php
/**
 * Created by PhpStorm.
 * User: benni
 * Date: 26.12.16
 * Time: 12:25
 */

namespace ProcessWire\Migrations\Command;


use ProcessWire\Migrations\Colors\CliStyles;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class InstallCommand extends Command
{
	/**
	 * @var Migrations|null
	 */
	protected $wire;

	/**
	 * @param \ProcessWire\ProcessWire|\ProcessWire $wire
	 */
	public function setWire ($wire)
	{
		$this->wire = $wire;
	}

	protected function configure ()
	{
		$this
			->setName('install')
			->setDescription('Install')
			->setHelp("Install the migrations module to the supplied processwire instance")
		;
	}

	protected function execute (InputInterface $input, OutputInterface $output)
	{
		$io = new CliStyles($input, $output);
		$modules = $this->wire->wire('modules');
		$modules->refresh();

		if ($modules->isInstalled('Migrations')) {
			$io->success('Migrations module is already installed.');
		} else {
			if ($modules->install('Migrations', true)) {
				$io->success('Migrations module is now installed.');
			} else {
				$io->error('Could not install module');
				return 1;
			}
		}
	}

}