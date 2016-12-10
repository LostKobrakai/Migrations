<?php

require(__DIR__ . '/../index.php');

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
			$this->migrations->migrateClass('Migration_2016_07_20_12_10');
			expect($this->templates->get('testTemplate'))->not->toBe(null);
		});

		it('should be able to run migrations successfully', function() {
			expect($this->fields->get('testField'))->toBe(null);
			$this->migrations->migrateClass('Migration_2016_07_20_12_11_54');
			expect($this->fields->get('testField'))->not->toBe(null);
		});

		it('should find migrations in the filesystem', function () {
			$migrationFiles = $this->migrations->getMigrations();
			$keys = array_keys($migrationFiles);

			expect(count($migrationFiles))->toBeGreaterThan(0);
			expect(reset($keys))->toBe('2016-07-20_12-10');
			expect(reset($migrationFiles))->toContain('site/migrations/2016-07-20_12-10.php');
		});

		it('should correctly convert classnames to filenames and back again', function () {

			$filename = $this->migrations->classnameToFilename('Migration_2016_07_20_12_10');
			$classname = $this->migrations->filenameToClassname($filename);

			expect($filename)->toBe('2016-07-20_12-10.php');
			expect($classname)->toBe('Migration_2016_07_20_12_10');
		});

		it('should correctly convert classnames to filenames with seconds', function () {

			$filename = $this->migrations->classnameToFilename('Migration_2016_07_20_12_11_54');
			$classname = $this->migrations->filenameToClassname($filename);

			expect($filename)->toBe('2016-07-20_12-11-54.php');
			expect($classname)->toBe('Migration_2016_07_20_12_11_54');
		});

		it('should also convert complete paths to a valid classname', function () {
			$classname = $this->migrations->filenameToClassname('/var/www/html/site/migrations/2016-07-20_12-10.php');

			expect($classname)->toBe('Migration_2016_07_20_12_10');
		});

		it('should be able to read a migration\'s descriptions', function () {
			$filename = $this->migrations->classnameToFilename('Migration_2016_07_20_12_10');
			$staticVars = $this->migrations->getStatics($filename);

			expect($staticVars["description"])->toBe('Add testTemplate');
		});

		it('should be able to read a migration\'s type', function () {
			$filename = $this->migrations->classnameToFilename('Migration_2016_07_20_12_10');
			$type = $this->migrations->getType($filename);

			expect($type)->toBe('TemplateMigration');
		});

		it('should be able to read stuff from a full pathname as well', function () {
			$filename = $this->migrations->classnameToFilename('Migration_2016_07_20_12_10');
			$filename = $this->path . $filename;
			$type = $this->migrations->getType($filename);
			$staticVars = $this->migrations->getStatics($filename);

			expect($staticVars["description"])->toBe('Add testTemplate');
			expect($type)->toBe('TemplateMigration');
		});

		xit('should ensure the existance of the migrations directory', function () {
			expect($this->files)
				->toReceive('mkdir')
				->with(Arg::toContain($this->path));

			$this->migrations->createPath();
		});
	});
});