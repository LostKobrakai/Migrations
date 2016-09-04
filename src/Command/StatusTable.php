<?php
/**
 * Created by PhpStorm.
 * User: benni
 * Date: 04.09.16
 * Time: 14:41
 */

namespace ProcessWire\Migrations\Command;


use Symfony\Component\Console\Style\StyleInterface;

trait StatusTable
{

	/**
	 * @param StyleInterface $io
	 * @param array          $migrations
	 */
	protected function renderTable (StyleInterface $io, array $migrations)
	{

		$io->table(array('T', 'Migration', 'Description', 'S'), array_map([$this, 'transformArray'], $migrations));
	}

	/**
	 * @param $file
	 * @return array
	 */
	protected function transformArray ($file)
	{
		$formatter = $this->getHelper('formatter');
		$root = $this->migrations->wire('config')->paths->root;

		$rootRelOrAbsolute = str_replace($root, '', $file);
		$folders = str_replace(basename($rootRelOrAbsolute), '', $rootRelOrAbsolute);
		$filename = basename($rootRelOrAbsolute, '.php');

		$staticVars = $this->migrations->getStatics($file);
		$desc = str_replace(array("\n", "\r\n", "\r"), "¶ ", $staticVars["description"]);
		$desc = $formatter->truncate($desc, 49, '…');
		$desc = str_replace("¶", "<muted>¶</muted>", $desc);
		$type = $this->migrations->getType($file);
		$type = substr($type, 0, 1);

		return [
			$type,
			sprintf('<muted>%s</muted>%s', $folders, $filename),
			$desc,
			$this->migrations->isMigrated($file)
				? '<success>✔</success>'
				: '<muted>-</muted>',
		];
	}
}