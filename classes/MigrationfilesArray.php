<?php

class MigrationfilesArray extends WireArray {
	/**
	 * @return Migrationfile
	 */
	public function makeBlankItem()
	{
		return new Migrationfile;
	}

	/**
	 * @param $item
	 * @return string
	 */
	public function getItemKey($item)
	{
		return $item->className;
	}

	/**
	 * @param $item
	 * @return bool
	 */
	public function isValidItem($item)
	{
		return $item instanceof Migrationfile;
	}
}