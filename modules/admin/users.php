<link rel="stylesheet" type="text/css" href="<?php echo BASE_URL_STATIC; ?>css/datatable.css" />
<h2>User Accounts</h2>

<a href="<?php echo BASE_URL; ?>admin/add-user" class="button">Add User</a>
<br/><br/>

<?php render_table($schema, $users, 'tbl-users'); ?>
