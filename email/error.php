<html>
	<body>
		<h2>An error has occured</h2>
		<b><?php echo $errno; ?>: <?php echo $errstr; ?></b>
		<br/>
		<?php echo $errfile; ?>: <?php echo $errline; ?>
		<br/>
		<pre>
<?php
	$lines = explode("\n", file_get_contents($errfile));
	$from = $errline - 5;
	if ($from < 0)
		$from = 0;
	$to = $errline + 5;
	if ($to > count($lines)-1)
		$from = count($lines)-1;
	for ($i = $from; $i < $to; $i++){
		if ($i == $errline)
			echo '<div style="color:red;">'.$lines[$i].'</div>';
		else
			echo $lines[$i].'<br/>';
	}
?>
		</pre>
	</body>
</html>