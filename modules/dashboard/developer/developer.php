<link rel="stylesheet" type="text/css" href="<?php echo BASE_URL_STATIC; ?>css/datatable.css" />
<h2>My Apps</h2>

<a href="<?php echo BASE_URL; ?>dashboard/developer/create-app" class="button">Create App</a>
<br/><br/>
<?php render_table($schema, $apps, 'tbl-apps'); ?>