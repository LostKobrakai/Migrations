<?php

class Migration_2016_07_20_12_10 extends TemplateMigration {

	public static $description = "Add testTemplate";

	protected function getTemplateName(){ return 'testTemplate'; }

	protected function templateSetup(Template $t){
		$t->label = 'Test Template';
	}

}