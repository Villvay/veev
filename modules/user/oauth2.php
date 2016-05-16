<h2><?php echo $html_head['title']; ?></h2>

<?php flash_message_dump(); ?>

<?php if (isset($app)){ ?>
<p><?php echo $html_head['title']; ?> is requesting to access following modules behalf of you:</p>
<ul>
<?php 	foreach ($app['scopes'] as $module){ ?>
	<li><?php echo ucwords($module); ?></li>
<?php 	} ?>
</ul>
<form method="post">
	<input type="hidden" name="code" value="<?php echo $app['code']; ?>" />
	<input type="hidden" name="redirect" value="<?php echo $app['redirect']; ?>" />
	<input type="button" value="Deny" onclick="document.location='<?php echo $app['redirect']; ?>?error=denied';" />
	<input type="submit" value="Allow" />
</form>
<?php } ?>