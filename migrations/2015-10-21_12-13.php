<?php

class Migration_2015_10_21_12_13 extends Migration {

	public static $description = "Test Migration\nDoesn't change anything";

	public function update() {
		$this->message("Ran the update() function!");
	}

	public function downgrade() {
		$this->message("Ran the downgrade() function!");
	}

}