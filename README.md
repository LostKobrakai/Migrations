# Migrations [![Build Status](https://travis-ci.org/LostKobrakai/Migrations.svg?branch=master)](https://travis-ci.org/LostKobrakai/Migrations)

**This module is deprecated in favor of [RockMigrations](https://processwire.com/talk/topic/21212-rockmigrations-easy-migrations-from-devstaging-to-live-server/). It'll continue to work and I might fix some smaller incompatibilities if they're reported, but no major development will happen on this anymore.**

<pre style="font-family: monospace; color: #00AAFF; background-color: white">
    ___       ___       ___       ___       ___       ___       ___
   /\__\     /\  \     /\  \     /\  \     /\  \     /\  \     /\  \
  /::L_L_   _\:\  \   /::\  \   /::\  \   /::\  \    \:\  \   /::\  \
 /:/L:\__\ /\/::\__\ /:/\:\__\ /::\:\__\ /::\:\__\   /::\__\ /::\:\__\
 \/_/:/  / \::/\/__/ \:\:\/__/ \;:::/  / \/\::/  /  /:/\/__/ \:\:\/  /
   /:/  /   \:\__\    \::/  /   |:\/__/    /:/  /   \/__/     \:\/  /
   \/__/     \/__/     \/__/     \|__|     \/__/               \/__/
</pre>

Migrations is a module to manage migration files, which in themselves allow you to handle all the development steps, which affect the ProcessWire database, across multiple enviroments. Be it a simple local development and online live environment by a single developer or multiple developers working in a team with multiple server environments to go through before shipping.

Migration files are simple small class files, which utilize the [ProcessWire API](https://processwire.com/api/ref/) to run any database affecting changes. It's not as nice as using the Admin UI directly, but certainly better than trying to migrate changes manually &ndash; possibly weeks after adding the changes. 

They are NOT database migrations like you might know them from frameworks like Laravel or Ruby on Rails. Those files won't setup any database tables or change table column types. It's about recreating what normally would be done via the backend UI. Creating a field, removing a field from a template, installing a module and so on.

For more information on the module please visit: https://lostkobrakai.github.io/Migrations/  
Snippets using the module: https://github.com/LostKobrakai/MigrationSnippets
