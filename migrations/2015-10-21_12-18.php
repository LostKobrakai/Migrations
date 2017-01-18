<?php

class Migration_2015_10_21_12_18 extends AccessMigration {

	public static $description = "Update the access settings for the basic-page template";

	protected function getAccessChanges ()
	{
		return [
			// add useRoles to basic-page template
			'+basic-page' => [
				// Allow the editor role to create pages of that template
				// and disallow it to add pages as children
				'editor' => ['+create', '-add']
			]
		];
	}
}