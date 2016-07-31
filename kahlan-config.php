<?php
use Kahlan\Filter\Filter;
use ProcessWire\ProcessWire;

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$args = $this->args();
$args->argument('reporter', 'default', 'verbose');
$args->argument('src', 'default', 'tests/site/modules/Migrations');

Filter::register('processwire.globals', function ($chain){
    define("PROCESSWIRE_TEST_RUNNING", true);
    $config = ProcessWire::buildConfig(__DIR__ . '/tests/');
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