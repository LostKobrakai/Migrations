# Migrations

<pre style="font-family: monospace; color: #00AAFF; background-color: white">
    ___       ___       ___       ___       ___       ___       ___
   /\__\     /\  \     /\  \     /\  \     /\  \     /\  \     /\  \
  /::L_L_   _\:\  \   /::\  \   /::\  \   /::\  \    \:\  \   /::\  \
 /:/L:\__\ /\/::\__\ /:/\:\__\ /::\:\__\ /::\:\__\   /::\__\ /::\:\__\
 \/_/:/  / \::/\/__/ \:\:\/__/ \;:::/  / \/\::/  /  /:/\/__/ \:\:\/  /
   /:/  /   \:\__\    \::/  /   |:\/__/    /:/  /   \/__/     \:\/  /
   \/__/     \/__/     \/__/     \|__|     \/__/               \/__/
</pre>

Migrations is a module to manage migration files, which in themselves allow you to handle all the development steps affecting the ProcessWire database across multiple enviroments. Be it a simple local development and online live environment by a single developer or multiple developers working in a team with multiple server enviroments to go through before shipping.

Migrations Files are simple small class files, which utilize the  [ProcessWire API](https://processwire.com/api/ref/) to run any changes affecting the database. It's not as nice as using the Admin UI directly, but certainly better than trying to migrate changes manually &ndash; possibly weeks after adding the changes. 

For more information on the module please visit: https://lostkobrakai.github.io/Migrations/  
Snippets using the module: https://github.com/LostKobrakai/MigrationSnippets