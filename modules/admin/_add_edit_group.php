<link rel="stylesheet" type="text/css" href="<?php echo BASE_URL_STATIC; ?>css/datatable.css" />
<link rel="stylesheet" type="text/css" href="<?php echo BASE_URL_STATIC; ?>css/acl.css" />
<script src="<?php echo BASE_URL_STATIC; ?>js/acl.js"></script>
<h2><?php echo $html_head['title']; ?></h2>

<?php 	render_form($schema, $a_user, 'admin/save-group', false,
					//	Form Extra
					function($schema, $data){ ?>
		<div class="form-group row">
			<label for="auth">Access Control</label>
			<table class="ACL">
				<tr>
					<th>Module</th>
					<th>Full Access</th>
					<th>View</th>
					<th>Add</th>
					<th>Edit</th>
					<th>Delete</th>
				</tr>
<?php 					$data['auth'] = array_merge(json_decode(PUBLIC_MODULES, true), json_decode($data['auth'], true));
						foreach($schema['auth']['enum'] as $module)
								renderACLRow('', $module, $data['auth'], 0); ?>
			</table>
		</div>
<?php 				});
		//
		function renderACLRow($path, $module, $data, $indent){
			$acl = isset($data[$path.$module[0]]) ? $data[$path.$module[0]] : array(); ?>
				<tr class="level-<?php echo $indent; ?>">
					<td><?php echo str_repeat('- &nbsp; &nbsp; ', $indent).$module[0]; ?></td>
					<td><input type="checkbox" name="auth[<?php echo $path.$module[0]; ?>][full]"<?php echo in_array('view', $acl) && in_array('add', $acl) && in_array('edit', $acl) && in_array('delete', $acl) ? ' checked':''; ?> onclick="aclSelect(this);" /></td>
					<td><input type="checkbox" name="auth[<?php echo $path.$module[0]; ?>][view]"<?php echo in_array('view', $acl)?' checked':''; ?> onclick="aclSelect(this);" /></td>
					<td><input type="checkbox" name="auth[<?php echo $path.$module[0]; ?>][add]"<?php echo in_array('add', $acl)?' checked':''; ?> onclick="aclSelect(this);" /></td>
					<td><input type="checkbox" name="auth[<?php echo $path.$module[0]; ?>][edit]"<?php echo in_array('edit', $acl)?' checked':''; ?> onclick="aclSelect(this);" /></td>
					<td><input type="checkbox" name="auth[<?php echo $path.$module[0]; ?>][delete]"<?php echo in_array('delete', $acl)?' checked':''; ?> onclick="aclSelect(this);" /></td>
				</tr>
<?php 		if (count($module[1]) > 0)
				foreach($module[1] as $mod)
					renderACLRow($module[0].'/', $mod, $data, $indent+1);
		} ?>
