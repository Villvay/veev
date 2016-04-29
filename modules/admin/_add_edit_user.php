<link rel="stylesheet" type="text/css" href="<?php echo BASE_URL_STATIC; ?>css/datatable.css" />
<h2><?php echo $html_head['title']; ?></h2>

<?php render_form($schema, $a_user, 'admin/save-user', false,
		function($schema, $data){
			foreach($schema['auth']['enum'] as $module){ ?>
		<div class="form-group row">
			<label>
				<input type="checkbox" name="module[<?php echo $module; ?>]" /><?php echo $module; ?>
			</label>
		</div>
<?php 		}
		}); ?>
