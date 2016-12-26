<?php
/**
 * Created by PhpStorm.
 * User: benni
 * Date: 26.12.16
 * Time: 13:28
 */

namespace ProcessWire\Migrations\Command;


use Migrationfile;
use MigrationfilesArray;
use Symfony\Component\Console\Style\StyleInterface;

trait MigrationsTable
{
	/**
	 * @param StyleInterface      $io
	 * @param MigrationfilesArray $migrations
	 */
	protected function renderTable (StyleInterface $io, MigrationfilesArray $migrations)
	{
		$io->table(
			array('T', 'Migration', 'Description', 'S'),
			array_map([$this, 'transformArray'], $migrations->getArray())
		);
	}
	/**
	 * @param Migrationfile $file
	 * @return array
	 */
	protected function transformArray (Migrationfile $file)
	{
		$root = $this->migrations->wire('config')->paths->root;

		$rootRelOrAbsolute = str_replace($root, '', $file);
		$folders = str_replace(basename($rootRelOrAbsolute), '', $rootRelOrAbsolute);

		return [
			substr($file->type, 0, 1),
			sprintf('<muted>%s</muted>%s', $folders, $file->filename),
			$this->truncatedDescription($file),
			$file->migrated ? '<success>✔</success>' : '<muted>-</muted>',
		];
	}

	/**
	 * @param Migrationfile $file
	 * @return mixed
	 */
	protected function truncatedDescription (Migrationfile $file)
	{
		$formatter = $this->getHelper('formatter');
		$desc = str_replace(array("\n", "\r\n", "\r"), "¶ ", $file->description);
		$desc = $formatter->truncate($desc, 49, '…');
		return str_replace("¶", "<muted>¶</muted>", $desc);
	}
}