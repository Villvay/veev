<link rel="stylesheet" type="text/css" href="<?php echo BASE_URL_STATIC; ?>css/datatable.css" />
<h2>Static Pages</h2>

<?php if (!isset($page)){
		render_table($schema, $pages, 'tbl-pages');
	}
	else{
		render_form($schema, $content, 'admin/pages/'.$content['slug']);
?><style>iframe#content_ifr{height:300px !important; width:99.8% !important;}</style><?php
	} ?>
