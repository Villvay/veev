<link rel="stylesheet" type="text/css" href="<?php echo BASE_URL_STATIC; ?>css/datatable.css" />
<h2>News Articles</h2>

<?php if (!isset($article)){ ?>

<a href="<?php echo BASE_URL; ?>news/dashboard/add/new" class="button">Add a News Article</a>
<br/><br/>
<?php render_table($schema, $news, 'tbl-news'); ?>

<?php 	if ($pages > 1){
			for ($i = 1; $i < $pages + 1; $i++){ ?>
<a class="button<?php echo $i == $page ? ' current' : ''; ?>" href="<?php echo BASE_URL; ?>admin/news/<?php echo $i; ?>"><?php echo $i; ?></a>
<?php 		}
		} ?>

<?php }
	else{
		render_form($schema, $article, 'news/dashboard/');//'/edit/'.$article['id']
	} ?>
