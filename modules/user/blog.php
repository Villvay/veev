<link rel="stylesheet" type="text/css" href="<?php echo BASE_URL_STATIC; ?>css/datatable.css" />
<h2>Blog Articles</h2>

<?php if (!isset($article)){ ?>

<a href="<?php echo BASE_URL; ?>user/blog/add/new" class="button">Add a Blog Article</a>
<br/><br/>
<?php render_table($schema, $blog, 'tbl-blog'); ?>

<?php 	if ($pages > 1){
			for ($i = 1; $i < $pages + 1; $i++){ ?>
<a class="button<?php echo $i == $page ? ' current' : ''; ?>" href="<?php echo BASE_URL; ?>admin/blog/<?php echo $i; ?>"><?php echo $i; ?></a>
<?php 		}
		} ?>

<?php }
	else{
		render_form($schema, $article, 'user/blog/edit/'.$article['id']);
	} ?>
