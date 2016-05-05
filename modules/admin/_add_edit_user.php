<link rel="stylesheet" type="text/css" href="<?php echo BASE_URL_STATIC; ?>css/datatable.css" />
<h2><?php echo $html_head['title']; ?></h2>

<!--div class="half"></div-->
<?php 	render_form($schema, $a_user, 'admin/save-user', false,
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
			$acl = isset($data[$path.$module[0]]) ? array_flip($data[$path.$module[0]]) : array(); ?>
				<tr class="level-<?php echo $indent; ?>">
					<td><?php echo str_repeat('- &nbsp; &nbsp; ', $indent).$module[0]; ?></td>
					<td><input type="checkbox" name="auth[<?php echo $path.$module[0]; ?>][full]"<?php echo isset($acl['view']) && isset($acl['add']) && isset($acl['edit']) && isset($acl['delete']) ? ' checked':''; ?> onclick="aclSelect(this);" /></td>
					<td><input type="checkbox" name="auth[<?php echo $path.$module[0]; ?>][view]"<?php echo isset($acl['view'])?' checked':''; ?> onclick="aclSelect(this);" /></td>
					<td><input type="checkbox" name="auth[<?php echo $path.$module[0]; ?>][add]"<?php echo isset($acl['add'])?' checked':''; ?> onclick="aclSelect(this);" /></td>
					<td><input type="checkbox" name="auth[<?php echo $path.$module[0]; ?>][edit]"<?php echo isset($acl['edit'])?' checked':''; ?> onclick="aclSelect(this);" /></td>
					<td><input type="checkbox" name="auth[<?php echo $path.$module[0]; ?>][delete]"<?php echo isset($acl['delete'])?' checked':''; ?> onclick="aclSelect(this);" /></td>
				</tr>
<?php 		if (count($module[1]) > 0)
				foreach($module[1] as $mod)
					renderACLRow($module[0].'/', $mod, $data, $indent+1);
		} ?>

<script>
	function aclSelect(chk){
		var name = chk.name.replace(/]/g, '').split('[');
		var view = chk.form.elements[name[0]+'['+name[1]+'][view]'];
		var add = chk.form.elements[name[0]+'['+name[1]+'][add]'];
		var edit = chk.form.elements[name[0]+'['+name[1]+'][edit]'];
		var del = chk.form.elements[name[0]+'['+name[1]+'][delete]'];
		var full = chk.form.elements[name[0]+'['+name[1]+'][full]'];
		if (name[2] == 'full')
			view.checked = add.checked = edit.checked = del.checked = chk.checked;
		else
			full.checked = (view.checked && add.checked && edit.checked && del.checked);
		for (var i = 0; i < chk.form.elements.length; i++)
			if (chk.form.elements[i].name.startsWith(name[0]+'['+name[1]+'/') && (name[2] == 'full' || chk.form.elements[i].name.endsWith(']['+name[2]+']'))){
				chk.form.elements[i].disabled = !chk.checked;
				if (!chk.checked)
					chk.form.elements[i].checked = false;
			}
	}
</script>
<style>
	table.ACL{
		border-collapse:collapse;
	}
	table.ACL tr td, table.ACL tr td:first-child{
		padding-top:0px;
		padding-bottom:0px;
	}
	table.ACL tr.level-0 td{
		padding-top:10px;
	}
	table.ACL tr.level-1 td{
		font-size:10pt;
	}
	table.ACL tr th{
		font-weight:normal;
		border-bottom:1px solid #808080;
	}
	table.ACL tr th, table.ACL tr td{
		text-align:center;
	}
	table.ACL tr th:first-child, table.ACL tr td:first-child{
		text-align:left;
	}
</style>
