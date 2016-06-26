<?php

class Migration_2015_10_21_12_17 extends TemplateMigration {

	public static $description = "Create/Delete a template with a TemplateMigration";

	protected function getTemplateName(){ return 'template_migration'; }

	protected function templateSetup(Template $t){
		$t->label = 'TemplateMigration';
		$t->urlSegments(true);

		// Migrations specific func
		// add field_migration before title
		$this->insertIntoTemplate($t, 'title', 'field_migration', false);

		// PW way could be
		// $t->fields->add()
		// $t->fields->insertBefore() 
		// …
	}

}