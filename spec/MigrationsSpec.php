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

	describe('Basic Functionality', function(){

		it('should find migrations in the filesystem', function(){
			$migrationFiles = $this->migrations->getMigrations();
			$keys = array_keys($migrationFiles);

			expect(count($migrationFiles))->toBeGreaterThan(1);
			expect(reset($keys))->toBe('Migration_2016_07_20_12_10');
			expect(reset($migrationFiles))->toContain('site/migrations/2016-07-20_12-10.php');
		});

		it('should correctly convert classnames to filenames and back again', function(){

			$filename = $this->migrations->classnameToFilename('Migration_2016_07_20_12_10');
			$classname = $this->migrations->filenameToClassname($filename);

			expect($filename)->toBe('2016-07-20_12-10.php');
			expect($classname)->toBe('Migration_2016_07_20_12_10');
		});

		it('should also convert complete paths to a valid classname', function(){
			$classname = $this->migrations->filenameToClassname('/var/www/html/site/migrations/2016-07-20_12-10.php');

			expect($classname)->toBe('Migration_2016_07_20_12_10');
		});

		it('should be able to read a migration\'s descriptions', function(){
			$filename = $this->migrations->classnameToFilename('Migration_2016_07_20_12_10');
			$staticVars = $this->migrations->getStatics($filename);

			expect($staticVars["description"])->toBe('Add testTemplate');
		});

		it('should be able to read a migration\'s type', function(){
			$filename = $this->migrations->classnameToFilename('Migration_2016_07_20_12_10');
			$type = $this->migrations->getType($filename);

			expect($type)->toBe('TemplateMigration');
		});

	});

	describe('Migrations', function(){

		afterEach(function(){
			$classes = $this->migrations->getRunMigrations();

			foreach (array_reverse($classes) as $class) {
				$this->migrations->rollbackClass($class);
			}
		});

		it("should correctly execute template migrations", function(){
			$this->migrations->migrateClass('Migration_2016_07_20_12_10');

			$t = $this->templates->get('testTemplate');

			expect($t)->toBeAnInstanceOf('\ProcessWire\Template');
			expect($t->label)->toBe('Test Template');
		});

		it("should correctly rollback template migrations", function(){
			$t = $this->templates->get('testTemplate');
			expect($t)->toBe(null);
		});

		it("should correctly return ran migrations", function(){
			$filename = $this->migrations->classnameToFilename('Migration_2016_07_20_12_10');

			expect($this->migrations->getRunMigrations())->toBe([]);
			expect($this->migrations->isMigrated($filename))->toBe(false);

			$this->migrations->migrateClass('Migration_2016_07_20_12_10');

			expect($this->migrations->isMigrated($filename))->toBe(true);
			expect($this->migrations->getRunMigrations())->toBe(['Migration_2016_07_20_12_10']);
		});

	});
});