<?php

	function render_view($view, $data){
		global $user, $module, $method, $lex, $lang;
		// Turn $data array to variables
		if (isset($data))
			foreach ($data as $key => $val)
				$$key = $val;
		// Render view to the output_buffer
		ob_start();
			if (file_exists($view))
				include $view;
			else
				require_once 'templates/error_404.php';
			$yield = ob_get_contents();
		ob_end_clean();
		return $yield;
	}

	function render_template($template, $yield, $html_head = false){
		global $user, $module, $method, $lex, $lang; //, $html_head;
		// Render the view inside the template to the output_buffer
		if ($html_head == false){
			$html_head = array('title' => ucfirst($module).' '.ucfirst(str_replace('_', ' ', $method)));
		}
		ob_start();
			if (file_exists('templates/'.$template)){
				include 'templates/'.$template;
				$yield = ob_get_contents();
			}
			else{
				require_once 'templates/error_404.php';
			}
		ob_end_clean();
		//
		return $yield;
	}

	// ========================================================

	function render_data_view($schema, $data, $edit_action = false){
		global $acl;
		echo '<br/>';
		foreach ($schema as $key => $field){
			if (isset($field['key']) && $field['key'] || (isset($field['display']) && $field['display'] == 'hidden')){
			}
			else if (!isset($field['form']) || $field['form']){ ?>
		<label for="<?php echo $key; ?>"><?php echo $field[0]; ?></label>
		<div class="row">
			<div class="col-xs-6 col-md-4">
				<b>
<?php 			if (isset($field['enum'])){
					echo isset($field['enum'][$data[$key]]) ? (is_array($field['enum'][$data[$key]]) ? $field['enum'][$data[$key]][0] : $field['enum'][$data[$key]]) : '-';
				}
				else if (isset($field['display'])){
					if ($field['display'] == 'calendar'){
						echo beautify_datetime($data[$key]);
					}
					if ($field['display'] == 'calendar+clock'){
						echo beautify_datetime($data[$key]);
					}
					else if ($field['display'] == 'password'){
						echo '[ ** Encrypted ** ]';
					}
					else if ($field['display'] == 'folder'){
						$path = explode('}', $field['path']);
						foreach ($path as &$segment)
							if ($segment != ''){
								$segment = explode('{', $segment);
								$segment = $segment[0].$data[$segment[1]];
							}
						$path = implode($path); ?>
				<iframe src="<?php echo BASE_URL; ?>dashboard/folder/view/<?php echo $path; ?>" class="form-control"></iframe>
<?php 				}
					else if ($field['display'] == 'textarea' || $field['display'] == 'richtext'){
						echo $data[$key];
					}
				}
				else{
					echo $data[$key];
				} ?>
				</b><hr/>
			</div>
		</div>
<?php 		}
		}
		if (isset($acl['edit']) && $edit_action != false){ ?>
		<input class="btn" type="button" value="Edit" onclick="window.location='<?php echo BASE_URL.$edit_action; ?>';" />
<?php 	} ?>
		<input class="btn" type="button" value="Back" onclick="window.history.back();" />
<?php }

	// ========================================================

	function render_form($schema, $data, $action, $onsubmit = false, $extra = false){
		$file_upload = false;
		$calendar = false;
		$richtext = false;
		$autofill = false;
		foreach ($schema as $col => $meta)
			if (isset($meta['display'])){
				if ($meta['display'] == 'calendar' || $meta['display'] == 'calendar+clock')
					$calendar = true;
				else if ($meta['display'] == 'file')
					$file_upload = true;
				else if ($meta['display'] == 'richtext')
					$richtext = true;
			}
			else if (isset($meta['autofill']))
				$autofill = true;
		if ($richtext){ ?>
	<script src="<?php echo BASE_URL_STATIC; ?>tinymce/tinymce.min.js"></script>
	<style>.mce-tinymce.mce-container.mce-panel{width:609px; border:0 solid #DDDDDD;} .mce-tinymce.mce-container.mce-panel{width:99.5%;}</style>
<?php 	}
		if ($autofill){ ?>
	<script src="<?php echo BASE_URL_STATIC; ?>js/select2filter.js"></script>
	<link rel="stylesheet" type="text/css" href="<?php echo BASE_URL_STATIC; ?>css/select2filter.css" />
<?php 	} ?>
	<form method="post" class="data-form" action="<?php echo BASE_URL.$action; ?>"<?php echo $file_upload ? ' enctype="multipart/form-data"' : ''; ?> <?php echo $onsubmit == false ? 'onsubmit="return validate(this);"' : $onsubmit; ?> autocomplete="off">
<?php 	foreach ($schema as $key => $field){
			if (isset($field['key']) && $field['key'] || (isset($field['display']) && $field['display'] == 'hidden')){ ?>
		<input type="hidden" name="<?php echo $key; ?>" value="<?php echo $data[$key]; ?>" />
<?php 		}
			else if (!isset($field['form']) || $field['form']){ ?>
		<div class="form-group row<?php echo isset($field['form-width']) ? ' width-'.$field['form-width'] : ''; ?>">
			<div class="col-xs-6 col-md-4<?php echo isset($field['required']) && $field['required'] ? ' required' : ''; ?><?php echo isset($field['display']) && $field['display'] == 'currency' ? ' currency' : ''; ?>">
				<label for="<?php echo $key; ?>"><?php echo $field[0]; ?></label>
<?php 			if (isset($field['enum']) /*&& is_array($field['enum'])*/){
					render_dropdown($key, $field['enum'], $data[$key]);
				}
				else if (isset($field['autofill'])){
					render_dropdown($key, $field['autofill'], $data[$key]);
					echo '<script>new select2filter(document.forms[0].'.$key.');</script>';
				}
				else if (isset($field['display'])){
					if ($field['display'] == 'calendar'){ ?>
				<input type="text" class="form-control" name="<?php echo $key; ?>" value="<?php echo substr($data[$key], 0, 10); ?>" data-validate="date" onfocus="ShowCalendar(this);" readonly="true" />
<?php 				}
					if ($field['display'] == 'calendar+clock'){ ?>
				<input type="text" class="form-control" name="<?php echo $key; ?>" value="<?php echo substr($data[$key], 0, 10); ?>" data-validate="date" onfocus="ShowCalendar(this, 'clock');" readonly="true" />
<?php 				}
					else if ($field['display'] == 'password'){ ?>
				<input type="password" class="form-control" name="<?php echo $key; ?>" value="<?php echo $data[$key]; ?>" />
<?php 				}
					else if ($field['display'] == 'textarea' || $field['display'] == 'richtext'){ ?>
				<textarea name="<?php echo $key; ?>" rows="1" class="form-control<?php echo $field['display'] == 'richtext' ? ' richtext' : ''; ?>"><?php echo $data[$key]; ?></textarea>
<?php 				}
					else if ($field['display'] == 'email'){ ?>
				<input type="email" class="form-control" name="<?php echo $key; ?>" value="<?php echo $data[$key]; ?>" />
<?php 				}
					else if ($field['display'] == 'currency'){ ?>
				<span>$</span>
				<input type="text" class="form-control" name="<?php echo $key; ?>" value="<?php echo $data[$key]; ?>" data-validate="currency" />
<?php 				}
					else if ($field['display'] == 'numeric'){ ?>
				<input type="text" class="form-control" name="<?php echo $key; ?>" value="<?php echo $data[$key]; ?>" data-validate="numeric" />
<?php 				}
					else if ($field['display'] == 'check' || $field['display'] == 'checkbox'){ ?>
				<input type="checkbox" class="form-control" name="<?php echo $key; ?>"<?php echo $data[$key] ? ' checked' : ''; ?> />
<?php 				}
					else if ($field['display'] == 'readonly'){ ?>
				<input type="text" class="form-control" name="<?php echo $key; ?>" value="<?php echo $data[$key]; ?>" readonly="true" />
<?php 				}
					else if ($field['display'] == 'file'){ ?>
				<input type="file" class="form-control" name="<?php echo $key; ?>" />
				<br/><br/><img src="<?php echo BASE_URL_STATIC.$data[$key].'-thumb.jpg'; ?>" width="240" />
<?php 				}
					else if ($field['display'] == 'folder'){
						$path = explode('}', $field['path']);
						foreach ($path as &$segment)
							if ($segment != ''){
								$segment = explode('{', $segment);
								$segment = $segment[0].$data[$segment[1]];
							}
						$path = implode($path); ?>
				<iframe src="<?php echo BASE_URL; ?><?php echo $path; ?>" class="form-control"></iframe>
<?php 				}
				}
				else{ ?>
				<input type="text" class="form-control" name="<?php echo $key; ?>" value="<?php echo $data[$key]; ?>" />
<?php 			} ?>
			</div>
		</div>
<?php 		}
		}
		if ($extra)
			$extra($schema, $data); ?>
		<div class="row">
			<input class="btn" type="submit" value="Save" />
			<input class="btn" type="button" value="Cancel" onclick="window.history.back();" />
		</div>
	</form>
<?php 	if ($richtext){ ?>
<script>
tinymce.init({
	selector: "textarea.richtext", plugins: ["link image code fullscreen textcolor"], convert_urls: false,
	toolbar: "bold italic | forecolor backcolor | fontsizeselect | alignleft aligncenter alignright | bullist numlist"
});
</script>
<?php 	}
		if ($calendar)
			render_calendar();
	}

	// ========================================================

	$cal_rendered = false;
	function render_calendar(){
		global $cal_rendered, $from_year, $to_year;
		if ($cal_rendered)
			return false;
		$cal_rendered = true; ?>
<script src="<?php echo BASE_URL_STATIC; ?>js/calendar.js"></script>
<script src="<?php echo BASE_URL_STATIC; ?>js/js_vlib.js"></script>
<link rel="stylesheet" type="text/css" href="<?php echo BASE_URL_STATIC; ?>css/calendar.css" /><?php
		include 'interfaces/calendar.php';
	}

	// ========================================================

	function render_table($schema, $data, $classname = false){
		$found = false; ?>
	<table width="100%" class="table table-striped<?php echo $classname != false ? ' '.$classname : ''; ?>"><thead><tr>
<?php 	$cmd_opened = false;
		foreach ($schema as $col => $meta){
			if (!isset($meta['table']) || $meta['table']){
				if (isset($meta['cmd']) || isset($meta['onclick'])){
					if (!$cmd_opened){
						echo '<th width="120" class="action_btns">Actions';
						$cmd_opened = true;
					}
				}
				else{
				?><th><?php echo $meta[0]; ?></th><?php
				}
			}
		}
		if ($cmd_opened)
			echo '</th>';
		?></tr></thead><tbody><?php
		$key = false;
		if (is_array($data))
			foreach ($data as $row)
				render_row($row, $schema, $found);
		else
			while ($row = row_assoc($data))
				render_row($row, $schema, $found);
		if (!$found){
			?><tr class="no-records"><td colspan="99"><i>No records to display</i></td></tr><?php
		} ?></tbody></table><?php
	}
	function render_row($row, $schema, &$found){
			foreach ($schema as $col => $meta){
				if (isset($meta['key']) && $meta['key'])
					$key = $row[$col];
			} ?><tr onclick="return row_click(this);"<?php echo isset($key) ? 'data-key="'.$key.'"' : ''; ?>><?php
			$cmd_opened = false;
			foreach ($schema as $col => $meta){
				if (isset($meta['link'])){
?><td><a class="<?php echo $col; ?><?php echo isset($meta['default']) ? ' default' : ''; ?>" href=<?php echo '"' . BASE_URL.str_replace('{key}', $key, $meta['link']) . '"'; ?>><?php echo $row[$col]; ?></a></td><?php
				}
				else if (isset($meta['cmd']) || isset($meta['onclick'])){
					if (!$cmd_opened){
						echo '<td class="action_btns">';
						$cmd_opened = true;
					}
					?><a class="button <?= $col; ?><?= isset($meta['default']) ? ' default' : ''; ?>" href=<?php
					if (isset($meta['cmd']))
						echo '"' . BASE_URL.str_replace('{key}', $key, $meta['cmd']) . '"' . (isset($meta['confirm']) ? ' onclick="if (confirm(\'Are you sure.?\')){return true;}else{event.stopPropagation(); return false;}"' : '');	//	' onclick="if (confirm(\'Are you sure.?\')){return true;}else{event.stopPropagation(); return false;}"'
					else if (isset($meta['onclick']))
						echo '"javascript:void(0);" onclick="'.str_replace('{key}', $key, $meta['onclick']) . '"';
					?>><?php echo $meta[0]; ?></a><?php
				}
				else if (!isset($meta['table']) || $meta['table']){
					echo '<td'.(isset($meta['display']) && ($meta['display'] == 'numeric' || $meta['display'] == 'currency') ? ' align="right"' : '').'>';
					if (isset($meta['table-display'])){
						if ($meta['table-display'] == 'enum')	//echo '<small>'.$row[$col].'</small>';
							render_dropdown($col.'['.$key.']', $meta['enum'], $row[$col], $col, (isset($meta['onchange']) ? $meta['onchange'] : false), true);
						else if ($meta['table-display'] == 'calendar')
							echo '<input type="text" class="form-control '.$col.'" name="'.$col.'['.$key.']" value="'.substr($row[$col], 0, 10).'" '.(isset($meta['onchange']) ? 'onchange="'.$meta['onchange'].'"' : '').'onfocus="return ShowCalendar(this);" readonly="true" />';
						else if ($meta['table-display'] == 'calendar+clock')
							echo '<input type="text" class="form-control '.$col.'" name="'.$col.'['.$key.']" value="'.$row[$col].'" onfocus="return ShowCalendar(this, \'clock\');" readonly="true" />';
						else if ($meta['table-display'] == 'small')
							echo '<small>'.$row[$col].'</small>';
						else
							echo $row[$col];
					}
					else if (isset($meta['function']))
						echo $meta['function']($row);
					else if (isset($meta['enum']))
						echo isset($meta['enum'][$row[$col]]) ? (is_array($meta['enum'][$row[$col]]) ? $meta['enum'][$row[$col]][0] : $meta['enum'][$row[$col]]) : '-';
					else if (isset($meta['autofill']))
						echo isset($meta['autofill'][$row[$col]]) ? (is_array($meta['autofill'][$row[$col]]) ? $meta['autofill'][$row[$col]][0] : $meta['autofill'][$row[$col]]) : '-';
					else if (isset($meta['display'])){
						if ($meta['display'] == 'calendar' || $meta['display'] == 'calendar+clock')
							echo beautify_datetime($row[$col]);
						else
							echo $row[$col];
					}
					else
						echo isset($row[$col]) ? $row[$col] : '-';
					echo '</td>';
				}
			}
			if ($cmd_opened)
				echo '</td>';
			?></tr><?php
			$found = true;
	}

	// ========================================================

	function render_dropdown($name, $data, $selected = false, $classname = false, $onchange = false, $ontblrow = false){ ?>
				<select name="<?php echo $name; ?>" onclick="dropdown_clicked(this);" class="form-control<?php echo $classname != false ? ' '.$classname : ''; ?>"<?php echo $onchange != false ? ' onchange="'.$onchange.'"' : ''; ?>>
					<option value="" class="first-child">Please Select</option>
<?php 				foreach ($data as $key => $val){ ?>
					<option value="<?php echo $key; ?>" <?php echo $selected == $key ? 'selected' : ''; ?>><?php echo is_array($val) ? $val[0] : $val; ?></option>
<?php 				} ?>
				</select>
<?php }

	// ========================================================

	function render_navigation($data, $classname = 'nav'){
		global $user, $module, $submodule, $method, $page; ?>
				<ul class="<?php echo $classname; ?>">
<?php 	foreach ($data as $link){
			//$acl = checkIfAuthorized($user, $link['module'], $submodule = false)
			$path = ( isset($link['module']) ? $link['module'] : '' ) . ( isset($link['module']) && isset($link['method']) ? '/' : '' ) . ( isset($link['method']) ? $link['method'] : '' );
			if (!isset($link['module']))
				$link['module'] = 'index';
			if (!isset($link['method']))
				$link['method'] = 'index'; ?>
					<li<?php echo ($module == $link['module'] || $module.'/'.$submodule == $link['module']) && ($method == $link['method'] || $page == $link['method']) ? ' class="current"' : ''; ?>>
<?php 		if (isset($link['submenu'])){ ?>
						<label>
							<?php echo isset($link['icon']) ? '<i class="fa '.$link['icon'].'"></i> ' : ''; ?><input type="checkbox" class="nav-chk" name="<?php echo str_replace('/', '-', $path); ?>"><?php echo $link['title']; ?>
						</label>
<?php 			render_navigation($link['submenu']);
			}
			else{ ?>
						<a href="<?php echo BASE_URL.$path; ?>"><?php echo isset($link['icon']) ? '<i class="fa '.$link['icon'].'"></i> ' : ''; ?><?php echo $link['title']; ?></a>
<?php 		} ?>
					</li>
<?php 	} ?>
				</ul>
<?php }

	// ========================================================

	function render_slider($path){
		$files_path = STATIC_FILES_ROOT.$path.'/';
		$slides = array();
		if (is_dir($files_path) && $dh = opendir($files_path)){
			while (($file = readdir($dh)) !== false){
				if ($file == '.' || $file == '..' || is_dir($files_path.$file)){//$files_path.'/'.$file
				}
				else if (substr($file, 0, 6) == 'thumb_'){
					$slides[] = substr($file, 6);
				}
			}
			closedir($dh);
		}
		if (count($slides) > 0){ ?>
<script src="<?php echo BASE_URL_STATIC; ?>js/slides.js"></script>
<link rel="stylesheet" type="text/css" href="<?php echo BASE_URL_STATIC; ?>css/slides.css" />
<ul class="slides">
<?php 		foreach ($slides as $slide){ ?>
	<li><img src="<?php echo BASE_URL_STATIC; ?><?php echo $path; ?>/<?php echo $slide; ?>" /></li>
<?php 		} ?>
</ul>
<script>new slideShow(document.querySelectorAll('ul.slides')[0]);</script>
<?php 	}
	}

	function list_languages(){
		$files_path = './data/lang/';
		$indexCreatedOn = @filemtime($files_path.'index.db');
		$index = @unserialize(gzinflate(file_get_contents($files_path.'index.db')));
		if (!$index)
			$index = array();
		$langs = array();
		$indexChanged = false;
		$dh = opendir($files_path);
		while (($file = readdir($dh)) !== false){
			if (substr($file, -5) == '.json'){
				$langs[] = substr($file, 0, -5);
				if (filemtime($files_path.$file) > $indexCreatedOn){
					$lang = json_decode(substr(file_get_contents($files_path.$file), 3), true);
					$index[substr($file, 0, -5)] = array($lang['language'], 'title' => $lang['language'], 'flag' => $lang['flag']);
					$indexChanged = true;
				}
			}
		}
		closedir($dh);
		foreach ($index as $key => $val)
			if (!in_array($key, $langs))
				unset($index[$key]);
		if ($indexChanged)
			file_put_contents($files_path.'index.db', gzdeflate(serialize($index)));
		return $index;
	}

	// ========================================================

	function flash_message($message, $level, $fadeout = false){
		if (!isset($_SESSION['flash_messages']))
			$_SESSION['flash_messages'] = array();
		$_SESSION['flash_messages'][] = array($level, $message, $fadeout);
	}

	function flash_message_dump(){
		if (!isset($_SESSION['flash_messages']) || count($_SESSION['flash_messages']) == 0)
			return true;
		//
		echo '<div id="flash_messages">';
		foreach ($_SESSION['flash_messages'] as $flash_message){
			$div_id = rand(20, 34956344).'_'.time();
			echo '<div class="'.$flash_message[0].'" id="flash_message_'.$div_id.'"><div class="icon"></div>'.$flash_message[1].
				'<a class="dismiss" href="javascript:popup_bring_down(\'flash_message_'.$div_id.'\', 100);"></a></div>';
			if ($flash_message[2])
				echo '<script>setTimeout("popup_bring_down(\'flash_message_'.$div_id.'\', 100);", 1600);</script>';
		}
		echo '</div>';
		//
		$_SESSION['flash_messages'] = array();
	}

	function shorten_string($string, $len, $content_id = false, $skip = 0){
		$string = str_replace("\t", '', $string);
		$string = str_replace(array("\n", "\r", '&nbsp;', '  '), ' ', $string);
		$string = preg_replace('#<script(.*?)</script>#s', '', $string);
		$string = strip_tags($string);
		//$string = str_replace(array('&', '<', '>', '"'), array('&amp;', '&lt;', '&gt;', '&quot;'), $string);
		//
		$len += $skip;
		if (strlen($string) < $len)
			return substr($string,  $skip);
		$tmp = strpos($string, ' ', $len);
		if ($tmp === false)
			return substr($string,  $skip);
		$shorten_count = uniqid();
		if ($content_id == false)
			return substr($string, $skip, $tmp - $skip);
		else
			return substr($string, $skip, $tmp - $skip).
				' <a href="'.BASE_URL.'content/view/'.$content_id.'" '.
					'onclick="show_more_text(\''.$shorten_count.'\', \''.$content_id.':x\', '.$tmp.'); return false;" '.
					'id="shorten_more_link_'.$shorten_count.'">...more</a>'.
				'<span style="display:none;" id="shorten_more_'.$shorten_count.'">'.'</span>';
	}

	function beautify_datetime($datetime){
		global $lang;
		$time = is_numeric($datetime) ? $datetime : strtotime($datetime);
		if ($time == 0)
			return '-';
		return date('Y-M-d', $time);
		$now = time();
		$diff = $now - $time;
		if ($time == 0)
			return '-';
		if ($diff < 86400){
			if ($diff < 0)
				return _beautify_datetime_future($datetime);
			else if ($diff < 5)
				return 'Just now';
			else if ($diff < 60)
				return $diff.'Seconds ago';
			else{
				$diff = floor($diff / 60);
				if ($diff < 60)
					return $diff.'Minutes ago';
				else if (date('j', $now) == date('j', $time))
					return date('g:i', $time).date('a', $time);
				else
					return 'Yesterday'.' '.date('g', $time).' '.date('a', $time);
			}
		}
		else
			if (date('Y', $now) == date('Y', $time))
				if (date('n', $now) == date('n', $time))
					if (date('W', $now) == date('W', $time))
						return date('l', $time).' '.date('g', $time).' '.date('a', $time);
					else
						return date('j', $time).date('S', $time).' '.date('M', $time).' '.date('g', $time).' '.date('a', $time);
				else
					return date('j', $time).date('S', $time).' '.date('M', $time);
			else
				return date('M', $time).' '.date('Y', $time);
	}
	/*function _beautify_datetime_future($datetime){
		$time = strtotime($datetime);
		$now = time();
		$diff = $time - $now;
		if ($diff < 86400){
			if ($diff < 5)
				return 'Just now';
			else if ($diff < 60)
				return 'in '.$diff.' seconds';
			else{
				$diff = floor($diff / 60);
				if ($diff < 60)
					return 'in '.$diff.' minutes';
				else if (date("j", $now) == date("j", $time))
					return date("g:i a", $time);
				else
					return "Tomorrow ".date("g a", $time);
			}
		}
		else
			if (date("Y", $now) == date("Y", $time))
				if (date("n", $now) == date("n", $time))
					if (date("W", $now) == date("W", $time))
						return date("l g a", $time);
					else
						return date("jS M g a", $time);
				else
					return date("jS M", $time);
			else
				return date("M Y", $time);
	}*/

	function slugify($text){
		$text = preg_replace('/[\/_|+ -.]+/', '-', $text);
		$text = trim($text, '-');
		$text = strtolower($text);
		return $text;
	}

?>