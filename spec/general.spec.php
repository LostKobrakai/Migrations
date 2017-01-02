<?php

require(__DIR__ . '/../index.php');

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

describe('General settings', function() use($wire) {
	it('should be installable', function() use($wire) {
		expect($wire->wire('modules')->install('Migrations'))->not->toBe(null);
	});

	describe('Installed', function() use($wire) {
		beforeEach(function() use($wire) {
			$this->migrations = $wire->wire('modules')->get('Migrations');
			foreach($wire->wire('all')->getArray() as $api => $value){
				if(in_array($api, ['log', 'process'])) continue;
				$this->$api = $value;
			}

			$this->path = realpath(__DIR__ . '/../site/migrations/') . '/';
		});

		it('should be able to run migrations successfully', function() {
			expect($this->templates->get('testTemplate'))->toBe(null);
			$files = $this->migrations->migrate('Migration_2016_07_20_12_10');
			expect($files->count)->toBe(1);
			expect($this->templates->get('testTemplate'))->not->toBe(null);
		});

		it('should be able to run migrations successfully', function() {
			expect($this->fields->get('testField'))->toBe(null);
			$files = $this->migrations->migrate('Migration_2016_07_20_12_11_54');
			expect($files->count)->toBe(1);
			expect($this->fields->get('testField'))->not->toBe(null);
		});

		it('should find migrations in the filesystem', function () {
			$migrationFiles = $this->migrations->getMigrations();
			$keys = $migrationFiles->getKeys();

			expect($migrationFiles)->toBeAnInstanceOf(MigrationfilesArray::class);
			expect(count($migrationFiles))->toBeGreaterThan(0);
			expect(reset($keys))->toBe('Migration_2016_07_20_12_10');
			expect($migrationFiles->first())->toBeAnInstanceOf(Migrationfile::class);
		});

		it('should correctly convert filenames to classnames', function () {
			$classname = Migrationfile::filenameToClassname('2016-07-20_12-10.php');
			expect($classname)->toBe('Migration_2016_07_20_12_10');
		});

		it('should correctly convert classnames to filenames with seconds', function () {
			$classname = Migrationfile::filenameToClassname('2016-07-20_12-11-54.php');
			expect($classname)->toBe('Migration_2016_07_20_12_11_54');
		});

		it('should also convert complete paths to a valid classname', function () {
			$classname = Migrationfile::filenameToClassname('/var/www/html/site/migrations/2016-07-20_12-10.php');
			expect($classname)->toBe('Migration_2016_07_20_12_10');
		});

		it('should be able to read a migration\'s descriptions', function () {
			$migrationFiles = $this->migrations->getMigrations();
			expect($migrationFiles->first()->description)->toBe('Add testTemplate');
		});

		it('should be able to read a migration\'s type', function () {
			$migrationFiles = $this->migrations->getMigrations();
			expect($migrationFiles->first()->type)->toBe('TemplateMigration');
		});

		xit('should ensure the existance of the migrations directory', function () {
			expect($this->files)
				->toReceive('mkdir')
				->with(Arg::toContain($this->path));

			$this->migrations->createPath();
		});

		it('should uninstall modules on downgrade successfully', function () {
			expect($this->modules->isInstalled('Helloworld'))->toBe(false);

			$files = $this->migrations->migrate('Migration_2017_01_02_20_12_12');

			expect($this->modules->isInstalled('Helloworld'))->toBe(true);

			$files = $this->migrations->rollback('Migration_2017_01_02_20_12_12');

			expect($this->modules->isInstalled('Helloworld'))->toBe(false);
		});
	});
});