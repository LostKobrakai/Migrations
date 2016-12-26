<?php

namespace ProcessWire\Migrations\Command;

use Migrations;

trait MigrationsModule
{
	/**
	 * @var Migrations|null
	 */
	protected $migrations;

	/**
	 * @param $migrations
	 */
	public function setMigrations ($migrations)
	{
		$this->migrations = $migrations;
	}

	/**
	 * @return bool
	 */
	public function isEnabled ()
	{
		if($this->migrations instanceof \Migrations || $this->migrations instanceof \ProcessWire\Migrations){
			return true;
		}
		return false;
	}
}