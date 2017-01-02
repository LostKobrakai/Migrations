<?php

abstract class ModuleMigration extends Migration{

	public static $description;

	abstract protected function getModuleName();
	
	protected function moduleConfig(array $config){
		return $config;
	}

	public function update() {
		$this->modules->resetCache();
		$name = $this->getModuleName();
		if(!$this->modules->isInstallable($name, true)) return;
		$this->modules->install($name);
		$config = $this->modules->getModuleConfigData($name); 
		$config = $this->moduleConfig($config);
		$this->modules->saveModuleConfigData($name, $config);
	}

	public function downgrade() {
		$this->modules->resetCache();
		$name = $this->getModuleName();
		if(!$this->modules->isUninstallable($name)) return;
		$this->modules->uninstall($name);
	}

}
