<link rel="stylesheet" type="text/css" href="<?php echo BASE_URL_STATIC; ?>css/datatable.css" />
<h2>User Groups</h2>

<a href="<?php echo BASE_URL; ?>admin/add-group" class="button">Add Group</a>
<br/><br/>

<?php render_table($schema, $groups, 'tbl-groups'); ?>
