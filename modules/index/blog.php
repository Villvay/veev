<?php if (isset($article)){ ?>

<h2><?php echo $article['title']; ?></h2>
<small><?php echo beautify_datetime($article['published']); ?></small>
<?php echo $article['content']; ?>

<?php }else{ ?>

<h2><?php echo $lex[$lang]['blog']; ?></h2>

<ul class="blog">
<?php 	while ($article = mysql_fetch_assoc($blog)){ ?>
	<li>
		<a href="<?php echo BASE_URL; ?>blog/<?php echo $page; ?>/<?php echo $article['id']; ?>/<?php echo slugify($article['title']); ?>">
			<small><?php echo beautify_datetime($article['published']); ?></small>
			<h3><?php echo $article['title']; ?></h3>
			<p><?php echo shorten_string($article['content'], 250); ?></p>
		</a>
	</li>
<?php 	} ?>
</ul>

<?php 	if ($pages > 1){
			for ($i = 1; $i < $pages + 1; $i++){ ?>
<a class="button<?php echo $i == $page ? ' current' : ''; ?>" href="<?php echo BASE_URL; ?>blog/<?php echo $i; ?>"><?php echo $i; ?></a>
<?php 		}
		} ?>

<?php } ?>
