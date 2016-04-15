<?php

abstract class TemplateMigration extends Migration{

	public static $description;

	abstract protected function getTemplateName();
	abstract protected function templateSetup(Template $t);

	public function update() {
		$t = new Template;
		$t->name = $this->getTemplateName();
		$fg = new Fieldgroup;
		$fg->name =  $this->getTemplateName();
		$fg->add("title");
		$fg->save();
		$t->fieldgroup = $fg;
		$this->templateSetup($t);
		$t->fieldgroup->save();
		$t->save();
		return $t;
	}

	public function downgrade() {
		$this->deleteTemplate($this->getTemplateName());
	}

}