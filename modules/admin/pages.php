<link rel="stylesheet" type="text/css" href="<?php echo BASE_URL_STATIC; ?>css/datatable.css" />
<h2>Static Pages</h2>

<?php if (!isset($page)){
		render_table($schema, $pages, 'tbl-pages');
	}
	else{
		render_form($schema, $content, 'admin/page/'.$content['slug']);
	} ?>
