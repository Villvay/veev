<h2>Services</h2>

<?php if ($result == false){ ?>
	Back-end service is <b>Stopped</b>
	<br/><br/>
	<a class="button" href="<?php echo BASE_URL; ?>admin/services/start">
		<img src="<?php echo BASE_URL_STATIC; ?>icons/button_green_play.png" />
		Start
	</a>
<?php }
	else{ ?>
	Back-end service is <b>Running</b> on pid <?php echo $pid; ?>
	<br/><br/>
	<a class="button" href="<?php echo BASE_URL; ?>admin/services/stop">
		<img src="<?php echo BASE_URL_STATIC; ?>icons/button_grey_pause.png" />
		Stop
	</a>
<?php } ?>

<pre>
<?php print_r($result2); ?>
</pre>
