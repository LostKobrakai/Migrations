<?php

abstract class Migration extends Wire{

	public static $description;

	abstract public function update();

	abstract public function downgrade();

	/**
	 * Cycle over a group of pages without running into memory exhaustion
	 * 
	 * @param string   $selector
	 * @param callable $callback
	 * @return int     $num
	 */
	protected function eachPageUncache($selector, callable $callback)
	{
		$num = 0;
		$id = 0;
		while (true) {
			$p = $this->pages->get("{$selector}, id>$id");
			if(!$id = $p->id) break;
			$callback($p);
			$this->pages->uncacheAll($p);
			$num++;
		}
		return $num;
	}

	/**
	 * Insert a field into a template optionally at a specific position.
	 * 
	 * @param Template|string   $template
	 * @param Field|string      $field
	 * @param Field|string|null $reference
	 * @param bool              $after
	 * @throws WireException
	 */
	protected function insertIntoTemplate ($template, $field, $reference = null, $after = true)
	{
		$template = $this->getTemplate($template);
		$fieldgroup = $template->fieldgroup;
		$method = $after ? 'insertAfter' : 'insertBefore';

		$field = $this->getField($field);

		// Get reference if supplied
		if($reference instanceof Field)	
			$reference = $fieldgroup->get($reference->name);
		else if(is_string($reference))  
			$reference = $fieldgroup->get($reference);

		// Insert field or append
		if($reference instanceof Field)
			$fieldgroup->$method($field, $reference);
		else 
			$fieldgroup->append($field);

		$fieldgroup->save();
	}

	/**
	 * Insert a field into a template optionally at a specific position.
	 *
	 * @param Template|string   $template
	 * @param Field|string      $field
	 * @return bool     $success (true => success, false => failure)
	 * @throws WireException
	 */
    protected function removeFromTemplate($template, $field) {
        $t = $this->getTemplate($template);
        $f = $this->getField($field);
        $success = $t->fieldgroup->remove($f);
        $t->fieldgroup->save();
        return $success;
    }


	/**
	 * Edit a field in template context
	 * 
	 * @param Template|string $template
	 * @param Field|string    $field
	 * @param callable        $callback
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
	protected function getTemplate ($template)
	{
		$template = !is_string($template) ? $template : $this->templates->get($template);
		if(!$template instanceof Template) throw new WireException("Invalid template $template");
		return $template;
	}

	/**
	 * @param Field|string $field
	 * @return Field
	 * @throws WireException
	 */
	protected function getField ($field)
	{
		$field = !is_string($field) ? $field : $this->fields->get($field);
		if(!$field instanceof Field) throw new WireException("Invalid field $field");
		return $field;
	}
}