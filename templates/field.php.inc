<?php

class {classname} extends FieldMigration {

	public static $description = "{description}";

	protected function getFieldName(){ /*return 'body';*/ }

	protected function getFieldType(){ /*return 'FieldtypeText';*/ }

	protected function fieldSetup(Field $f){
		//$f->label = 'Bodytext';
		//$f->collapsed = Inputfield::collapsedNever;
	}

}