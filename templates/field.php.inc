<?php

class {classname} extends FieldMigration {

	public static $description = "{description}";

	protected function getFieldName(){ return '{fieldName}'; }

	protected function getFieldType(){ return '{fieldType}'; }

	protected function fieldSetup(Field $f){
		//$f->label = 'Bodytext';
		//$f->collapsed = Inputfield::collapsedNever;

		//$this->insertIntoTemplate('', $f);
		//$this->editInTemplateContext('', $f, function(Field $f) {});
	}

}