<link rel="stylesheet" type="text/css" href="<?php echo BASE_URL_STATIC; ?>css/datatable.css" />

<?php if (isset($pages)){ ?>
<h2>Static Pages</h2>
<a href="<?php echo BASE_URL; ?>admin/pages/add" class="button">Add Page</a>
<br/><br/>
<?php 	render_table($schema, $pages, 'tbl-pages');
	}
	else{ ?>
<h2>Add/Edit Page</h2>
<?php 	render_form($schema, $content, 'admin/pages');
?><style>iframe#content_ifr{height:300px !important; width:99.8% !important;}</style><?php
	} ?>
