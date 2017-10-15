<h2>Background Service</h2>

<?php if ($result == false){ ?>

	Background service is <b>Stopped</b>
	<br/><br/>

	<a class="button" href="<?php echo BASE_URL; ?>admin/services/start">
		<img src="<?php echo BASE_URL_STATIC; ?>icons/button_green_play.png" />
		Start
	</a>

<?php }
	else{ ?>

	Background service is <b>Running</b><!-- on pid <?php echo $pid; ?>-->
	<br/><br/>

	<a class="button" href="<?php echo BASE_URL; ?>admin/services/stop">
		<img src="<?php echo BASE_URL_STATIC; ?>icons/button_red_stop.png" />
		Stop
	</a>

	<pre>
<?php print_r($result); ?>
	</pre>

<?php } ?>
