<?php

	if (!isset($_SESSION['user']) && $_SESSION['user']['level'] < 5 && $method != 'log_in')
		redirect('user', 'log_in');

	$template_file = 'admin.php';

	function index($params){
		$data = array();
		//
		$data['html_head'] = array('title' => 'Admin Dashboard');
		return $data;
	}

	// --------------------------------------------------------------------

	$pages_index = array('home' => 'Home Page', 'about' => 'About Us', 'contact' => 'Contact Us');
	$pages_schema = array(
						'stub' 	=> array('Title', 		'key' => true),
						'en' 		=> array('English', 	'display' => 'richtext', 'table' => false),
						'ch' 		=> array('Chinese', 	'display' => 'richtext', 'table' => false),
						'slides' 	=> array('Slides', 	'display' => 'folder', 'path' => 'user/images/uploads/{stub}', 'table' => false),
						'edit' 		=> array('Edit', 		'form' => false, 'cmd' => 'admin/pages/{key}', 'default' => true),
						'view' 	=> array('View', 		'form' => false, 'cmd' => '{key}')
					);
	function pages($params){
		global $pages_schema;
		$data = array('schema' => $pages_schema);
		$db = connect_database();
		//
		if (isset($params[0])){
			$data['page'] = $params[0];
			//
			$found = false;
			$content = $db->query('SELECT stub, en, ch FROM content WHERE stub = \''.$params[0].'\'');
			if ($content = mysql_fetch_assoc($content)){
				$data['content'] = $content;
				$found = true;
			}
			//
			if (isset($params['en'])){
				if ($found)
					$db->update('content', array('ch' => $params['ch'], 'en' => $params['en']), 'stub = \''.$params['stub'].'\'');
				else
					$db->insert('content', $params);
				flash_message('Content is saved', 'success');
				redirect('admin', 'pages');
			}
		}
		else
			$data['pages'] = $db->query('SELECT stub FROM content');
		//
		$data['html_head'] = array('title' => 'Pages: Admin Dashboard');
		return $data;
	}

	// --------------------------------------------------------------------

	function inquiry($params){
		$data = array();
		//
		//
		$data['html_head'] = array('title' => 'Inquiry: Admin Dashboard');
		return $data;
	}

	// --------------------------------------------------------------------

	$user_levels = array(1 => 'Basic User', 2 => 'Super User', 3 => 'Manager', 5 => 'Admin', 8 => 'Super Admin');
	$users_schema = array(
						'id' 		=> array('ID', 		'table' => false, 'key' => true),
						'username' 	=> array('Username', 	'form-width' => '50'),
						'password' 	=> array('Password', 	'table' => false, 'display' => 'password', 'form-width' => '50'),
						'email' 	=> array('Email'),
						'level' 	=> array('Level', 		'enum' => $user_levels),
						'timezone' 	=> array('Time Zone'),
						'edit' 		=> array('Edit', 		'form' => false, 'cmd' => 'admin/edit-user/{key}', 'default' => true)
					);

	function users($params){
		global $users_schema, $user;
		$data = array('schema' => $users_schema);
		$db = connect_database();
		//
		$data['users'] = $db->query('SELECT * FROM `user` WHERE cid = '.$user['cid']);
		//
		$data['html_head'] = array('title' => 'User Accounts');
		return $data;
	}

	function edit_user($params){
		global $users_schema, $user, $timezones;
		//$timezones = timezone_identifiers_list();
		//$timezones = array_combine($timezones, $timezones);
		$users_schema['timezone']['enum'] = $timezones;
		$data = array('schema' => $users_schema);
		$db = connect_database();
		//
		$data['user'] = mysql_fetch_assoc($db->query('SELECT * FROM `user` WHERE cid = '.$user['cid'].' AND id = '.$params[0]));
		$data['user']['password'] = '[encrypted]';
		//
		$data['html_head'] = array('title' => 'Edit User Account');
		return $data;
	}

	function add_user($params){
		global $users_schema, $user, $timezones;
		$users_schema['timezone']['enum'] = $timezones;
		$data = array('schema' => $users_schema);
		//
		$data['user'] = array('id' => 'new', 'username' => '', 'password' => '', 'email' => '', 'level' => '1', 'timezone' => $user['timezone']);
		//
		$data['html_head'] = array('title' => 'Add User Account');
		return $data;
	}

	function save_user($params){
		global $user;
		$db = connect_database();
		if ($params['password'] != '[encrypted]')
			$params['password'] = md5($params['password'].':NaCl');
		//
		if ($params['id'] == 'new'){
			unset($params['id']);
			$params['cid'] = $user['cid'];
			$db->insert('user', $params);
		}
		else
			$db->update('user', $params);
		redirect('admin', 'users');
	}

	// --------------------------------------------------------------------

