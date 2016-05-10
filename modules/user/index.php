<link rel="stylesheet" type="text/css" href="<?php echo BASE_URL_STATIC; ?>css/datatable.css" />
<h2>My Account</h2>

<?php render_form($schema, $user, 'user'); ?>

<br/>
<?php echo $_SERVER['HTTP_USER_AGENT']; ?>
<?php render_table($logins_schema, $logins, 'logins'); ?>
