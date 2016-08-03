<?php
use Kahlan\Filter\Filter;
use Kahlan\Plugin\Stub;
use Kahlan\Reporter\Coverage;
use Kahlan\Reporter\Coverage\Driver\Phpdbg;
use Kahlan\Reporter\Coverage\Driver\Xdebug;

# use ProcessWire\ProcessWire;

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

/**
 * Set default values
 */
$args = $this->args();
$args->argument('reporter', 'default', 'verbose');
$args->argument('src', 'default', 'tests/site/modules/Migrations');

/**
 * Add processwire globals to the suite, so they're available in all tests
 */
Filter::register('processwire.globals', function ($chain){
		define("PROCESSWIRE_TEST_RUNNING", true);
		new \Migrations();
    $config = ProcessWire::buildConfig(__DIR__ . '/');
    $config->allowExceptions = true;
    $wire = new ProcessWire($config);

    $root = $this->suite();
    foreach($wire->wire('all')->getArray() as $api => $data){
      if($api == "process") continue;
      $root->$api = $data;
    }

    return $chain->next();
});

Filter::apply($this, 'run', 'processwire.globals');

// Add *.module to coverage
// Exclude Spinner.php as well
Filter::register('coverage', function($chain) {
	if (!$this->args()->exists('coverage')) {
		return;
	}
	$reporters = $this->reporters();
	$driver = null;
	if (PHP_SAPI === 'phpdbg') {
		$driver = new Phpdbg();
	} elseif (extension_loaded('xdebug')) {
		$driver = new Xdebug();
	} else {
		fwrite(STDERR, "ERROR: PHPDBG SAPI has not been detected and Xdebug is not installed, code coverage can't be used.\n");
		exit(-1);
	}
	$coverage = new Coverage([
		'verbosity' => $this->args()->get('coverage') === null ? 1 : $this->args()->get('coverage'),
		'driver' => $driver,
		'path' => $this->args()->get('src'),
		'colors' => !$this->args()->get('no-colors'),
		'include' => ['*.php', '*.module'],
		'exclude' => ['tests/site/modules/Migrations/bin/Spinner.php']
	]);
	$reporters->add('coverage', $coverage);
});

Filter::apply($this, 'coverage', 'coverage');