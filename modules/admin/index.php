<link rel="stylesheet" type="text/css" href="<?php echo BASE_URL_STATIC; ?>css/catalog.css" />
<h2>Dashboard</h2>

<ul class="prods">
<?php
	function renderIcon($data){
		if (isset($data['url'])){
?>
	<li>
		<a href="<?php echo $data['url']; ?>" class="sel">
			<img src="<?php echo BASE_URL_STATIC; ?>icons/<?php echo $data['icon']; ?>.png" />
			<br/><?php echo $data['title']; ?>
		</a>
	</li>
<?php
		}
		else{
?>
	<li>
		<a href="<?php echo BASE_URL; ?><?php echo $data['module']; ?>/<?php echo $data['method']; ?>" class="sel">
			<img src="<?php echo BASE_URL_STATIC; ?>icons/<?php echo $data['icon']; ?>.png" />
			<br/><?php echo $data['title']; ?>
		</a>
	</li>
<?php
		}
	}
	$icons = array(
		array('module' => 'admin', 'method' => 'users', 'icon' => 'user', 'title' => 'Users'),
		array('module' => 'admin', 'method' => 'groups', 'icon' => 'user_group', 'title' => 'Groups'),
		array('module' => 'admin', 'method' => 'config', 'icon' => 'speed_kmh', 'title' => 'Configure'),
		array('module' => 'admin', 'method' => 'services', 'icon' => 'nodejs', 'title' => 'Services'),
		array('module' => 'admin', 'method' => 'logs', 'icon' => 'monitor', 'title' => 'Logs'),
		array('module' => 'admin/developer', 'method' => 'database', 'icon' => 'database_check', 'title' => 'Database'),
		//array('module' => 'admin', 'method' => 'categories', 'icon' => 'tag_white_barcode', 'title' => 'Categories'),
		array('module' => 'admin', 'method' => 'pages', 'icon' => 'document-lined-pen', 'title' => 'Pages')
	);
	foreach ($icons as $icon)
		if (!isset($icon['module']) || checkIfAuthorized($user, $icon['module']))
			renderIcon($icon);
?>
</ul>
<style>
ul.prods li img {
    width: 48px;
    height: 48px;
}
ul.prods li a.sel {
    width: 70px;
    height: 100px;
}
</style>