$timezones = array(
    'Pacific/Midway'       => '(GMT-11:00) Midway Island',
    'US/Samoa'             => '(GMT-11:00) Samoa',
    'US/Hawaii'            => '(GMT-10:00) Hawaii',
    'US/Alaska'            => '(GMT-09:00) Alaska',
    'US/Pacific'           => '(GMT-08:00) Pacific Time (US &amp; Canada)',
    'America/Tijuana'      => '(GMT-08:00) Tijuana',
    'US/Arizona'           => '(GMT-07:00) Arizona',
    'US/Mountain'          => '(GMT-07:00) Mountain Time (US &amp; Canada)',
    'America/Chihuahua'    => '(GMT-07:00) Chihuahua',
    'America/Mazatlan'     => '(GMT-07:00) Mazatlan',
    'America/Mexico_City'  => '(GMT-06:00) Mexico City',
    'America/Monterrey'    => '(GMT-06:00) Monterrey',
    'Canada/Saskatchewan'  => '(GMT-06:00) Saskatchewan',
    'US/Central'           => '(GMT-06:00) Central Time (US &amp; Canada)',
    'US/Eastern'           => '(GMT-05:00) Eastern Time (US &amp; Canada)',
    'US/East-Indiana'      => '(GMT-05:00) Indiana (East)',
    'America/Bogota'       => '(GMT-05:00) Bogota',
    'America/Lima'         => '(GMT-05:00) Lima',
    'America/Caracas'      => '(GMT-04:30) Caracas',
    'Canada/Atlantic'      => '(GMT-04:00) Atlantic Time (Canada)',
    'America/La_Paz'       => '(GMT-04:00) La Paz',
    'America/Santiago'     => '(GMT-04:00) Santiago',
    'Canada/Newfoundland'  => '(GMT-03:30) Newfoundland',
    'America/Buenos_Aires' => '(GMT-03:00) Buenos Aires',
    'Greenland'            => '(GMT-03:00) Greenland',
    'Atlantic/Stanley'     => '(GMT-02:00) Stanley',
    'Atlantic/Azores'      => '(GMT-01:00) Azores',
    'Atlantic/Cape_Verde'  => '(GMT-01:00) Cape Verde Is.',
    'Africa/Casablanca'    => '(GMT) Casablanca',
    'Europe/Dublin'        => '(GMT) Dublin',
    'Europe/Lisbon'        => '(GMT) Lisbon',
    'Europe/London'        => '(GMT) London',
    'Africa/Monrovia'      => '(GMT) Monrovia',
    'Europe/Amsterdam'     => '(GMT+01:00) Amsterdam',
    'Europe/Belgrade'      => '(GMT+01:00) Belgrade',
    'Europe/Berlin'        => '(GMT+01:00) Berlin',
    'Europe/Bratislava'    => '(GMT+01:00) Bratislava',
    'Europe/Brussels'      => '(GMT+01:00) Brussels',
    'Europe/Budapest'      => '(GMT+01:00) Budapest',
    'Europe/Copenhagen'    => '(GMT+01:00) Copenhagen',
    'Europe/Ljubljana'     => '(GMT+01:00) Ljubljana',
    'Europe/Madrid'        => '(GMT+01:00) Madrid',
    'Europe/Paris'         => '(GMT+01:00) Paris',
    'Europe/Prague'        => '(GMT+01:00) Prague',
    'Europe/Rome'          => '(GMT+01:00) Rome',
    'Europe/Sarajevo'      => '(GMT+01:00) Sarajevo',
    'Europe/Skopje'        => '(GMT+01:00) Skopje',
    'Europe/Stockholm'     => '(GMT+01:00) Stockholm',
    'Europe/Vienna'        => '(GMT+01:00) Vienna',
    'Europe/Warsaw'        => '(GMT+01:00) Warsaw',
    'Europe/Zagreb'        => '(GMT+01:00) Zagreb',
    'Europe/Athens'        => '(GMT+02:00) Athens',
    'Europe/Bucharest'     => '(GMT+02:00) Bucharest',
    'Africa/Cairo'         => '(GMT+02:00) Cairo',
    'Africa/Harare'        => '(GMT+02:00) Harare',
    'Europe/Helsinki'      => '(GMT+02:00) Helsinki',
    'Europe/Istanbul'      => '(GMT+02:00) Istanbul',
    'Asia/Jerusalem'       => '(GMT+02:00) Jerusalem',
    'Europe/Kiev'          => '(GMT+02:00) Kyiv',
    'Europe/Minsk'         => '(GMT+02:00) Minsk',
    'Europe/Riga'          => '(GMT+02:00) Riga',
    'Europe/Sofia'         => '(GMT+02:00) Sofia',
    'Europe/Tallinn'       => '(GMT+02:00) Tallinn',
    'Europe/Vilnius'       => '(GMT+02:00) Vilnius',
    'Asia/Baghdad'         => '(GMT+03:00) Baghdad',
    'Asia/Kuwait'          => '(GMT+03:00) Kuwait',
    'Africa/Nairobi'       => '(GMT+03:00) Nairobi',
    'Asia/Riyadh'          => '(GMT+03:00) Riyadh',
    'Europe/Moscow'        => '(GMT+03:00) Moscow',
    'Asia/Tehran'          => '(GMT+03:30) Tehran',
    'Asia/Baku'            => '(GMT+04:00) Baku',
    'Europe/Volgograd'     => '(GMT+04:00) Volgograd',
    'Asia/Muscat'          => '(GMT+04:00) Muscat',
    'Asia/Tbilisi'         => '(GMT+04:00) Tbilisi',
    'Asia/Yerevan'         => '(GMT+04:00) Yerevan',
    'Asia/Kabul'           => '(GMT+04:30) Kabul',
    'Asia/Karachi'         => '(GMT+05:00) Karachi',
    'Asia/Tashkent'        => '(GMT+05:00) Tashkent',
    'Asia/Kolkata'         => '(GMT+05:30) Kolkata',
    'Asia/Kathmandu'       => '(GMT+05:45) Kathmandu',
    'Asia/Yekaterinburg'   => '(GMT+06:00) Ekaterinburg',
    'Asia/Almaty'          => '(GMT+06:00) Almaty',
    'Asia/Dhaka'           => '(GMT+06:00) Dhaka',
    'Asia/Novosibirsk'     => '(GMT+07:00) Novosibirsk',
    'Asia/Bangkok'         => '(GMT+07:00) Bangkok',
    'Asia/Jakarta'         => '(GMT+07:00) Jakarta',
    'Asia/Krasnoyarsk'     => '(GMT+08:00) Krasnoyarsk',
    'Asia/Chongqing'       => '(GMT+08:00) Chongqing',
    'Asia/Hong_Kong'       => '(GMT+08:00) Hong Kong',
    'Asia/Kuala_Lumpur'    => '(GMT+08:00) Kuala Lumpur',
    'Australia/Perth'      => '(GMT+08:00) Perth',
    'Asia/Singapore'       => '(GMT+08:00) Singapore',
    'Asia/Taipei'          => '(GMT+08:00) Taipei',
    'Asia/Ulaanbaatar'     => '(GMT+08:00) Ulaan Bataar',
    'Asia/Urumqi'          => '(GMT+08:00) Urumqi',
    'Asia/Irkutsk'         => '(GMT+09:00) Irkutsk',
    'Asia/Seoul'           => '(GMT+09:00) Seoul',
    'Asia/Tokyo'           => '(GMT+09:00) Tokyo',
    'Australia/Adelaide'   => '(GMT+09:30) Adelaide',
    'Australia/Darwin'     => '(GMT+09:30) Darwin',
    'Asia/Yakutsk'         => '(GMT+10:00) Yakutsk',
    'Australia/Brisbane'   => '(GMT+10:00) Brisbane',
    'Australia/Canberra'   => '(GMT+10:00) Canberra',
    'Pacific/Guam'         => '(GMT+10:00) Guam',
    'Australia/Hobart'     => '(GMT+10:00) Hobart',
    'Australia/Melbourne'  => '(GMT+10:00) Melbourne',
    'Pacific/Port_Moresby' => '(GMT+10:00) Port Moresby',
    'Australia/Sydney'     => '(GMT+10:00) Sydney',
    'Asia/Vladivostok'     => '(GMT+11:00) Vladivostok',
    'Asia/Magadan'         => '(GMT+12:00) Magadan',
    'Pacific/Auckland'     => '(GMT+12:00) Auckland',
    'Pacific/Fiji'         => '(GMT+12:00) Fiji',
);

?>