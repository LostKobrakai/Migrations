# Examples 

## Getting started

Migrations are simple small classes, which consist of basically two methods and a static description variable. The two methods are named `update()` and `downgrade()` and are called respecively when running the migration or rolling it back.

The most basic migration does therefore look something like this:

```
:::php
<?php

class Migration_2015_10_21_12_13 extends Migration {
	
	/**
	 * Describe what the migration does
	 */
	public static $description = "Doesn't change anything, just shows messages.";

	/**
	 * Is executed when the migration is run
	 */
	public function update() {
		$this->message("Ran the update() function!");
	}

	/**
	 * Is executed when the migration is rolled back
	 */
	public function downgrade() {
		$this->message("Ran the downgrade() function!");
	}
}
```

You might do lot's of different things in those migrations, but I'll just name some examples:

### Change a field or template setting  

```
:::php
<?php

test
```

- Add some new pages to the installation
- Add a field to a tempate, which wasn't needed before


## Specialized Migration Types

Specialized migration types are much simpler to use, because their rollback functionality is much simpler and often tightly coupled to the data needed to run the migration in the first place. Therefore it's enough to supply the information about what to create/add and the deletion will just work as well.

### Field Migration

Does create a field and remove it on rollback.

```
:::php
<?php

class Migration_2015_10_21_12_15 extends FieldMigration {
	public static $description = "Create a new integer field to store the max. number of participants.";

	/**
	 * Supply the name of the field
	 */
	protected function getFieldName(){ return 'num_max_participants'; }

	/**
	 * Supply the type of the field
	 */
	protected function getFieldType(){ return 'FieldtypeInteger'; }

	/**
	 * Set the field up (it's saved automatically)
	 */
	protected function fieldSetup(Field $f){
		// Base Settings
		$f->label = 'Max. Participants';
		$f->description = 'The maximum number of participants for this event.';
		$f->collapsed = Inputfield::collapsedNever;

		// Fieldtype Settings
		$f->zeroNotEmpty = 1;

		// Inputfield Settings
		$f->min = 0;
		$f->inputType = 'number';

		// Add this field after the date field in the event template
		$this->insertIntoTemplate('event', 'date', $f->name);
	}
}
```

### Template Migration

Does create a template and remove it on rollback.

```
:::php
<?php

class Migration_2015_10_21_12_17 extends TemplateMigration {
	public static $description = "Create a new blog-post template";

	/**
	 * Supply the name of the template
	 */
	protected function getTemplateName(){ return 'blog-post'; }

	/**
	 * Set the field up (it's saved automatically)
	 */
	protected function templateSetup(Template $t){
		$t->label = 'Blog-Post';

		// Global fields are already added
		$this->insertIntoTemplate($t, 'title', 'body');
		$this->insertIntoTemplate($t, 'body', 'date_published');
		$this->insertIntoTemplate($t, 'date_published', 'author');

		// Allow specific urlSegments
		$t->urlSegments(array(
			'comments',
			'media'
		));
	}
}
```

### Module Migration

This is the simplest migration type. Add the module into the vcs (>3.0 can also use composer) and install it like this. Will only deinstalled the module on rollback.

!!! note "Do not remove the module from the vcs before running the migration"
		Trying to remove a module without it's files will fail. At best just let ProcessWire remove the files in a additional migration, which could also download it on rollback.

```
:::php
<?php

class Migration_2015_10_21_12_16 extends ModuleMigration {
	public static $description = "Install LazyCron";
	protected function getModuleName(){ return 'LazyCron'; }
}
```