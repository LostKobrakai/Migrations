<?php 
namespace tests;

describe('Migrations Module', function(){

	before(function(){
		// Install the module
		$this->modules->resetCache();
		if($this->modules->isInstallable('Migrations', true)) {
			$this->modules->install('Migrations');
		}

		$this->migrations = $this->modules->get('Migrations');
	});

	afterEach(function(){
		$classes = $this->migrations->getRunMigrations();

		foreach (array_reverse($classes) as $class) {
			$this->migrations->rollbackClass($class);
		}
	});

	it("should correctly execute template migrations", function(){
		$this->migrations->migrateClass('Migration_2016_07_20_12_10');

		$t = $this->templates->get('testTemplate');

		expect($t)->toBeAnInstanceOf("\ProcessWire\Template");
		expect($t->label)->toBe('Test Template');
	});

	it("should correctly rollback template migrations", function(){
		$t = $this->templates->get('testTemplate');
		expect($t)->toBe(null);
	});
});