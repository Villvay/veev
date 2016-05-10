<link rel="stylesheet" type="text/css" href="<?php echo BASE_URL_STATIC; ?>css/datatable.css" />
<h2>My Account</h2>

<?php render_form($schema, $user, 'user'); ?>
<br/>
<h3>Recently used devices</h3>
<?php render_table($logins_schema, $logins, 'logins'); ?>
<style>table.table-striped tr td img {width:24px;}</style>