!!! note "Work in Progress"

## Project Setup

Migrations can be brough into projects of any state with one small requirement. Any enviroment running those migrations does need to start at a common base. For already running projects this will probably be the live server's database, as that's the one that cannot allow for any changes.

If that baseline database is installed then one can start using migration files. Any new environment added to the project can simply start by installing that baseline as well and subsequently running all available migration files. This should result in the minimal setup to run your project. 

## Backups

!!! warning "Create a backup before running migrations"

Your migrations will probably hold lot's of code, which does delete data. This module does not have any security measurements to prevent that. Be sure to test your migrations locally and save a database backup before running them.

## Immutability of migration files

!!! warning "Do not modify already shared migration files"

As soon as a migration file is shared and possibly run on multiple environments it should never been edited further. If there's an issue with one of them fix it with another migration. The only exception to that rule is if a migration does delete unrecoverable data, but that would obviously need to be communicated to other parties working with those migrations.

## Order and Collision

For cases where multiple people work on a site it does happen that two people edit the same thing. If that happens it's best to keep the order of migrations to merge them. Do a rollback to before both of those colliding changes and resolve the issue in the latest of those migrations or even in a new migration. The latter is especially adviseable if you'd need to edit a [already shared migration](#immutability-of-migration-files).