<?php
/**
 * Created by PhpStorm.
 * User: benni
 * Date: 04.09.16
 * Time: 08:27
 */

namespace ProcessWire\Migrations\Command;
use Migrations;

/**
 * Class MigrationsDependent
 *
 * @package ProcessWire\Migrations\Command
 * @property Migrations|null $migrations
 */
trait MigrationsDependent
{
	/**
	 * @var Migrations|null
	 */
	protected $migrations;

	public function setMigrations ($migrations)
	{
		$this->migrations = $migrations;
	}

	public function isEnabled ()
	{
		if(!$this->migrations instanceof Migrations && !$this->migrations instanceof \ProcessWire\Migrations){
			return false;
		}

		return true;
	}

}