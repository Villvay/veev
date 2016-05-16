<link rel="stylesheet" type="text/css" href="<?php echo BASE_URL_STATIC; ?>css/datatable.css" />
<h2><?php echo $html_head['title']; ?></h2>

<?php if (!isset($article)){ ?>

<a href="<?php echo BASE_URL; ?>dashboard/add/new" class="button">Add Content</a>
<br/><br/>
<?php render_table($schema, $news, 'tbl-news'); ?>

<?php 	if ($pages > 1){
			for ($i = 1; $i < $pages + 1; $i++){ ?>
<a class="button<?php echo $i == $page ? ' current' : ''; ?>" href="<?php echo BASE_URL; ?>dashboard/index/<?php echo $i; ?>"><?php echo $i; ?></a>
<?php 		}
		} ?>

<?php }
	else{
		render_form($schema, $article, 'dashboard');
?><style>iframe#content_ifr{height:300px !important; width:99.8% !important;}</style><?php
	} ?>