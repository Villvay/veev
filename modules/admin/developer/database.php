<link rel="stylesheet" type="text/css" href="<?php echo BASE_URL_STATIC; ?>css/datatable.css" />
<link rel="stylesheet" type="text/css" href="<?php echo BASE_URL_STATIC; ?>css/admin-database.css" />
<h2>Database</h2>

<p>This allows you synchronize database schema changes between two or more deployments of a Veev app.</p>

<form method="post">
	<a href="<?php echo BASE_URL; ?>admin/developer/database/export" class="button">Export Schema (to file) <i class="fa fa-database"></i> <i class="fa fa-arrow-right"></i> <i class="fa fa-file"></i></a>
	<input type="submit" data-href="<?php echo BASE_URL; ?>admin/developer/database/import" value="Import Schema (to database)" />

<small><p>After changing Database schema on localhost, <i>Export Schema</i>; it will be written to a file. Commit it to your repository/VCS.<br/>
After updating the project (with your favourite VCS), any database change will be listed here,<br/>
&nbsp; &nbsp; &nbsp; you can import database changes to your localhost database from that file.</p></small>

<?php
	foreach ($import as $table => $tblSchema){
		render_tbl($table, $tblSchema, $schema, $tables, 'new');
	}
	foreach ($tables as $table => $tblSchema){
		if (!isset($import[$table]))
			render_tbl($table, $tblSchema, $schema, $import, 'old');
	}
?>
</form>

<?php function render_tbl($table, $tblSchema, $schema, $import, $direction = 'new'){
		$sql = isset($import[$table]) ? '' : ($direction == 'new' ? _create_query($table, $tblSchema) : _drop_query($table)); ?>
<section class="<?php echo isset($import[$table]) ? 'exist'.(serialize($import[$table]) == serialize($tblSchema) ? ' same collapse' : '') : $direction; ?>">
	<a class="toggle"></a>
	<h3><?php echo $table; ?></h3>
	<table class="table table-striped tbl-<?php echo $table; ?>" width="100%">
		<thead>
			<tr>
				<th>Field</th>
<?php 	foreach ($schema as $col => $meta){ ?>
				<th><?php echo $meta[0]; ?></th>
<?php 	} ?>
			</tr>
		</thead>
		<tbody>
<?php 	$changedAny = false;
		foreach ($tblSchema as $field => $row){
			render_rw($field, $row, $import, $table, $schema, $direction, $sql);
			if (isset($import[$table]) && !isset($import[$table][$field])){
				$sql .= ($sql != '' ? ',' : '')."\n".'  ADD `'.$field.'` '.$row['Type'].($row['Size'] != '' ? '('.$row['Size'].')' : '').($row['Null'] == 'NO' ? ' NOT NULL' : '').//($row['Default'] == '' ? '' : 'DEFAULT \''.$row['Default'].'\'').
					($row['Extra'] == 'auto_increment' ? ' AUTO_INCREMENT' : '').($row['Null'] == 'YES' ? (' DEFAULT '.($row['Default'] == '' ? 'NULL' : '\''.$row['Default'].'\'')) : '');
				$changedAny = true;
			}
		}
		if (isset($import[$table])){
			foreach ($import[$table] as $col => $meta){
				if (!isset($tblSchema[$col])){
					render_rw($col, $import[$table][$col], array(), $table, $schema, 'old', $sql);
					$sql .= ($sql != '' ? ',' : '')."\n".'  DROP COLUMN '.$col;
				}
			}
			$changedAny = true;
		}
		if ($changedAny && $sql != '')
			$sql = 'ALTER TABLE '.$table.$sql.';'; ?>
		</tbody>
	</table>
	<textarea name="sql[]"><?php echo $sql; ?></textarea>
</section>
<br /><br />
<?php } ?>

<?php function render_rw($field, $row, $import, $table, $schema, $direction, &$sql){
		$exist = false;
		$changed = false;
		if (isset($import[$table]) && isset($import[$table][$field])){
			$exist = true;
			$changed = serialize($row) != serialize($import[$table][$field]);
			if ($changed)
				$sql .= ($sql != '' ? ',' : '')."\n".'  MODIFY `'.$field.'`';
		} ?>
			<tr class="<?php echo $exist ? 'exist' : $direction; ?>">
				<td><?php echo $field; ?></td>
<?php 		foreach ($schema as $col => $meta){
				$changedIn = false;
				if ($changed){
					$changedIn = $row[$col] != $import[$table][$field][$col];
				} ?>
				<td class="<?php echo $changedIn ? 'changed' : ''; ?>"><?php echo $changedIn ? $import[$table][$field][$col].' =&gt; ' : ''; ?><?php echo $row[$col]; ?></td>
<?php 		} ?>
			</tr>
<?php
	} ?>

<script src="<?php echo BASE_URL_STATIC; ?>js/admin-database.js"></script>