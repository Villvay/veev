<link rel="stylesheet" type="text/css" href="<?php echo BASE_URL_STATIC; ?>css/datatable.css" />

<?php if (!isset($category)){ ?>
<h2>Content Categories</h2>
<a href="<?php echo BASE_URL; ?>admin/categories/new" class="button">Add Category</a>
<br/><br/>
<?php 	render_table($schema, $categories, 'tbl-categories');
	}
	else{ ?>
<h2>Add/Edit Category</h2>
<?php 	render_form($schema, $category, 'admin/categories/'.$category['id']);
	} ?>
