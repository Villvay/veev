<form method="post" enctype="multipart/form-data">
	<input type="file" name="file" />
	<input type="submit" value="Upload" />
</form>

<ul>
<?php foreach ($files as $file){ ?>
	<li onclick="setImg('<?php echo BASE_URL_STATIC; ?><?php echo $path; ?>/<?php echo $file['name']; ?>', this);">
		<img src="<?php echo BASE_URL_STATIC; ?><?php echo $path; ?>/<?php echo $file['thumb']; ?>" width="80" />
	</li>
<?php } ?>
</ul>

<script>
	var sel_li = false;
	function setImg(img, li){
		if (sel_li != false)
			sel_li.className = '';
		/*if (sel_li == li){}*/
		li.className = 'sel';
		sel_li = li;
		//
		parent.setImg(img);
	}
</script>

<style>
	body{
		background-color:#FFFFFF;
	}
	ul{
		list-style-type:none;
		margin:0px;
		padding:0px;
	}
	ul li{
		border:1px solid #8EAEFF;
		margin:4px;
		padding:2px;
		border-radius:2px;
		float:left;
	}
	ul li:hover{
		border:1px solid #5787FF;
		box-shadow:0px 0px 16px #FFFF66;
	}
	li.sel, ul li.sel:hover{
		border:2px solid #5787FF;
		margin:3px;
		box-shadow:0px 0px 12px 4px #FFFF66;
	}
</style>
