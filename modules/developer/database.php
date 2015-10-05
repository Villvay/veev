<link rel="stylesheet" type="text/css" href="<?php echo BASE_URL_STATIC; ?>css/datatable.css" />
<h2>Database</h2>

<form method="post">
<input type="submit" data-href="<?php echo BASE_URL; ?>developer/database/import" value="Import Schema" />
<a href="<?php echo BASE_URL; ?>developer/database/export" class="button">Export Schema</a>

<small><p>After updating the project, any database change will be listed here, you can import database changes to your localhost from that file.<br/>After changing Database schema on localhost, <i>Export Schema</i>; it will be written to a file. Commit it to your repository/VCS.</p></small>

<?php /* table width="100%">
	<tr>
		<td width="100%" valign="top">
			Localhost
<pre>
<?php print_r($tables); ?>
</pre>
		</td>
		<td width="100%" valign="top">
			Schema File
<pre>
<?php print_r($import); ?>
</pre>
		</td>
	</tr>
</table */ ?>

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
		$sql = isset($import[$table]) ? 'ALTER TABLE '.$table : ($direction == 'new' ? _create_query($table, $tblSchema) : _drop_query($table));
		/*/echo isset($import[$table]) ? '' : '<input type="hidden" name="sql[]" value="'.($direction == 'new' ? _create_query($table, $tblSchema) : _drop_query($table)).'" />';
<?php /* input type="hidden" name="sql[]" value="<?php echo isset($import[$table]) ? '' : ''.($direction == 'new' ? _create_query($table, $tblSchema) : _drop_query($table)).''; ?>" / */ ?>
<section class="<?php echo isset($import[$table]) ? 'exist' : $direction; ?>">
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
<?php 	foreach ($tblSchema as $field => $row){
			render_rw($field, $row, $import, $table, $schema, $direction, $sql);
			if (isset($import[$table]) && !isset($import[$table][$field])){
				$sql .= "\n".'  ADD `'.$field.'` '.$row['Type'].($row['Size'] != '' ? '('.$row['Size'].')' : '').($row['Null'] == 'NO' ? ' NOT NULL' : '').
					($row['Extra'] == 'auto_increment' ? ' AUTO_INCREMENT' : '').($row['Null'] == 'YES' ? (' DEFAULT '.($row['Default'] == '' ? 'NULL' : '\''.$row['Default'].'\'')) : '');
			}
		}
		if (isset($import[$table])){
			foreach ($import[$table] as $col => $meta){
				if (!isset($tblSchema[$col])){
					render_rw($col, $import[$table][$col], array(), $table, $schema, 'old', $sql);
					$sql .= "\n".'  DROP COLUMN '.$col;
				}
			}
		} ?>
		</tbody>
	</table>
	<input type="hidden" name="sql[]" value="<?php echo $sql; ?>" />
	<textarea><?php echo $sql; ?></textarea>
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
				$sql .= "\n".'  MODIFY `'.$field.'`';
		} ?>
			<tr class="<?php echo $exist ? 'exist' : $direction; ?>">
				<td><?php echo $field; ?></td>
<?php 		foreach ($schema as $col => $meta){
				$changedIn = false;
				if ($changed){
					$changedIn = $row[$col] != $import[$table][$field][$col];
					if ($changedIn){
						if ($col == 'Type')
							$sql .= ' '.$row[$col];
						else if ($col == 'Size')
							$sql .= '('.$row[$col].')';
						else if ($col == 'Null')
							$sql .= $row['Null'] == 'NO' ? ' NOT NULL' : ' NULL';
						else if ($col == 'Extra' && $row['Extra'] == 'auto_increment')
							$sql .= ' AUTO_INCREMENT';
						else if ($col == 'Default')
							$sql .= ' DEFAULT '.($row['Default'] == '' ? 'NULL' : '\''.$row['Default'].'\'');
					}
				} ?>
				<td class="<?php echo $changedIn ? 'changed' : ''; ?>"><?php echo $changedIn ? $import[$table][$field][$col].' =&gt; ' : ''; ?><?php echo $row[$col]; ?></td>
<?php 		} ?>
			</tr>
<?php } ?>

<style>
	h3{
		margin:0px 0px 6px 0px;
	}
	section{
		padding:5px;
		margin:-6px;
		border-radius:5px;
	}
	textarea{
		font-family:monospace;
		margin-top:6px;
	}
	/* ----- */
	section.exist{
		border:1px solid #FFFF88;
		background-color:#FFFFCC;
	}
	section.exist textarea{
		background-color:#FFFFEE;
		color:#666600;
	}
	section.new{
		border:1px solid #88FF88;
		background-color:#CCFFCC;
	}
	section.new textarea{
		background-color:#EEFFEE;
		color:#006600;
	}
	section.old{
		border:1px solid #FF8888;
		background-color:#FFCCCC;
	}
	section.old textarea{
		background-color:#FFEEEE;
		color:#660000;
	}
	/* ----- */
	section.new tr td, section.exist tr.new td{
		background-color:#CCF4CC;
	}
	section.new tr:nth-child(odd) td, section.exist tr.new:nth-child(odd) td{
		background-color:#CFEBCF;
	}
	/* ----- */
	section.old tr td, section.exist tr.old td{
		background-color:#F4CCCC;
	}
	section.old tr:nth-child(odd) td, section.exist tr.old:nth-child(odd) td{
		background-color:#EBCFCF;
	}
	/* ----- */
	section.exist tr td.changed{
		background-color:#F4F4CC;
	}
	section.exist tr:nth-child(odd) td.changed{
		background-color:#EBEBCF;
	}
	/* ----- */
	table.table-striped tr:hover td {
		background-color: #FFFFD0 !important;
	}
</style>