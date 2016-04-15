<?php

abstract class FieldMigration extends Migration{

	public static $description;

	abstract protected function getFieldName();
	abstract protected function getFieldType();
	abstract protected function fieldSetup(Field $f);

	public function update() {
		$f = new Field;
		$f->name = $this->getFieldName();
		$f->type = $this->getFieldType();
		$this->fieldSetup($f);
		$f->save();
		return $f;
	}

	public function downgrade() {
		$this->deleteField($this->getFieldName());
	}

}