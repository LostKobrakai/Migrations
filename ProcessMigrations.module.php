<?php
/**
 * Migrations (0.0.1)
 * 
 * 
 * @author Benjamin Milde
 * 
 * ProcessWire 2.x
 * Copyright (C) 2011 by Ryan Cramer
 * Licensed under GNU/GPL v2, see LICENSE.TXT
 * 
 * http://www.processwire.com
 * http://www.ryancramer.com
 * 
 */

class ProcessMigrations extends Process {

	protected $migrations = null;

	protected function m() {
		if(!is_null($this->migrations)) return $this->migrations;
		$this->migrations = $this->wire('modules')->get('Migrations');
		return $this->migrations;
	}

	private function arrayToLinks($links)
	{
		array_walk($links, function(&$i, $k){
			$i = "<a href='$i' target='_blank'>$k</a>";
		});
		return $links;
	}

	private function arrayToList($list)
	{
		return array_reduce($list, function($c, $item){
			return $c . "<li>$item</li>";
		}, '<ul>') . '</ul>';
	}

	public function ___execute() {
		$table = $this->modules->get("MarkupAdminDataTable");
		$table->setSortable(false);
		$table->setEncodeEntities(false);
		$table->headerRow(array(
			$this->_("Filename"),
			$this->_("Description"),
			$this->_("Action"),
			$this->_("Status"),
		));

		try {
			$files = $this->m()->getMigrations();
		} catch (WireException $e) {
			$files = array();
			$this->error($e->getMessage());
		}

		if(!count($files)){
			// Onboarding
			$button = $this->createNewButton();
			$button->addClass('head_button_clone');
			$button->addClass('space_after');

			$out = "<p>" . $this->_('It seems like the module couldn\'t find any migrations. Create a new migrations to see it listed here.') . "</p>";
			$out .= $button->render();
			$out .= "<p>" . $this->_('If this doesn\'t work please make sure the /site/migrations/ folder does exist and is readable by ProcessWire.') . "</p>";
			$out .= "<p>" . $this->_('Getting help:') . "</p>";
			$links = array(
				$this->_('Forum Support Topic') => 'https://processwire.com/talk/topic/13045-migrations/',
				$this->_('GitHub') => 'https://github.com/LostKobrakai/Migrations'
			);
			$out .= $this->arrayToList($this->arrayToLinks($links));
			return $out;
		}

		foreach ($files as $file) {
			$staticVars = $this->m()->getStatics($file);
			$basename = basename($file);
			$type = substr($this->m()->getType($file), 0, 1);
			
			$data = array();
			$data[] = basename($file, '.php') . ' <span style="color: #ddd">' . $type . '</span>';
			$data[] = nl2br($staticVars["description"]);
			$migrated = $this->m()->isMigrated($file);

			$iconMigrate = "<i class='fa fa-play' title='Migrate'></i>";
			$iconRollback = "<i class='fa fa-history' title='Rollback'></i>";

			!$migrated  
				? $data[$iconMigrate] = "execute/?id=" . md5($basename) 
				: $data[$iconRollback] = "revoke/?id=" . md5($basename);

			$migrated  
				? $data[] = '<span style="color: limegreen">Migrated</span>' 
				: $data[] = '<span style="color: #ddd">-</span>';

			$table->row($data);
		}

		$button = $this->wire('modules')->get('InputfieldButton');
		$button->attr('href', 'migrate/');
		$button->value = $this->_('Migrate Latest');
		$button->addClass('head_button_clone');
		$button->icon = 'play';

		$button2 = $this->createNewButton();

		return $table->render().$button->render().$button2->render();
	}

	protected function createNewButton()
	{
		$button = $this->wire('modules')->get('InputfieldButton');
		$button->attr('href', 'create/');
		$button->value = $this->_('Create New');
		$button->addClass('ui-priority-secondary');
		$button->icon = 'plus-circle';

		return $button;
	}

	public function ___executeExecute() {
		$file = $this->evaluateID();
		try{
			$this->m()->migrate($file);
		} catch (Exception $e) {
			$this->message("Could not migrate $file. Got error " . get_class($e) . " with " . $e->getMessage());
			return false;
		}
		$this->session->redirect($this->page->url);
	}

	public function ___executeRevoke() {
		$file = $this->evaluateID();
		try{
			$this->m()->rollback($file);
		} catch (Exception $e) {
			$this->message("Could not rollback $file. Got error " . get_class($e) . " with " . $e->getMessage());
			return false;
		}
		$this->session->redirect($this->page->url);
	}

	protected function evaluateID() {
		if(!$this->input->get->id) $this->session->redirect($this->page->url);

		$id = $this->input->get->text("id");
		$file = null;
		$files = $this->m()->getMigrations();

		foreach ($files as $f) {
			if(md5(basename($f)) === $id) $file = $f;
		}

		if(!$file){
			$this->error("Couldn't find file!");
			$this->message($id);
			$this->message($files);
			$this->session->redirect($this->page->url);
		}

		return $file;
	}

	public function ___executeMigrate()
	{	
		$toRun = $this->m()->getLatestToMigrate();

		if(!$count = count($toRun)){
			$this->message('Nothing to migrate');
			$this->session->redirect($this->page->url);
		}

		foreach ($toRun as $file) {
			if($this->m()->isMigrated($file)) continue;
			$this->m()->migrate($file);
			$this->message("Ran migration on " . basename($file) . ".");
		}

		$this->session->redirect($this->page->url);
	}

	public function ___executeCreate()
	{
		$types = array(
			'default' => $this->_('Default Migration'),
			'field' => $this->_('Field Migration'),
			'template' => $this->_('Template Migration'),
		);

		if($this->input->post->create){
			$type = $this->input->post->text('type');
			$desc = $this->input->post->textarea('desc');

			if(!in_array($type, array_keys($types))) $this->session->redirect($this->page->url);

			try{
				$file = $this->m()->createNew($desc, $type);
				$this->message("Created new migration " . basename($file));
			} catch(WireException $e) {
				$this->error($e->getMessage());
			}
			$this->session->redirect($this->page->url);
		}

		$form = $this->wire('modules')->get('InputfieldForm');
		$form->attr('action', '.');

		$type = $this->wire('modules')->get('InputfieldSelect');
		$type->attr('id+name', 'type');
		$type->required = true;
		$type->label = $this->_('Type');
		$type->description = $this->_('Type of migration.');
		foreach ($types as $key => $label) {
			$type->addOption($key, $label);
		}
		$type->columnWidth = 20;
		$form->add($type);

		$desc = $this->wire('modules')->get('InputfieldTextarea');
		$desc->attr('id+name', 'desc');
		$desc->label = $this->_('Description');
		$desc->description = $this->_('Description for the migration. (optional)');
		$desc->columnWidth = 80;
		$form->add($desc);

		$submit = $this->wire('modules')->get('InputfieldSubmit');
		$submit->attr('id+name', 'create');
		$submit->value = $this->_('Create');
		$submit->icon = 'plus-circle';
		$form->add($submit);

		return $form->render();
	}

}