<?php

class Migration_2016_07_20_12_11_54 extends FieldMigration {

	public static $description = "Add testField";

	protected function getFieldName(){ return 'testField'; }

	protected function getFieldType(){ return 'FieldtypeText'; }

	protected function fieldSetup(Field $f){
		$f->label = 'Bodytext';
		$f->collapsed = Inputfield::collapsedNever;

		$f->maxlength = 120;
	}

}