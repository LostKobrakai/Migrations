# How does all of this work on a higher level and why do I need this?

Above is the question I need to answer here. So first of all the reason behind all this seemingly laborous migration files.

Migrating changes between a running live system and a development system is super easy for changes to files, but hard if it comes to changes affecting the database. Consider a scenario where a site is live (and content is being created there) and you're developing new features for a few weeks or even month. When it comes to moving those changes to the live site you need a way to know which templates need updating and which fields might have been created. 

Doing those kind of updates by hand is slow (downtime for the site), can quite easily lead to minor or even mayor errors if something is not 100% correctly transfered and it requires a good amout of discipline before that, so all changes are actually catched in development, especially if multiple people work on the project.

Migration files take the ambiguity out of the task by moving all those changes into executable code. This way they can be relyably recreated on the live system and even be automated. Additionally those migrations live in source control with all it's benefits and are also easy to share to collaborators.

## Evaluation (tldr;)

__Pro:__

- Automate changes
- Source control for them
- Simpler for collaboration
- Fast run/rollback to switch between configurations

__Cons:__

- Migrations need to be created for each change affecting the db
- Changes directly via the Admin UI should be avoided

## Workflow

Some of you might know database migrations from frameworks like Laravel or Rails. These specifically change the db schema of the application. That's not what we're doing. We won't edit db tables or otherwise interact with mysql directly as long as there's a ProcessWire API being far more simpler to use. 

On a high level migrations will rather look like in this table. 

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

*[Work in Process]*