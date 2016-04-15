<?php

class Migration_2015_10_21_12_14 extends Migration {

	public static $description = "Test Migration\nDoesn't change anything\nIt would solve all problems";

	public function update() {
		$this->message("Ran the update() function!");
	}

	public function downgrade() {
		$this->message("Ran the downgrade() function!");
	}

}