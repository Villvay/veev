<link rel="stylesheet" type="text/css" href="<?php echo BASE_URL_STATIC; ?>css/catalog.css" />
<h2>Dashboard</h2>

<ul class="prods">
	<li>
		<a href="<?php echo BASE_URL; ?>admin/users" class="sel">
			<img src="<?php echo BASE_URL_STATIC; ?>icons/user.png" />
			<br/>Users
		</a>
	</li>
	<li>
		<a href="<?php echo BASE_URL; ?>admin/groups" class="sel">
			<img src="<?php echo BASE_URL_STATIC; ?>icons/user_group.png" />
			<br/>Groups
		</a>
	</li>
	<li>
		<a href="<?php echo BASE_URL; ?>admin/developer/errors" class="sel">
			<img src="<?php echo BASE_URL_STATIC; ?>icons/burning.png" />
			<br/>Errors
		</a>
	</li>
	<li>
		<a href="<?php echo BASE_URL; ?>admin/developer/database" class="sel">
			<img src="<?php echo BASE_URL_STATIC; ?>icons/database_check.png" />
			<br/>Database
		</a>
	</li>
	<li>
		<a href="<?php echo BASE_URL; ?>admin/developer/vcs" class="sel">
			<img src="<?php echo BASE_URL_STATIC; ?>icons/copy-document.png" />
			<br/>Version Control
		</a>
	</li>
	<li>
		<a href="<?php echo BASE_URL; ?>admin/inquiry" class="sel">
			<img src="<?php echo BASE_URL_STATIC; ?>icons/mail_download.png" />
			<br/>Inquiry
		</a>
	</li>
	<li>
		<a href="<?php echo BASE_URL; ?>admin/pages" class="sel">
			<img src="<?php echo BASE_URL_STATIC; ?>icons/document-lined-pen.png" />
			<br/>Pages
		</a>
	</li>
	<li>
		<a href="<?php echo BASE_URL; ?>admin/categories" class="sel">
			<img src="<?php echo BASE_URL_STATIC; ?>icons/tag_white_barcode.png" />
			<br/>Categories
		</a>
	</li>
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