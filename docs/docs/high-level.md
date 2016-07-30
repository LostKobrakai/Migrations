## The situation

So why should you bother creating all those migration files instead of using the backend UI? The issue is that database changes work in a linear fashion, which is hardly "mergeable" automatically. If you don't need to merge different changes to your ProcessWire database, you won't need migrations. But chances are high that you do need to do that at some time.

For example migrating changes between a running live system and a development system. It's super easy to push changes to template files or other code from development to the live server. But it's harder when it comes to changes affecting the database a.k.a changes to fields, templates or pages. Consider a scenario where a website is live and your client or other users are creating content online. At the same time you're developing new features for a few weeks or even month on your local machine. When it comes to moving those changes to the live site you need to merge your changes with the changes that happened on the live site.

Doing those kind of updates by hand is slow (downtime for the site) and can quite easily lead to errors if something is not 100% correctly transfered. A good amout of discipline in documentation while adding changes locally can improve these issues, but it's still a manual process that does take some time. This is especially apparent if multiple people work on the project and changes come from different people.

Migration files take the ambiguity out of this task by moving all needed changes into executable code. This way any database modifications can be reliably recreated on the live system and even be automated. Additionally those migrations can be added to source control – with all it's benefits – and are also easy to share to collaborators.

## Evaluation

The TL;DR points for using the module and migration files.

__Pro:__

- Automate changes
- Trackable by source control
- Simple to share
- Fast run/rollback to switch between configurations

__Cons:__

- Migrations need to be created for each change affecting the db
- Changes directly via the Admin UI should be avoided

## Workflow

So you get why you need to use migrations, but how does it actually work. In short: Not much different than before. You'll still add fields, change template settings or add "static" pages to your installation. Only that you won't do it via the backend UI, but rather by coding those changes with the [ProcessWire API](https://processwire.com/api/ref/). On a high level this will look like that:

<table>
	<thead>
		<tr>
			<th style="text-align: center;" colspan=2>update()</th>
			<th style="text-align: center;" colspan=2>downgrade()</th>
		</tr>
	</thead>
  <tr>
    <td colspan=4>
    	<span style="display: inline-block; width: 100%; text-align: center;">Common Base</span>
    </td>
  </tr>
  <tr>
    <td>Create the author field</td>
    <td>&darr;</td>
    <td>&uarr;</td>
    <td>Delete the author field</td>
  </tr>
  <tr>
    <td>Create the blog-post template</td>
    <td>&darr;</td>
    <td>&uarr;</td>
    <td>Delete the blog-post template</td>
  </tr>
  <tr>
    <td>Install SchedulePages module</td>
    <td>&darr;</td>
    <td>&uarr;</td>
    <td>Deinstall SchedulePages module</td>
  </tr>
  <tr>
    <td>Add the blog root page</td>
    <td>&darr;</td>
    <td>&uarr;</td>
    <td>Remove the blog-root</td>
  </tr>
  <tr>
    <td colspan=4>
    	<span style="display: inline-block; width: 100%; text-align: center;">…</span>
    </td>
  </tr>
</table>

You'll probably understand the left part of the table &ndash; but why bother implementing the removal of those changes as well? This is really mainly a workflow improvement as well as a savety net. If you can always rollback your changes you can undo any potentially erroneous migrations at any time and probably much quicker than manually. 

Even if it's not about errors, you can also use this to improve local work, too. For example in a multi-branch git workflow one might often switch between different branches. This will update the codebase, but not the database state. By rolling back migrations of the current branch, then switching the git branch and migrating all changes for the other branch one can keep file and db modifications in sync.