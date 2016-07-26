<pre style="font-family: 'Source Code Pro'; color: #00AAFF; background-color: white; margin-top: -2.6em">
    ___       ___       ___       ___       ___       ___       ___
   /\__\     /\  \     /\  \     /\  \     /\  \     /\  \     /\  \
  /::L_L_   _\:\  \   /::\  \   /::\  \   /::\  \    \:\  \   /::\  \
 /:/L:\__\ /\/::\__\ /:/\:\__\ /::\:\__\ /::\:\__\   /::\__\ /::\:\__\
 \/_/:/  / \::/\/__/ \:\:\/__/ \;:::/  / \/\::/  /  /:/\/__/ \:\:\/  /
   /:/  /   \:\__\    \::/  /   |:\/__/    /:/  /   \/__/     \:\/  /
   \/__/     \/__/     \/__/     \|__|     \/__/               \/__/
</pre>

# Intro

## Description

There where various threads on the ProcessWire Forums about the topic on how to reasonably handle multiple dev/staging and live environments with ProcessWire and at best handle it as automatically as possible. A git based workflow makes it easy to handle files, but the pain point of migrating db changes has even lead to multiple requests of not handling the template/field setup in the db at all.

![Admin UI](images/UI.png)

This module does help you to manage migration files, where any changes that affect the database can be stored in php files using just the simple [ProcessWire API](https://processwire.com/api/ref/) at your disposal. It's not as nice as using the Admin UI directly, but certainly better than trying to migrate changes manually –  possibly weeks after adding the changes. Also there's always the option to create helper modules, which can export changes made in the Admin UI to something usable in those migration files. I'm using something like that to export template access changes into a json format.

!!! warning "Create a backup before running migrations!"
    Your migrations will probably hold lot's of code, which does delete data. This module does not have any security measurements to prevent that. Be sure to test your migrations locally and save a database backup before running them.

## Migration Types

There are currently four types of migrations:

- __default (Migration)__  
Default migrations are the most free form migrations. There's just a description and two functions – `update()` and `downgrade()`. What you're doing in those functions is totally up to you, but it's recommended to try the best to keep changes as reversible as possible. Meaning that running update() and downgrade() once should have as less effect on the installation as possible. The ProcessWire API is available exactly like in modules using the `$this->pages`, `$this->config`, … syntax.

- __FieldMigration__
- __TemplateMigration__
- __ModuleMigration__  
These make your live easier by providing a more declarative way of migrating the creation of Fields/Templates or the installation of modules. All the boilerplate is handled by the base classes these migrations do extend, so you don't need to think about `update()` and `downgrade()`. You can rather just describe the item you want to handle and the rest is been taken care of.

## CLI

The module does include a CLI interface, which does allow the migrations to be run automatically by Continous Integration Systems or deployment scripts or just by yourself if you like the command-line. The script is located in the bin/ directory inside the module's folder. It does however require a composer package to work, which you can simply add by running `composer require league/climate` in your site directory (or the root directory for pw 3.0). Make sure to require composers autoload.php in your config.php for 2.x installations. 

The CLI does have a quite handy help page, which you get by running `php migrate -h` so I'm just adding the important bits of that here:

<pre>
> php migrate -h

[…]

Usage: migrate [-h, --help] [-i info, --info info] [-m migrate, --migrate migrate] [-n new, --new new] 
  [-nf newField, --newField newField] [-nm newModule, --newModule newModule] 
  [-nt newTemplate, --newTemplate newTemplate] [-r rollback, --rollback rollback]

Optional Arguments:
	-m migrate, --migrate migrate
		Run a specific migration or all new* ones if none given.
		* From latest migrated to newest.
	-r rollback, --rollback rollback
		Undo a specific migration or the latest one if none given.
	-n new, --new new
		Bootstrap a new migrations file. Optionally you can already supply a description.
	-nt newTemplate, --newTemplate newTemplate
		Bootstrap a new template migrations file. Optionally you can already supply a description.
	-nm newModule, --newModule newModule
		Bootstrap a new module migrations file. Optionally you can already supply a description.
	-nf newField, --newField newField
		Bootstrap a new field migrations file. Optionally you can already supply a description.
	-i info, --info info
		Get detailed info about a migration.
	-h, --help
		Show all commands of the cli tool.
</pre>

To run the CLI script from the project root simply add a file with the following content to the folder. This way you won't copy anything and it'll update with the module itself like normal.

```
:::php
<?php
// Full or relative path to the module's cli
include_once __DIR__ . '/site/modules/Migrations/bin/migrate';
```

## Helper Functions

There are already a handful of helper function included in the Migration base class, which tackle things I found to need way to much boilerplate for kinda simple changes. Read the code comments on what each of them does.

!!! note "The number of helper functions will probably grow over time."

```
:::php
<?php

/**
 * This does use @diogo's while loop technique to loop over all pages 
 * without getting memory exhaustion. 
 */
$this->eachPageUncache("template=toBeHidden", function($p){
  $p->setAndSave('status', Page::statusHidden);
}); 
```

```
:::php
<?php

/**
 * $template, $field, $reference = null, $after = true
 * The below function reads like this:
 * In the template … add the field … relative to the field … in the position after/before
 */
$this->insertIntoTemplate('basic-page', 'images', 'body', false); 
```

```
:::php
<?php

/**
 * Edit field settings in context of a template
 */
$this->editInTemplateContext('basic-page', 'title', function($f, $template){
  $f->label = 'Headline';
}); 
```

### Custom Helper Functions

You can create your own helper functions by using ProcessWire [Hooks](https://processwire.com/api/hooks/). These functions can then be used in your migrations.

```
:::php
<?php

// in ready.php
$wire->addHook('Migration::renameHome', function(HookEvent $event){
	$name = $event->arguments(0);
	$event->wire('pages')->get('/')->setAndSave('title', $name);
});

// in the migration::update method
$this->renameHome('Root'); 
// in the migration::downgrade method
$this->renameHome('Home'); 
```