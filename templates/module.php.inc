<?php

class {classname} extends ModuleMigration {

	public static $description = "{description}";

	protected function getModuleName(){ /* return 'Migrations'; */ }

	/**
	 * optional
	 */
	protected function moduleConfig(array $config){
		$config['setting'] = true;
		return $config;
	}

}