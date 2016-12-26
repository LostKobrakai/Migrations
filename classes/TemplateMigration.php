<?php

abstract class TemplateMigration extends Migration{

	public static $description;

	abstract protected function getTemplateName();
	abstract protected function templateSetup(Template $t);

	/**
	 * Create a template
	 * Add a fieldgroup with the 'title' field
	 * @todo add all global fields instead
	 */
	public function update() {
		$t = new Template;
		$t->name = $this->getTemplateName();

		$fg = new Fieldgroup;
		$fg->name =  $this->getTemplateName();
		foreach($this->fields as $field) { // add global fields
			if(!($field->flags & Field::flagGlobal)) continue;
			$fg->add($field);
		}
		$fg->save();
		$t->fieldgroup = $fg;

		$t->fieldgroup->save();
		$t->save();

		$this->templateSetup($t);

		$t->fieldgroup->save();
		$t->save();

		return $t;
	}

	/**
	 * Delete the template 
	 * Does delete all pages of that template and does even delete system templates
	 */
	public function downgrade() {
		$template = $this->getTemplate($this->getTemplateName());
		$fieldgroup = $template->fieldgroup;

		$fg = $this->wire('fieldgroups')->get($template->name);
		$minNumberToError = $fg->id == $fieldgroup->id ? 2 : 1;

		if($fg->numTemplates() >= $minNumberToError) 
			throw new WireException("Cannot delete $template->name, because it's fieldgroup is used by at least one other template.");

		// Remove all pages of that template
		$selector = "template=$template, include=all, check_access=0";
		$this->eachPageUncache($selector, function($p) { $this->pages->delete($p, true); });

		// Delete fieldgroup and template
		$this->templates->delete($template);
		$this->fieldgroups->delete($fieldgroup);
	}

}
