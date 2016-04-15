<?php

abstract class Migration extends Wire{

	public static $description;

	abstract public function update();

	abstract public function downgrade();

	/**
	 * @param Template|string $template
	 * @param Field|string $field
	 * @param Field|string|null $reference
	 * @param bool $after
	 * @throws WireException
	 */
	protected function insertIntoTemplate ($template, $field, $reference = null, $after = true)
	{
		$template = $this->getTemplate($template);
		$fieldgroup = $template->fieldgroup;

		$field = $this->getField($field);

		if($reference instanceof Field)	$reference = $fieldgroup->get($reference->name);
		if(is_string($reference)) $reference = $fieldgroup->get($reference);

		if($reference instanceof Field) $fieldgroup->insertAfter($field, $reference);
		else $fieldgroup->append($field);

		$fieldgroup->save();
	}

	/**
	 * @param          $field
	 * @param callable $callback
	 */
	protected function editInTemplateContext ($template, $field, callable $callback)
	{
		$template = $this->getTemplate($template);
		$fieldgroup = $template->fieldgroup;
		$field = $this->getField($field);

		$context = $fieldgroup->getField($field->name, true);
		$callback($context, $template);
		$this->fields->saveFieldgroupContext($context, $fieldgroup);
	}

	/**
	 * @param Template|string $template
	 * @return Template
	 * @throws WireException
	 */
	private function getTemplate ($template)
	{
		$template = !is_string($template) ? $template : $this->templates->get($template);
		if(!$template instanceof Template) throw new WireException("Invalid template $template");
		return $template;
	}

	/**
	 * @param Fieldgroup|string $fieldgroup
	 * @return Fieldgroup
	 * @throws WireException
	 */
	private function getFieldgroup ($fieldgroup)
	{
		$fieldgroup = !is_string($fieldgroup) ? $fieldgroup : $this->fieldgroups->get($fieldgroup);
		if(!$fieldgroup instanceof Fieldgroup) throw new WireException("Invalid fieldgroup $fieldgroup");
		return $fieldgroup;
	}

	/**
	 * @param Field|string $field
	 * @return Field
	 * @throws WireException
	 */
	private function getField ($field)
	{
		$field = !is_string($field) ? $field : $this->fields->get($field);
		if(!$field instanceof Field) throw new WireException("Invalid field $field");
		return $field;
	}

	/**
	 * @param Field|string $field
	 * @throws WireException
	 */
	public function deleteField ($field)
	{
		$field = $this->getField($field);

		$fgs = $field->getFieldgroups();

		foreach($fgs as $fg){
			$fg->remove($field);
			$fg->save();
		}

		$this->fields->delete($field);
	}

	/**
	 * @param Field|string $field
	 * @throws WireException
	 */
	public function deleteTemplate ($template)
	{
		$template = $this->getTemplate($template);
		$fieldgroup = $this->getFieldgroup($template->name);

		$this->templates->delete($template);
		$this->fieldgroups->delete($fieldgroup);
	}
}