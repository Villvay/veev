<link rel="stylesheet" type="text/css" href="<?php echo BASE_URL_STATIC; ?>css/datatable.css" />
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
//echo $col .':'. $row[$col] .':'. $import[$table][$field][$col];
					$changedIn = $row[$col] != $import[$table][$field][$col];
				} ?>
				<td class="<?php echo $changedIn ? 'changed' : ''; ?>"><?php echo $changedIn ? $import[$table][$field][$col].' =&gt; ' : ''; ?><?php echo $row[$col]; ?></td>
<?php 		} ?>
			</tr>
<?php
	} ?>

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
		max-width:100%;
		width:100%;
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

<script>
var sections = document.querySelectorAll('section');
for (var i = 0; i < sections.length; i++){
	//sections[i].style.height = sections[i].offsetHeight+'px';
	sections[i].setAttribute('data-height', sections[i].offsetHeight);
}
var toggles = document.querySelectorAll('section a.toggle');
var togglingElem, expandedHeight;
for (var i = 0; i < toggles.length; i++)
	toggles[i].onclick = function(){
		if (this.parentNode.className.indexOf('collapse') > 0){
			togglingElem = this.parentNode;
			togglingElem.style.transition = '';
			togglingElem.style.height = 'auto';
			togglingElem.className = togglingElem.className.replace(' collapse', '');
			expandedHeight = togglingElem.offsetHeight-12;
			togglingElem.style.height = '28px';
			setTimeout(
				function(){
					togglingElem.style.height = expandedHeight + 'px';
					togglingElem.setAttribute('data-height', expandedHeight);
					togglingElem.style.transition = 'height 1s';
				}, 50
			);
			setTimeout(
				function(){
					togglingElem.style.height = 'auto';
				}, 1500
			);
		}
		else{
			togglingElem = this.parentNode;
			togglingElem.style.height = togglingElem.getAttribute('data-height') + 'px';
			togglingElem.style.transition = 'height 1s';
			setTimeout(
				function(){
					togglingElem.style.height = '28px';
					togglingElem.className = togglingElem.className + ' collapse';
				}, 50
			);
		}
	}
</script>
<style>
	section{
		height:auto;
		overflow:hidden;
		xtransition:height 1s;
	}
	section.collapse{
		height:28px !important;
	}
	section a.toggle{
		background-image:url('<?php echo BASE_URL_STATIC; ?>icons/button_blue_delete.png');
		display:block;
		float:right;
		height:48px;
		width:48px;
		margin:-7px;
		cursor:pointer;
	}
	section.collapse a.toggle{
		background-image:url('<?php echo BASE_URL_STATIC; ?>icons/button_blue_add.png');
	}
</style>