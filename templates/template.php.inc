<?php

class {classname} extends TemplateMigration {

	public static $description = "{description}";

	protected function getTemplateName(){ return '{templateName}'; }

	protected function templateSetup(Template $t){
		//$t->label = 'basic';
		//$t->fieldgroup->add('body');
	}

}