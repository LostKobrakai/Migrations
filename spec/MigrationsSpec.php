<?php
namespace tests;

use Kahlan\Arg;
use Kahlan\Plugin\Monkey;
use Kahlan\Plugin\Stub;

describe('Migrations Module', function () {

	before(function () {

		// Install the module
		$this->modules->resetCache();
		if ($this->modules->isInstallable('Migrations', true)) {
			$this->modules->install('Migrations');
		}

		$this->migrations = $this->modules->get('Migrations');

		$this->path = realpath(__DIR__ . '/../tests/site/migrations/') . '/';
	});

	describe('Basic Functionality', function () {

		it('should be initable', function () {
			$callback = function () {
				$m = new \Migrations();
				$m->init();
			};

			expect($callback)->not->toThrow();
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

		it('should ensure the existance of the migrations directory', function () {
			expect($this->files)
				->toReceive('mkdir')
				->with(Arg::toContain($this->path));

			$this->migrations->createPath();
		});

	});

	describe('Migrations', function () {

		afterEach(function () {
			$classes = $this->migrations->getRunMigrations();

			foreach (array_reverse($classes) as $class) {
				$this->migrations->rollbackClass($class);
			}
		});

		it("should correctly execute template migrations", function () {
			$this->migrations->migrateClass('Migration_2016_07_20_12_10');

			$t = $this->templates->get('testTemplate');

			expect($t)->toBeAnInstanceOf('Template');
			expect($t->label)->toBe('Test Template');
		});

		it("should correctly rollback template migrations", function () {
			$t = $this->templates->get('testTemplate');
			expect($t)->toBe(null);
		});

		it("should correctly return ran migrations", function () {
			$filename = $this->migrations->classnameToFilename('Migration_2016_07_20_12_10');

			expect($this->migrations->getRunMigrations())->toBe([]);
			expect($this->migrations->isMigrated($filename))->toBe(false);

			$this->migrations->migrateClass('Migration_2016_07_20_12_10');

			expect($this->migrations->isMigrated($filename))->toBe(true);
			expect($this->migrations->getRunMigrations())->toBe(['Migration_2016_07_20_12_10']);
		});

		it("should return the latest migrations to migrate, even though older ones are still not migrated", function () {
			$filename1 = $this->migrations->classnameToFilename('Migration_2016_07_20_12_10');
			$filename2 = $this->migrations->classnameToFilename('Migration_2016_07_20_12_11_54');
			$filename1 = $this->path . $filename1;
			$filename2 = $this->path . $filename2;

			expect($this->migrations->getLatestToMigrate())->toBe([$filename1, $filename2]);

			$this->migrations->migrateClass('Migration_2016_07_20_12_11_54');

			expect($this->migrations->getLatestToMigrate())->toBe([]);
		});

	});

	describe('Creation', function () {

		beforeEach(function () {
			Stub::on($this->migrations)->method('getTime')->andReturn(123, 124, 125, 126);

			$this->types = ['default', 'field', 'module', 'template'];
			$this->typeClasses = ['Default', 'FieldMigration', 'ModuleMigration', 'TemplateMigration'];

			$this->filenames = [
				'1970-01-01_00-02-03.php',
				'1970-01-01_00-02-04.php',
				'1970-01-01_00-02-05.php',
				'1970-01-01_00-02-06.php',
			];
		});

		afterEach(function () {
			foreach ($this->filenames as $f){
				$p = $this->path . $f;
				if (is_file($p)) unlink($p);
			}
		});

		it("should be able to create new migrations", function () {
			$filename = $this->migrations->createNew();

			expect($filename)->toContain('site/migrations/1970-01-01_00-02-03.php');
		});

		it("should only create one new file", function () {
			$migsBefore = $this->migrations->getMigrations();
			$this->migrations->createNew();
			$migsAfter = $this->migrations->getMigrations();

			expect(count($migsAfter))->toBe(count($migsBefore) + 1);
		});

		it("should only correctly create descriptions (for all types)", function () {
			foreach ($this->types as $type){
				$filename = $this->migrations->createNew("testDesc with lines\nDumm", $type);
				$staticVars = $this->migrations->getStatics($filename);
				expect($staticVars["description"])->toBe("testDesc with lines\nDumm");
			}
		});

		it("should only correctly create types", function () {
			foreach ($this->types as $key => $type){
				$filename = $this->migrations->createNew("testDesc with lines\nDumm", $type);
				$type = $this->migrations->getType($filename);
				expect($type)->toBe($this->typeClasses[$key]);
			}
		});

	});

});