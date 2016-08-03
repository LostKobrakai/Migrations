<?php
namespace tests;

use Field;
use Template;
use tests\dummies\Dummy;

describe('Migration', function(){

	before(function(){
		include_once realpath(__DIR__ . '/../tests/site/modules/Migrations') . '/Migration.php';
		include_once __DIR__ . '/dummies/Migration.php';
	});

	describe('Helpers', function(){

		beforeEach(function(){
			$this->migration = new Dummy();
		});

		it('should normalize field and fieldname input into a field object', function(){
			$field = $this->migration->publicGetField('title');
			$field2 = $this->migration->publicGetField($field);

			expect($field)->toBe($field2);

			foreach ([$field, $field2] as $f){
				expect($f)->toBeAnInstanceOf(Field::class);
				expect($f->name)->toBe('title');
			}
		});

		it('should normalize template and templatename input into a template object', function(){
			$template = $this->migration->publicGetTemplate('basic-page');
			$template2 = $this->migration->publicGetTemplate($template);

			expect($template)->toBe($template2);

			foreach ([$template, $template2] as $t){
				expect($t)->toBeAnInstanceOf(Template::class);
				expect($t->name)->toBe('basic-page');
			}
		});

		it('should loop over selector with uncaching the pages', function(){
			expect($this->pages)->toReceive('uncacheAll');

			$this->migration->publicEachPageUncache('id=1', function($p){
				expect($p->id)->toBe(1);
			});
		});

		it('should quickly allow a field to be edited in template context', function(){
			$this->migration->publicEditInTemplateContext('home', 'title', function($f){
				$f->columnWidth = 60;
			});

			$field = $this->fields->get('title');
			$template = $this->templates->get('home');
			$fieldgroup = $template->fieldgroup;

			$f = $fieldgroup->getField($field->name, true);

			expect($f->columnWidth)->toBe(60);

			$this->migration->publicEditInTemplateContext('home', 'title', function($f){
				$f->columnWidth = 100;
			});

			$field = $this->fields->get('title');
			$template = $this->templates->get('home');
			$fieldgroup = $template->fieldgroup;

			$f = $fieldgroup->getField($field->name, true);

			expect($f->columnWidth)->toBe(null);
		});

	});
});