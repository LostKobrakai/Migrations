<?php

class Migration_2015_10_21_12_15 extends FieldMigration {

	public static $description = "Create/Delete a field with a FieldMigration";

	protected function getFieldName(){ return 'field_migration'; }

	protected function getFieldType(){ return 'FieldtypeText'; }

	protected function fieldSetup(Field $f){
		$f->label = 'FieldMigration';
		$f->collapsed = Inputfield::collapsedNever;
		$f->columnWidth = 50;
	}

}