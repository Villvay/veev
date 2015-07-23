<?php 	$files_path = STATIC_FILES_ROOT.$path.'/';
		$slides = array();
		if (is_dir($files_path) && $dh = opendir($files_path)){
			while (($file = readdir($dh)) !== false){
				if ($file == '.' || $file == '..' || is_dir($files_path.'/'.$file)){
				}
				else if (substr($file, 0, 6) == 'thumb_'){
					$slides[] = substr($file, 6);
				}
			}
			closedir($dh);
		}
		if (count($slides) > 0){ ?>
<script src="<?php echo BASE_URL_STATIC; ?>js/slides.js"></script>
<link rel="stylesheet" type="text/css" href="<?php echo BASE_URL_STATIC; ?>css/slides.css" />
<ul class="slides">
<?php 		foreach ($slides as $slide){ ?>
	<li><img src="<?php echo BASE_URL_STATIC; ?><?php echo $path; ?>/<?php echo $slide; ?>" /></li>
<?php 		} ?>
</ul>
<script>new slideShow(document.querySelectorAll('ul.slides')[0]);</script>
<?php 	} ?>
