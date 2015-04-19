<?php

/*
 * Copyright (C) 2013-2015 Luna
 * Based on code by FluxBB copyright (C) 2008-2012 FluxBB
 * Based on code by Rickard Andersson copyright (C) 2002-2008 PunBB
 * Licensed under GPLv3 (http://getluna.org/license.php)
 */

class Installer {
	
	const DEFAULT_LANG = 'English';
	const DEFAULT_STYLE = 'Luna';
	
	public static function is_supported_php_version() {
		return function_exists('version_compare') && version_compare(PHP_VERSION, Version::MIN_PHP_VERSION, '>=');
	}
	
	public static function guess_base_url() {
		// Make an educated guess regarding base_url
		$base_url  = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ? 'https://' : 'http://';	// protocol
		$base_url .= preg_replace('%:(80|443)$%', '', $_SERVER['HTTP_HOST']);							// host[:port]
		$base_url .= str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));							// path
		
		return $base_url;
	}
	
	public static function determine_database_extensions() {
		// Determine available database extensions
		$dual_mysql = false;
		$db_extensions = array();
		$mysql_innodb = false;

		if (function_exists('mysqli_connect')) {
			$db_extensions[] = array('mysqli', 'MySQL Improved');
			$db_extensions[] = array('mysqli_innodb', 'MySQL Improved (InnoDB)');
			$mysql_innodb = true;
		}

		if (function_exists('mysql_connect')) {
			$db_extensions[] = array('mysql', 'MySQL Standard');
			$db_extensions[] = array('mysql_innodb', 'MySQL Standard (InnoDB)');
			$mysql_innodb = true;
	
			if (count($db_extensions) > 2)
				$dual_mysql = true;
		}

		if (function_exists('sqlite_open'))
			$db_extensions[] = array('sqlite', 'SQLite');

		if (function_exists('pg_connect'))
			$db_extensions[] = array('pgsql', 'PostgreSQL');
			
		return $db_extensions;
	}
	
	public static function generate_config_file($db_type, $db_host, $db_name, $db_username, $db_password, $db_prefix = '', $cookie_name = false, $cookie_seed = false) {
		if ($cookie_name === false)
		$cookie_name = 'luna_cookie_'.random_key(6, false, true);
		
		if ($cookie_seed === false)
		$cookie_seed = random_key(16, false, true);
		
		return '<?php'."\n\n".'$db_type = \''.$db_type."';\n".'$db_host = \''.$db_host."';\n".'$db_name = \''.addslashes($db_name)."';\n".'$db_username = \''.addslashes($db_username)."';\n".'$db_password = \''.addslashes($db_password)."';\n".'$db_prefix = \''.addslashes($db_prefix)."';\n".'$p_connect = false;'."\n\n".'$cookie_name = '."'".$cookie_name."';\n".'$cookie_domain = '."'';\n".'$cookie_path = '."'/';\n".'$cookie_secure = 0;'."\n".'$cookie_seed = \''.$cookie_seed."';\n\ndefine('PUN', 1);\n";
	}
	
	public static function validate_config($username, $password1, $password2, $email, $title, $default_lang, $default_style) {
		global $lang;

		$alerts = array();

		// Validate username and passwords
		if (luna_strlen($username) < 2)
			$alerts[] = $lang['Username 1'];
		elseif (luna_strlen($username) > 25) // This usually doesn't happen since the form element only accepts 25 characters
			$alerts[] = $lang['Username 2'];
		elseif (!strcasecmp($username, 'Guest'))
			$alerts[] = $lang['Username 3'];
		elseif (preg_match('%[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}%', $username) || preg_match('%((([0-9A-Fa-f]{1,4}:){7}[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){6}:[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){5}:([0-9A-Fa-f]{1,4}:)?[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){4}:([0-9A-Fa-f]{1,4}:){0,2}[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){3}:([0-9A-Fa-f]{1,4}:){0,3}[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){2}:([0-9A-Fa-f]{1,4}:){0,4}[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){6}((\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b)\.){3}(\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b))|(([0-9A-Fa-f]{1,4}:){0,5}:((\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b)\.){3}(\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b))|(::([0-9A-Fa-f]{1,4}:){0,5}((\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b)\.){3}(\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b))|([0-9A-Fa-f]{1,4}::([0-9A-Fa-f]{1,4}:){0,5}[0-9A-Fa-f]{1,4})|(::([0-9A-Fa-f]{1,4}:){0,6}[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){1,7}:))%', $username))
			$alerts[] = $lang['Username 4'];
		elseif ((strpos($username, '[') !== false || strpos($username, ']') !== false) && strpos($username, '\'') !== false && strpos($username, '"') !== false)
			$alerts[] = $lang['Username 5'];
		elseif (preg_match('%(?:\[/?(?:b|u|i|h|colou?r|quote|code|img|url|email|list)\]|\[(?:code|quote|list)=)%i', $username))
			$alerts[] = $lang['Username 6'];
	
		if (luna_strlen($password1) < 4)
			$alerts[] = $lang['Short password'];
		elseif ($password1 != $password2)
			$alerts[] = $lang['Passwords not match'];
	
		// Validate email
		require FORUM_ROOT.'include/email.php';
	
		if (!is_valid_email($email))
			$alerts[] = $lang['Wrong email'];
	
		if ($title == '')
			$alerts[] = $lang['No board title'];
	
		$languages = forum_list_langs();
		if (!in_array($default_lang, $languages))
			$alerts[] = $lang['Error default language'];
	
		$styles = forum_list_styles();
		if (!in_array($default_style, $styles))
			$alerts[] = $lang['Error default style'];
			
		return $alerts;
	}
	
	private static function load_database_driver($db_type) {
		global $lang;
		
		// Load the appropriate DB layer class
		switch ($db_type) {
			case 'mysql':
				require FORUM_ROOT.'include/dblayer/mysql.php';
				break;
	
			case 'mysql_innodb':
				require FORUM_ROOT.'include/dblayer/mysql_innodb.php';
				break;
	
			case 'mysqli':
				require FORUM_ROOT.'include/dblayer/mysqli.php';
				break;
	
			case 'mysqli_innodb':
				require FORUM_ROOT.'include/dblayer/mysqli_innodb.php';
				break;
	
			case 'pgsql':
				require PUN_ROOT.'include/dblayer/pgsql.php';
				break;
	
			case 'sqlite':
				require FORUM_ROOT.'include/dblayer/sqlite.php';
				break;
	
			default:
				error(sprintf($lang['DB type not valid'], luna_htmlspecialchars($db_type)));
		}
	}
	
	private static function validate_database_version($db_type, $db) {
		global $lang;
		
		// Do some DB type specific checks
		switch ($db_type) {
			case 'mysql':
			case 'mysqli':
			case 'mysql_innodb':
			case 'mysqli_innodb':
				$mysql_info = $db->get_version();
				if (version_compare($mysql_info['version'], Version::MIN_MYSQL_VERSION, '<'))
					error(sprintf($lang['You are running error'], 'MySQL', $mysql_info['version'], Version::FORUM_VERSION, Version::MIN_MYSQL_VERSION));
				break;
	
			case 'pgsql':
				$pgsql_info = $db->get_version();
				if (version_compare($pgsql_info['version'], Version::MIN_PGSQL_VERSION, '<'))
					error(sprintf($lang['You are running error'], 'PostgreSQL', $pgsql_info['version'], Version::FORUM_VERSION, Version::MIN_PGSQL_VERSION));
				break;
	
			case 'sqlite':
				if (strtolower($db_prefix) == 'sqlite_')
					error($lang['Prefix reserved']);
				break;
		}

		// Check if InnoDB is available
		if ($db_type == 'mysql_innodb' || $db_type == 'mysqli_innodb') {
			$result = $db->query('SHOW VARIABLES LIKE \'have_innodb\'');
			list (, $result) = $db->fetch_row($result);
			if ((strtoupper($result) != 'YES'))
				error($lang['InnoDB off']);
		}
	}
	
	public static function create_database($db_type, $db_host, $db_name, $db_username, $db_password, $db_prefix, $title, $description, $default_lang, $default_style, $email, $avatars, $base_url) {
		global $lang;

		// Validate prefix
		if (strlen($db_prefix) > 0 && (!preg_match('%^[a-zA-Z_][a-zA-Z0-9_]*$%', $db_prefix) || strlen($db_prefix) > 40))
		error(sprintf($lang['Table prefix error'], $db->prefix));
		
		// Load the appropriate DB layer class
		Installer::load_database_driver($db_type);
		
		// Create the database object (and connect/select db)
		$db = new DBLayer($db_host, $db_username, $db_password, $db_name, $db_prefix, false);
		
		// Do some DB type specific checks
		Installer::validate_database_version($db_type, $db);
		
		// Make sure FluxBB isn't already installed
		$result = $db->query('SELECT 1 FROM '.$db->prefix.'users WHERE id=1');
		if ($db->num_rows($result))
		error(sprintf($lang['Existing table error'], $db->prefix, $db_name));
		
		// Start a transaction
		$db->start_transaction();
		
		// Create all tables
		$schema = array(
			'FIELDS'		=> array(
				'id'			=> array(
					'datatype'		=> 'SERIAL',
					'allow_null'	=> false
				),
				'username'		=> array(
					'datatype'		=> 'VARCHAR(200)',
					'allow_null'	=> true
				),
				'ip'			=> array(
					'datatype'		=> 'VARCHAR(255)',
					'allow_null'	=> true
				),
				'email'			=> array(
					'datatype'		=> 'VARCHAR(80)',
					'allow_null'	=> true
				),
				'message'		=> array(
					'datatype'		=> 'VARCHAR(255)',
					'allow_null'	=> true
				),
				'expire'		=> array(
					'datatype'		=> 'INT(10) UNSIGNED',
					'allow_null'	=> true
				),
				'ban_creator'	=> array(
					'datatype'		=> 'INT(10) UNSIGNED',
					'allow_null'	=> false,
					'default'		=> '0'
				)
			),
			'PRIMARY KEY'	=> array('id'),
			'INDEXES'		=> array(
				'username_idx'	=> array('username')
			)
		);
	
		if ($db_type == 'mysql' || $db_type == 'mysqli' || $db_type == 'mysql_innodb' || $db_type == 'mysqli_innodb')
			$schema['INDEXES']['username_idx'] = array('username(25)');
	
		$db->create_table('bans', $schema) or error('Unable to create bans table', __FILE__, __LINE__, $db->error());
	
	
		$schema = array(
			'FIELDS'		=> array(
				'id'			=> array(
					'datatype'		=> 'SERIAL',
					'allow_null'	=> false
				),
				'cat_name'		=> array(
					'datatype'		=> 'VARCHAR(80)',
					'allow_null'	=> false,
					'default'		=> '\'New Category\''
				),
				'disp_position'	=> array(
					'datatype'		=> 'INT(10)',
					'allow_null'	=> false,
					'default'		=> '0'
				)
			),
			'PRIMARY KEY'	=> array('id')
		);
	
		$db->create_table('categories', $schema) or error('Unable to create categories table', __FILE__, __LINE__, $db->error());
	
	
		$schema = array(
			'FIELDS'		=> array(
				'id'			=> array(
					'datatype'		=> 'SERIAL',
					'allow_null'	=> false
				),
				'search_for'	=> array(
					'datatype'		=> 'VARCHAR(60)',
					'allow_null'	=> false,
					'default'		=> '\'\''
				),
				'replace_with'	=> array(
					'datatype'		=> 'VARCHAR(60)',
					'allow_null'	=> false,
					'default'		=> '\'\''
				)
			),
			'PRIMARY KEY'	=> array('id')
		);
	
		$db->create_table('censoring', $schema) or error('Unable to create censoring table', __FILE__, __LINE__, $db->error());
	
	
		$schema = array(
			'FIELDS'		=> array(
				'conf_name'		=> array(
					'datatype'		=> 'VARCHAR(255)',
					'allow_null'	=> false,
					'default'		=> '\'\''
				),
				'conf_value'	=> array(
					'datatype'		=> 'TEXT',
					'allow_null'	=> true
				)
			),
			'PRIMARY KEY'	=> array('conf_name')
		);
	
		$db->create_table('config', $schema) or error('Unable to create config table', __FILE__, __LINE__, $db->error());
	
	
		$schema = array(
			'FIELDS'		=> array(
				'group_id'		=> array(
					'datatype'		=> 'INT(10)',
					'allow_null'	=> false,
					'default'		=> '0'
				),
				'forum_id'		=> array(
					'datatype'		=> 'INT(10)',
					'allow_null'	=> false,
					'default'		=> '0'
				),
				'read_forum'	=> array(
					'datatype'		=> 'TINYINT(1)',
					'allow_null'	=> false,
					'default'		=> '1'
				),
				'post_replies'	=> array(
					'datatype'		=> 'TINYINT(1)',
					'allow_null'	=> false,
					'default'		=> '1'
				),
				'post_topics'	=> array(
					'datatype'		=> 'TINYINT(1)',
					'allow_null'	=> false,
					'default'		=> '1'
				)
			),
			'PRIMARY KEY'	=> array('group_id', 'forum_id')
		);
	
		$db->create_table('forum_perms', $schema) or error('Unable to create forum_perms table', __FILE__, __LINE__, $db->error());
	
	
		$schema = array(
			'FIELDS'		=> array(
				'id'			=> array(
					'datatype'		=> 'SERIAL',
					'allow_null'	=> false
				),
				'forum_name'	=> array(
					'datatype'		=> 'VARCHAR(80)',
					'allow_null'	=> false,
					'default'		=> '\'New forum\''
				),
				'forum_desc'	=> array(
					'datatype'		=> 'TEXT',
					'allow_null'	=> true
				),
				'moderators'	=> array(
					'datatype'		=> 'TEXT',
					'allow_null'	=> true
				),
				'num_topics'	=> array(
					'datatype'		=> 'MEDIUMINT(8) UNSIGNED',
					'allow_null'	=> false,
					'default'		=> '0'
				),
				'num_posts'		=> array(
					'datatype'		=> 'MEDIUMINT(8) UNSIGNED',
					'allow_null'	=> false,
					'default'		=> '0'
				),
				'last_post'		=> array(
					'datatype'		=> 'INT(10) UNSIGNED',
					'allow_null'	=> true
				),
				'last_post_id'	=> array(
					'datatype'		=> 'INT(10) UNSIGNED',
					'allow_null'	=> true
				),
				'last_poster_id'=> array(
					'datatype'		=> 'INT(10)',
					'allow_null'	=> true,
					'default'		=> NULL,
				),
				'sort_by'		=> array(
					'datatype'		=> 'TINYINT(1)',
					'allow_null'	=> false,
					'default'		=> '0'
				),
				'disp_position'	=> array(
					'datatype'		=> 'INT(10)',
					'allow_null'	=> false,
					'default'		=> '0'
				),
				'cat_id'		=> array(
					'datatype'		=> 'INT(10) UNSIGNED',
					'allow_null'	=> false,
					'default'		=> '0'
				),
				'color'			=> array(
					'datatype'		=> 'VARCHAR(25)',
					'allow_null'	=> false,
					'default'		=> '\'#0d4382\''
				),
				'parent_id'		=> array(
					'datatype'		=> 'INT',
					'allow_null'	=> true,
					'default'		=> 0
				)
			),
			'PRIMARY KEY'	=> array('id')
		);
	
		$db->create_table('forums', $schema) or error('Unable to create forums table', __FILE__, __LINE__, $db->error());
	
	
		$schema = array(
			'FIELDS'		=> array(
				'g_id'						=> array(
					'datatype'		=> 'SERIAL',
					'allow_null'	=> false
				),
				'g_title'					=> array(
					'datatype'		=> 'VARCHAR(50)',
					'allow_null'	=> false,
					'default'		=> '\'\''
				),
				'g_user_title'				=> array(
					'datatype'		=> 'VARCHAR(50)',
					'allow_null'	=> true
				),
				'g_moderator'				=> array(
					'datatype'		=> 'TINYINT(1)',
					'allow_null'	=> false,
					'default'		=> '0'
				),
				'g_mod_edit_users'			=> array(
					'datatype'		=> 'TINYINT(1)',
					'allow_null'	=> false,
					'default'		=> '0'
				),
				'g_mod_rename_users'		=> array(
					'datatype'		=> 'TINYINT(1)',
					'allow_null'	=> false,
					'default'		=> '0'
				),
				'g_mod_change_passwords'	=> array(
					'datatype'		=> 'TINYINT(1)',
					'allow_null'	=> false,
					'default'		=> '0'
				),
				'g_mod_ban_users'			=> array(
					'datatype'		=> 'TINYINT(1)',
					'allow_null'	=> false,
					'default'		=> '0'
				),
				'g_read_board'				=> array(
					'datatype'		=> 'TINYINT(1)',
					'allow_null'	=> false,
					'default'		=> '1'
				),
				'g_view_users'				=> array(
					'datatype'		=> 'TINYINT(1)',
					'allow_null'	=> false,
					'default'		=> '1'
				),
				'g_post_replies'			=> array(
					'datatype'		=> 'TINYINT(1)',
					'allow_null'	=> false,
					'default'		=> '1'
				),
				'g_post_topics'				=> array(
					'datatype'		=> 'TINYINT(1)',
					'allow_null'	=> false,
					'default'		=> '1'
				),
				'g_edit_posts'				=> array(
					'datatype'		=> 'TINYINT(1)',
					'allow_null'	=> false,
					'default'		=> '1'
				),
				'g_delete_posts'			=> array(
					'datatype'		=> 'TINYINT(1)',
					'allow_null'	=> false,
					'default'		=> '1'
				),
				'g_delete_topics'			=> array(
					'datatype'		=> 'TINYINT(1)',
					'allow_null'	=> false,
					'default'		=> '1'
				),
				'g_set_title'				=> array(
					'datatype'		=> 'TINYINT(1)',
					'allow_null'	=> false,
					'default'		=> '1'
				),
				'g_search'					=> array(
					'datatype'		=> 'TINYINT(1)',
					'allow_null'	=> false,
					'default'		=> '1'
				),
				'g_search_users'			=> array(
					'datatype'		=> 'TINYINT(1)',
					'allow_null'	=> false,
					'default'		=> '1'
				),
				'g_send_email'				=> array(
					'datatype'		=> 'TINYINT(1)',
					'allow_null'	=> false,
					'default'		=> '1'
				),
				'g_post_flood'				=> array(
					'datatype'		=> 'SMALLINT(6)',
					'allow_null'	=> false,
					'default'		=> '30'
				),
				'g_search_flood'			=> array(
					'datatype'		=> 'SMALLINT(6)',
					'allow_null'	=> false,
					'default'		=> '30'
				),
				'g_email_flood'				=> array(
					'datatype'		=> 'SMALLINT(6)',
					'allow_null'	=> false,
					'default'		=> '60'
				),
				'g_pm'						=> array(
					'datatype'		=> 'TINYINT(1)',
					'allow_null'	=> false,
					'default'		=> '1'
				),
				'g_pm_limit'				=> array(
					'datatype'		=> 'INT',
					'allow_null'	=> false,
					'default'		=> '20'
				),
				'g_report_flood'			=> array(
					'datatype'		=> 'SMALLINT(6)',
					'allow_null'	=> false,
					'default'		=> '60'
				),
				'g_soft_delete_view'		=> array(
					'datatype'		=> 'TINYINT(1)',
					'allow_null'	=> false,
					'default'		=> '1'
				),
				'g_soft_delete_posts'		=> array(
					'datatype'		=> 'TINYINT(1)',
					'allow_null'	=> false,
					'default'		=> '1'
				),
				'g_soft_delete_topics'		=> array(
					'datatype'		=> 'TINYINT(1)',
					'allow_null'	=> false,
					'default'		=> '1'
				)
			),
			'PRIMARY KEY'	=> array('g_id')
		);
		
		$db->create_table('groups', $schema) or error('Unable to create groups table', __FILE__, __LINE__, $db->error());
	
		$schema = array(
			'FIELDS'		=> array(
				'id'			=> array(
					'datatype'		=> 'SERIAL',
					'allow_null'	=> false
				),
				'url'			=> array(
					'datatype'		=> 'VARCHAR(200)',
					'allow_null'	=> false,
					'default'		=> '\'\''
				),
				'name'			=> array(
					'datatype'		=> 'VARCHAR(200)',
					'allow_null'	=> false,
					'default'		=> '\'\''
				),
				'disp_position'	=> array(
					'datatype'		=> 'INT(10)',
					'allow_null'	=> false,
					'default'		=> '0'
				),
				'visible'	=> array(
					'datatype'		=> 'INT(10)',
					'allow_null'	=> false,
					'default'		=> '1'
				),
				'sys_entry'		=> array(
					'datatype'		=> 'INT(10)',
					'allow_null'	=> true,
					'default'		=> '0'
				)
			),
			'PRIMARY KEY'	=> array('id')
		);
	
		$db->create_table('menu', $schema) or error('Unable to create menu table', __FILE__, __LINE__, $db->error());
	
	
		$schema = array(
			'FIELDS'			=> array(
				'id'				=> array(
					'datatype'			=> 'SERIAL',
					'allow_null'		=> false
				),
				'user_id'			=> array(
					'datatype'			=> 'INT(10)',
					'allow_null'		=> false,
					'default'			=> '0'
				),
				'message'			=> array(
					'datatype'			=> 'VARCHAR(255)',
					'allow_null'		=> false,
					'default'			=> '0'
				),
				'icon'				=> array(
					'datatype'			=> 'VARCHAR(255)',
					'allow_null'		=> false,
					'default'			=> '0'
				),
				'link'			=> array(
					'datatype'			=> 'VARCHAR(255)',
					'allow_null'		=> false,
					'default'			=> '0'
				),
				'time'				=> array(
					'datatype'			=> 'INT(11)',
					'allow_null'		=> false,
					'default'			=> '0'
				),
				'viewed'			=> array(
					'datatype'		=> 'TINYINT(1)',
					'allow_null'		=> false,
					'default'			=> '0'
				),
			),
			'PRIMARY KEY'		=> array('id'),
		);
		
		$db->create_table('notifications', $schema) or error('Unable to create notifications table', __FILE__, __LINE__, $db->error());
	
	
		$schema = array(
			'FIELDS'		=> array(
				'user_id'		=> array(
					'datatype'		=> 'INT(10) UNSIGNED',
					'allow_null'	=> false,
					'default'		=> '1'
				),
				'ident'			=> array(
					'datatype'		=> 'VARCHAR(200)',
					'allow_null'	=> false,
					'default'		=> '\'\''
				),
				'logged'		=> array(
					'datatype'		=> 'INT(10) UNSIGNED',
					'allow_null'	=> false,
					'default'		=> '0'
				),
				'idle'			=> array(
					'datatype'		=> 'TINYINT(1)',
					'allow_null'	=> false,
					'default'		=> '0'
				),
				'last_post'			=> array(
					'datatype'		=> 'INT(10) UNSIGNED',
					'allow_null'	=> true
				),
				'last_search'		=> array(
					'datatype'		=> 'INT(10) UNSIGNED',
					'allow_null'	=> true
				),
			),
	
			'UNIQUE KEYS'	=> array(
				'user_id_ident_idx'	=> array('user_id', 'ident')
			),
			'INDEXES'		=> array(
				'ident_idx'		=> array('ident'),
				'logged_idx'	=> array('logged')
			)
		);
	
		if ($db_type == 'mysql' || $db_type == 'mysqli' || $db_type == 'mysql_innodb' || $db_type == 'mysqli_innodb') {
			$schema['UNIQUE KEYS']['user_id_ident_idx'] = array('user_id', 'ident(25)');
			$schema['INDEXES']['ident_idx'] = array('ident(25)');
		}
	
		if ($db_type == 'mysql_innodb' || $db_type == 'mysqli_innodb')
			$schema['ENGINE'] = 'InnoDB';
	
		$db->create_table('online', $schema) or error('Unable to create online table', __FILE__, __LINE__, $db->error());
	
	
		$schema = array(
			'FIELDS'		=> array(
				'id'			=> array(
					'datatype'		=> 'SERIAL',
					'allow_null'	=> false
				),
				'poster'		=> array(
					'datatype'		=> 'VARCHAR(200)',
					'allow_null'	=> false,
					'default'		=> '\'\''
				),
				'poster_id'		=> array(
					'datatype'		=> 'INT(10) UNSIGNED',
					'allow_null'	=> false,
					'default'		=> '1'
				),
				'poster_ip'		=> array(
					'datatype'		=> 'VARCHAR(39)',
					'allow_null'	=> true
				),
				'poster_email'	=> array(
					'datatype'		=> 'VARCHAR(80)',
					'allow_null'	=> true
				),
				'message'		=> array(
					'datatype'		=> 'MEDIUMTEXT',
					'allow_null'	=> true
				),
				'hide_smilies'	=> array(
					'datatype'		=> 'TINYINT(1)',
					'allow_null'	=> false,
					'default'		=> '0'
				),
				'posted'		=> array(
					'datatype'		=> 'INT(10) UNSIGNED',
					'allow_null'	=> false,
					'default'		=> '0'
				),
				'edited'		=> array(
					'datatype'		=> 'INT(10) UNSIGNED',
					'allow_null'	=> true
				),
				'edited_by'		=> array(
					'datatype'		=> 'VARCHAR(200)',
					'allow_null'	=> true
				),
				'topic_id'		=> array(
					'datatype'		=> 'INT(10) UNSIGNED',
					'allow_null'	=> false,
					'default'		=> '0'
				),
				'marked'		=> array(
					'datatype'		=> 'TINYINT(1)',
					'allow_null'	=> false,
					'default'		=> '0'
				),
				'soft'		=> array(
					'datatype'		=> 'TINYINT(1)',
					'allow_null'	=> false,
					'default'		=> '0'
				)
			),
			'PRIMARY KEY'	=> array('id'),
			'INDEXES'		=> array(
				'topic_id_idx'	=> array('topic_id'),
				'multi_idx'		=> array('poster_id', 'topic_id')
			)
		);
	
		$db->create_table('posts', $schema) or error('Unable to create posts table', __FILE__, __LINE__, $db->error());
	
	
		$schema = array(
			'FIELDS'		=> array(
				'id'			=> array(
					'datatype'		=> 'SERIAL',
					'allow_null'	=> false
				),
				'rank'			=> array(
					'datatype'		=> 'VARCHAR(50)',
					'allow_null'	=> false,
					'default'		=> '\'\''
				),
				'min_posts'		=> array(
					'datatype'		=> 'MEDIUMINT(8) UNSIGNED',
					'allow_null'	=> false,
					'default'		=> '0'
				)
			),
			'PRIMARY KEY'	=> array('id')
		);
	
		$db->create_table('ranks', $schema) or error('Unable to create ranks table', __FILE__, __LINE__, $db->error());
	
	
		$schema = array(
			'FIELDS'		=> array(
				'id'			=> array(
					'datatype'		=> 'SERIAL',
					'allow_null'	=> false
				),
				'post_id'		=> array(
					'datatype'		=> 'INT(10) UNSIGNED',
					'allow_null'	=> false,
					'default'		=> '0'
				),
				'topic_id'		=> array(
					'datatype'		=> 'INT(10) UNSIGNED',
					'allow_null'	=> false,
					'default'		=> '0'
				),
				'forum_id'		=> array(
					'datatype'		=> 'INT(10) UNSIGNED',
					'allow_null'	=> false,
					'default'		=> '0'
				),
				'reported_by'	=> array(
					'datatype'		=> 'INT(10) UNSIGNED',
					'allow_null'	=> false,
					'default'		=> '0'
				),
				'created'		=> array(
					'datatype'		=> 'INT(10) UNSIGNED',
					'allow_null'	=> false,
					'default'		=> '0'
				),
				'message'		=> array(
					'datatype'		=> 'TEXT',
					'allow_null'	=> true
				),
				'zapped'		=> array(
					'datatype'		=> 'INT(10) UNSIGNED',
					'allow_null'	=> true
				),
				'zapped_by'		=> array(
					'datatype'		=> 'INT(10) UNSIGNED',
					'allow_null'	=> true
				)
			),
			'PRIMARY KEY'	=> array('id'),
			'INDEXES'		=> array(
				'zapped_idx'	=> array('zapped')
			)
		);
	
		$db->create_table('reports', $schema) or error('Unable to create reports table', __FILE__, __LINE__, $db->error());
	
	
		$schema = array(
			'FIELDS'		=> array(
				'id'			=> array(
					'datatype'		=> 'INT(10) UNSIGNED',
					'allow_null'	=> false,
					'default'		=> '0'
				),
				'ident'			=> array(
					'datatype'		=> 'VARCHAR(200)',
					'allow_null'	=> false,
					'default'		=> '\'\''
				),
				'search_data'	=> array(
					'datatype'		=> 'MEDIUMTEXT',
					'allow_null'	=> true
				)
			),
			'PRIMARY KEY'	=> array('id'),
			'INDEXES'		=> array(
				'ident_idx'	=> array('ident')
			)
		);
	
		if ($db_type == 'mysql' || $db_type == 'mysqli' || $db_type == 'mysql_innodb' || $db_type == 'mysqli_innodb')
			$schema['INDEXES']['ident_idx'] = array('ident(8)');
	
		$db->create_table('search_cache', $schema) or error('Unable to create search_cache table', __FILE__, __LINE__, $db->error());
	
	
		$schema = array(
			'FIELDS'		=> array(
				'post_id'		=> array(
					'datatype'		=> 'INT(10) UNSIGNED',
					'allow_null'	=> false,
					'default'		=> '0'
				),
				'word_id'		=> array(
					'datatype'		=> 'INT(10) UNSIGNED',
					'allow_null'	=> false,
					'default'		=> '0'
				),
				'subject_match'	=> array(
					'datatype'		=> 'TINYINT(1)',
					'allow_null'	=> false,
					'default'		=> '0'
				)
			),
			'INDEXES'		=> array(
				'word_id_idx'	=> array('word_id'),
				'post_id_idx'	=> array('post_id')
			)
		);
	
		$db->create_table('search_matches', $schema) or error('Unable to create search_matches table', __FILE__, __LINE__, $db->error());
	
	
		$schema = array(
			'FIELDS'		=> array(
				'id'			=> array(
					'datatype'		=> 'SERIAL',
					'allow_null'	=> false
				),
				'word'			=> array(
					'datatype'		=> 'VARCHAR(20)',
					'allow_null'	=> false,
					'default'		=> '\'\'',
					'collation'		=> 'bin'
				)
			),
			'PRIMARY KEY'	=> array('word'),
			'INDEXES'		=> array(
				'id_idx'	=> array('id')
			)
		);
	
		if ($db_type == 'sqlite') {
			$schema['PRIMARY KEY'] = array('id');
			$schema['UNIQUE KEYS'] = array('word_idx'	=> array('word'));
		}
	
		$db->create_table('search_words', $schema) or error('Unable to create search_words table', __FILE__, __LINE__, $db->error());
	
	
		$schema = array(
			'FIELDS'		=> array(
				'user_id'		=> array(
					'datatype'		=> 'INT(10) UNSIGNED',
					'allow_null'	=> false,
					'default'		=> '0'
				),
				'topic_id'		=> array(
					'datatype'		=> 'INT(10) UNSIGNED',
					'allow_null'	=> false,
					'default'		=> '0'
				)
			),
			'PRIMARY KEY'	=> array('user_id', 'topic_id')
		);
	
		$db->create_table('topic_subscriptions', $schema) or error('Unable to create topic subscriptions table', __FILE__, __LINE__, $db->error());
	
	
		$schema = array(
			'FIELDS'		=> array(
				'user_id'		=> array(
					'datatype'		=> 'INT(10) UNSIGNED',
					'allow_null'	=> false,
					'default'		=> '0'
				),
				'forum_id'		=> array(
					'datatype'		=> 'INT(10) UNSIGNED',
					'allow_null'	=> false,
					'default'		=> '0'
				)
			),
			'PRIMARY KEY'	=> array('user_id', 'forum_id')
		);
	
		$db->create_table('forum_subscriptions', $schema) or error('Unable to create forum subscriptions table', __FILE__, __LINE__, $db->error());
	
	
		$schema = array(
			'FIELDS'		=> array(
				'id'			=> array(
					'datatype'		=> 'SERIAL',
					'allow_null'	=> false
				),
				'poster'		=> array(
					'datatype'		=> 'VARCHAR(200)',
					'allow_null'	=> false,
					'default'		=> '\'\''
				),
				'subject'		=> array(
					'datatype'		=> 'VARCHAR(255)',
					'allow_null'	=> false,
					'default'		=> '\'\''
				),
				'posted'		=> array(
					'datatype'		=> 'INT(10) UNSIGNED',
					'allow_null'	=> false,
					'default'		=> '0'
				),
				'first_post_id'	=> array(
					'datatype'		=> 'INT(10) UNSIGNED',
					'allow_null'	=> false,
					'default'		=> '0'
				),
				'last_post'		=> array(
					'datatype'		=> 'INT(10) UNSIGNED',
					'allow_null'	=> false,
					'default'		=> '0'
				),
				'last_post_id'	=> array(
					'datatype'		=> 'INT(10) UNSIGNED',
					'allow_null'	=> false,
					'default'		=> '0'
				),
				'last_poster'	=> array(
					'datatype'		=> 'VARCHAR(200)',
					'allow_null'	=> true
				),
				'num_views'		=> array(
					'datatype'		=> 'MEDIUMINT(8) UNSIGNED',
					'allow_null'	=> false,
					'default'		=> '0'
				),
				'num_replies'	=> array(
					'datatype'		=> 'MEDIUMINT(8) UNSIGNED',
					'allow_null'	=> false,
					'default'		=> '0'
				),
				'last_poster_id'=> array(
					'datatype'		=> 'INT(10)',
					'allow_null'	=> true,
					'default'		=> NULL,
				),
				'closed'		=> array(
					'datatype'		=> 'TINYINT(1)',
					'allow_null'	=> false,
					'default'		=> '0'
				),
				'sticky'		=> array(
					'datatype'		=> 'TINYINT(1)',
					'allow_null'	=> false,
					'default'		=> '0'
				),
				'moved_to'		=> array(
					'datatype'		=> 'INT(10) UNSIGNED',
					'allow_null'	=> true
				),
				'forum_id'		=> array(
					'datatype'		=> 'INT(10) UNSIGNED',
					'allow_null'	=> false,
					'default'		=> '0'
				),
				'soft'		=> array(
					'datatype'		=> 'TINYINT(1)',
					'allow_null'	=> false,
					'default'		=> '0'
				)
			),
			'PRIMARY KEY'	=> array('id'),
			'INDEXES'		=> array(
				'forum_id_idx'		=> array('forum_id'),
				'moved_to_idx'		=> array('moved_to'),
				'last_post_idx'		=> array('last_post'),
				'last_poster_id'	=> array('last_poster'),
				'first_post_id_idx'	=> array('first_post_id')
			)
		);
	
		$db->create_table('topics', $schema) or error('Unable to create topics table', __FILE__, __LINE__, $db->error());
	
		$schema = array(
			'FIELDS'		=> array(
				'id'				=> array(
					'datatype'		=> 'SERIAL',
					'allow_null'	=> false
				),
				'group_id'			=> array(
					'datatype'		=> 'INT(10) UNSIGNED',
					'allow_null'	=> false,
					'default'		=> '3'
				),
				'username'			=> array(
					'datatype'		=> 'VARCHAR(200)',
					'allow_null'	=> false,
					'default'		=> '\'\''
				),
				'password'			=> array(
					'datatype'		=> 'VARCHAR(256)',
					'allow_null'	=> false,
					'default'		=> '\'\''
				),
				// 'salt'			=> array(
				// 	'datatype'		=> 'VARCHAR(10)',
				// 	'allow_null'	=> false,
				// 	'default'		=> NULL,
				// ),
				'email'				=> array(
					'datatype'		=> 'VARCHAR(80)',
					'allow_null'	=> false,
					'default'		=> '\'\''
				),
				'title'				=> array(
					'datatype'		=> 'VARCHAR(50)',
					'allow_null'	=> true
				),
				'realname'			=> array(
					'datatype'		=> 'VARCHAR(40)',
					'allow_null'	=> true
				),
				'url'				=> array(
					'datatype'		=> 'VARCHAR(100)',
					'allow_null'	=> true
				),
				'facebook'			=> array(
					'datatype'		=> 'VARCHAR(50)',
					'allow_null'	=> true
				),
				'msn'				=> array(
					'datatype'		=> 'VARCHAR(80)',
					'allow_null'	=> true
				),
				'twitter'			=> array(
					'datatype'		=> 'VARCHAR(50)',
					'allow_null'	=> true
				),
				'google'			=> array(
					'datatype'		=> 'VARCHAR(50)',
					'allow_null'	=> true
				),
				'location'			=> array(
					'datatype'		=> 'VARCHAR(30)',
					'allow_null'	=> true
				),
				'signature'			=> array(
					'datatype'		=> 'TEXT',
					'allow_null'	=> true
				),
				'disp_topics'		=> array(
					'datatype'		=> 'TINYINT(3) UNSIGNED',
					'allow_null'	=> true
				),
				'disp_posts'		=> array(
					'datatype'		=> 'TINYINT(3) UNSIGNED',
					'allow_null'	=> true
				),
				'email_setting'		=> array(
					'datatype'		=> 'TINYINT(1)',
					'allow_null'	=> false,
					'default'		=> '1'
				),
				'notify_with_post'	=> array(
					'datatype'		=> 'TINYINT(1)',
					'allow_null'	=> false,
					'default'		=> '0'
				),
				'auto_notify'		=> array(
					'datatype'		=> 'TINYINT(1)',
					'allow_null'	=> false,
					'default'		=> '0'
				),
				'show_smilies'		=> array(
					'datatype'		=> 'TINYINT(1)',
					'allow_null'	=> false,
					'default'		=> '1'
				),
				'show_img'			=> array(
					'datatype'		=> 'TINYINT(1)',
					'allow_null'	=> false,
					'default'		=> '1'
				),
				'show_img_sig'		=> array(
					'datatype'		=> 'TINYINT(1)',
					'allow_null'	=> false,
					'default'		=> '1'
				),
				'show_avatars'		=> array(
					'datatype'		=> 'TINYINT(1)',
					'allow_null'	=> false,
					'default'		=> '1'
				),
				'show_sig'			=> array(
					'datatype'		=> 'TINYINT(1)',
					'allow_null'	=> false,
					'default'		=> '1'
				),
				'timezone'			=> array(
					'datatype'		=> 'FLOAT',
					'allow_null'	=> false,
					'default'		=> '0'
				),
				'dst'				=> array(
					'datatype'		=> 'TINYINT(1)',
					'allow_null'	=> false,
					'default'		=> '0'
				),
				'time_format'		=> array(
					'datatype'		=> 'TINYINT(1)',
					'allow_null'	=> false,
					'default'		=> '0'
				),
				'date_format'		=> array(
					'datatype'		=> 'TINYINT(1)',
					'allow_null'	=> false,
					'default'		=> '0'
				),
				'language'			=> array(
					'datatype'		=> 'VARCHAR(25)',
					'allow_null'	=> false,
					'default'		=> '\''.$db->escape($default_lang).'\''
				),
				'style'				=> array(
					'datatype'		=> 'VARCHAR(25)',
					'allow_null'	=> false,
					'default'		=> '\''.$db->escape($default_style).'\''
				),
				'num_posts'			=> array(
					'datatype'		=> 'INT(10) UNSIGNED',
					'allow_null'	=> false,
					'default'		=> '0'
				),
				'last_post'			=> array(
					'datatype'		=> 'INT(10) UNSIGNED',
					'allow_null'	=> true
				),
				'last_search'		=> array(
					'datatype'		=> 'INT(10) UNSIGNED',
					'allow_null'	=> true
				),
				'last_email_sent'	=> array(
					'datatype'		=> 'INT(10) UNSIGNED',
					'allow_null'	=> true
				),
				'last_report_sent'	=> array(
					'datatype'		=> 'INT(10) UNSIGNED',
					'allow_null'	=> true
				),
				'registered'		=> array(
					'datatype'		=> 'INT(10) UNSIGNED',
					'allow_null'	=> false,
					'default'		=> '0'
				),
				'registration_ip'	=> array(
					'datatype'		=> 'VARCHAR(39)',
					'allow_null'	=> false,
					'default'		=> '\'0.0.0.0\''
				),
				'last_visit'		=> array(
					'datatype'		=> 'INT(10) UNSIGNED',
					'allow_null'	=> false,
					'default'		=> '0'
				),
				'admin_note'		=> array(
					'datatype'		=> 'VARCHAR(30)',
					'allow_null'	=> true
				),
				'activate_string'	=> array(
					'datatype'		=> 'VARCHAR(80)',
					'allow_null'	=> true
				),
				'activate_key'		=> array(
					'datatype'		=> 'VARCHAR(8)',
					'allow_null'	=> true
				),
				'use_pm'		=> array(
					'datatype'		=> 'TINYINT(1)',
					'allow_null'	=> false,
					'default'		=> '1'
				),
				'notify_pm'		=> array(
					'datatype'		=> 'TINYINT(1)',
					'allow_null'	=> false,
					'default'		=> '1'
				),
				'notify_pm_full'=> array(
					'datatype'		=> 'TINYINT(1)',
					'allow_null'	=> false,
					'default'		=> '0'
				),
				'num_pms'	=> array(
					'datatype'		=> 'INT(10) UNSIGNED',
					'allow_null'	=> false,
					'default'		=> '0'
				),
				'first_run'		=> array(
					'datatype'		=> 'TINYINT(1)',
					'allow_null'	=> false,
					'default'		=> '0'
				),
				'color_scheme'	=> array(
					'datatype'		=> 'INT(25)',
					'allow_null'	=> false,
					'default'		=> '3'
				),
				'adapt_time'		=> array(
					'datatype'		=> 'TINYINT(1)',
					'allow_null'	=> false,
					'default'		=> '0'
				),
				'accent'	=> array(
					'datatype'		=> 'INT(25)',
					'allow_null'	=> false,
					'default'		=> '3'
				)
			),
			'PRIMARY KEY'	=> array('id'),
			'UNIQUE KEYS'	=> array(
				'username_idx'		=> array('username')
			),
			'INDEXES'		=> array(
				'registered_idx'	=> array('registered')
			)
		);
	
		if ($db_type == 'mysql' || $db_type == 'mysqli' || $db_type == 'mysql_innodb' || $db_type == 'mysqli_innodb')
			$schema['UNIQUE KEYS']['username_idx'] = array('username(25)');
	
		$db->create_table('users', $schema) or error('Unable to create users table', __FILE__, __LINE__, $db->error());
		
		$schema = array(
			'FIELDS'			=> array(
				'id'				=> array(
					'datatype'		=> 'SERIAL',
					'allow_null'	=> false
				),
				'shared_id'		=> array(
					'datatype'		=> 'INT(10)',
					'allow_null'	=> false,
					'default'		=> '0'
				),
				'last_shared_id'	=> array(
					'datatype'			=> 'INT(10)',
					'allow_null'		=> false,
					'default'			=> '0'
				),
				'last_post'			=> array(
					'datatype'			=> 'INT(10)',
					'allow_null'		=> true,
					'default'			=> '0'
				),
				'last_post_id'		=> array(
					'datatype'			=> 'INT(10)',
					'allow_null'		=> true,
					'default'			=> '0'
				),
				'last_poster'		=> array(
					'datatype'			=> 'VARCHAR(255)',
					'allow_null'		=> false,
					'default'			=> '0'
				),
				'owner'				=> array(
					'datatype'			=> 'INTEGER',
					'allow_null'		=> false,
					'default'			=> '0'
				),
				'subject'			=> array(
					'datatype'			=> 'VARCHAR(255)',
					'allow_null'		=> false
				),
				'message'			=> array(
					'datatype'			=> 'MEDIUMTEXT',
					'allow_null'		=> false
				),
				'hide_smilies'	=> array(
					'datatype'		=> 'TINYINT(1)',
					'allow_null'	=> false,
					'default'		=> '0'
				),
				'show_message'	=> array(
					'datatype'		=> 'TINYINT(1)',
					'allow_null'	=> false,
					'default'		=> '0'
				),
				'sender'	=> array(
					'datatype'		=> 'VARCHAR(200)',
					'allow_null'	=> false
				),
				'receiver'	=> array(
					'datatype'		=> 'VARCHAR(200)',
					'allow_null'	=> true
				),
				'sender_id'	=> array(
					'datatype'			=> 'INT(10)',
					'allow_null'		=> false,
					'default'			=> '0'
				),
				'receiver_id'	=> array(
					'datatype'			=> 'VARCHAR(255)',
					'allow_null'		=> true,
					'default'			=> '0'
				),
				'sender_ip'	=> array(
					'datatype'			=> 'VARCHAR(39)',
					'allow_null'		=> true
				),
				'posted'	=> array(
					'datatype'			=> 'INT(10)',
					'allow_null'		=> false,
				),
				'showed'	=> array(
					'datatype'			=> 'TINYINT(1)',
					'allow_null'		=> false,
					'default'			=> '0'
				)
			),
			'PRIMARY KEY'		=> array('id'),
		);
		
		$db->create_table('messages', $schema) or error('Unable to create messages table', __FILE__, __LINE__, $db->error());

		// Insert config data
		$luna_config = array(
			'o_cur_version'				=> Version::FORUM_VERSION,
			'o_core_version'			=> Version::FORUM_CORE_VERSION,
			'o_code_name'				=> Version::FORUM_CODE_NAME,
			'o_database_revision'		=> Version::FORUM_DB_VERSION,
			'o_searchindex_revision'	=> Version::FORUM_SI_VERSION,
			'o_parser_revision'			=> Version::FORUM_PARSER_VERSION,
			'o_board_title'				=> $title,
			'o_board_desc'				=> $description,
			'o_default_timezone'		=> 0,
			'o_time_format'				=> $lang['lang_time'],
			'o_date_format'				=> $lang['lang_date'],
			'o_timeout_visit'			=> 1800,
			'o_timeout_online'			=> 300,
			'o_show_user_info'			=> 1,
			'o_show_post_count'			=> 1,
			'o_signatures'				=> 1,
			'o_smilies'					=> 1,
			'o_smilies_sig'				=> 1,
			'o_make_links'				=> 1,
			'o_default_lang'			=> $default_lang,
			'o_default_style'			=> $default_style,
			'o_default_accent'			=> 3,
			'o_default_user_group'		=> 4,
			'o_topic_review'			=> 15,
			'o_disp_topics_default'		=> 30,
			'o_disp_posts_default'		=> 25,
			'o_indent_num_spaces'		=> 4,
			'o_quote_depth'				=> 3,
			'o_users_online'			=> 1,
			'o_censoring'				=> 0,
			'o_ranks'					=> 1,
			'o_has_posted'				=> 1,
			'o_topic_views'				=> 1,
			'o_gzip'					=> 0,
			'o_report_method'			=> 0,
			'o_regs_report'				=> 0,
			'o_default_email_setting'	=> 1,
			'o_mailing_list'			=> $email,
			'o_avatars'					=> $avatars,
			'o_avatars_dir'				=> 'img/avatars',
			'o_avatars_width'			=> 100,
			'o_avatars_height'			=> 100,
			'o_avatars_size'			=> 20480,
			'o_search_all_forums'		=> 1,
			'o_base_url'				=> $base_url,
			'o_admin_email'				=> $email,
			'o_webmaster_email'			=> $email,
			'o_forum_subscriptions'		=> 1,
			'o_topic_subscriptions'		=> 1,
			'o_first_run_message'		=> $lang['First run message'],
			'o_show_first_run'			=> 1,
			'o_first_run_guests'		=> 1,
			'o_first_run_backstage'		=> 0,
			'o_smtp_host'				=> NULL,
			'o_smtp_user'				=> NULL,
			'o_smtp_pass'				=> NULL,
			'o_smtp_ssl'				=> 0,
			'o_regs_allow'				=> 1,
			'o_regs_verify'				=> 0,
			'o_video_width'				=> 640,
			'o_video_height'			=> 360,
			'o_enable_advanced_search'	=> 1,
			'o_announcement'			=> 0,
			'o_announcement_message'	=> $lang['Announcement'],
			'o_rules'					=> 0,
			'o_rules_message'			=> $lang['Rules'],
			'o_maintenance'				=> 0,
			'o_maintenance_message'		=> $lang['Maintenance message'],
			'o_default_dst'				=> 0,
			'o_feed_type'				=> 2,
			'o_feed_ttl'				=> 0,
			'o_cookie_bar'				=> 0,
			'o_moderated_by'			=> 1,
			'o_admin_note'				=> '',
			'o_pms_enabled'				=> 1,
			'o_pms_mess_per_page'		=> 10,
			'o_pms_max_receiver'		=> 5,
			'o_pms_notification'		=> 1,
			'o_emoji'					=> 0,
			'o_emoji_size'				=> 16,
			'o_back_to_top'				=> 1,
			'o_show_copyright'			=> 1,
			'o_copyright_type'			=> 0,
			'o_custom_copyright'		=> NULL,
			'o_header_search'			=> 1,
			'o_board_statistics'		=> 1,
			'o_notification_flyout'		=> 1,
			'o_update_ring'				=> 1,
			'p_message_img_tag'			=> 1,
			'p_message_all_caps'		=> 1,
			'p_subject_all_caps'		=> 1,
			'p_sig_all_caps'			=> 1,
			'p_sig_img_tag'				=> 0,
			'p_sig_length'				=> 400,
			'p_sig_lines'				=> 4,
			'p_allow_banned_email'		=> 1,
			'p_allow_dupe_email'		=> 0,
			'p_force_guest_email'		=> 1
		);
	
		foreach ($luna_config as $conf_name => $conf_value) {
			$db->query('INSERT INTO '.$db_prefix.'config (conf_name, conf_value) VALUES(\''.$conf_name.'\', '.(is_null($conf_value) ? 'NULL' : '\''.$db->escape($conf_value).'\'').')')
				or error('Unable to insert into table '.$db_prefix.'config. Please check your configuration and try again', __FILE__, __LINE__, $db->error());
		}
		
		$db->end_transaction();

		return $db;
	}
	
	public static function insert_default_users($username, $password, $email, $language, $style) {
		global $db, $db_type, $lang;
		
		$now = time();
		
		$db->start_transaction();

		// Insert guest and first admin user
		$db->query('INSERT INTO '.$db->prefix.'users (group_id, username, password, email) VALUES(3, \''.$db->escape($lang['Guest']).'\', \''.$db->escape($lang['Guest']).'\', \''.$db->escape($lang['Guest']).'\')')
			or error('Unable to add guest user. Please check your configuration and try again', __FILE__, __LINE__, $db->error());
	
		$db->query('INSERT INTO '.$db->prefix.'users (group_id, username, password, email, language, style, num_posts, last_post, registered, registration_ip, last_visit) VALUES(1, \''.$db->escape($username).'\', \''.luna_hash($password).'\', \''.$email.'\', \''.$db->escape($language).'\', \''.$db->escape($style).'\', 1, '.$now.', '.$now.', \''.$db->escape(get_remote_address()).'\', '.$now.')')
			or error('Unable to add administrator user. Please check your configuration and try again', __FILE__, __LINE__, $db->error());
		
		$db->end_transaction();
	}
	
	public static function insert_default_groups() {
		global $db, $db_type, $lang;
		
		$now = time();
		
		$db->start_transaction();

		// Insert the first 4 groups
		$db->query('INSERT INTO '.$db->prefix.'groups ('.($db_type != 'pgsql' ? 'g_id, ' : '').'g_title, g_user_title, g_moderator, g_mod_edit_users, g_mod_rename_users, g_mod_change_passwords, g_mod_ban_users, g_read_board, g_view_users, g_post_replies, g_post_topics, g_edit_posts, g_delete_posts, g_delete_topics, g_set_title, g_search, g_search_users, g_send_email, g_post_flood, g_search_flood, g_email_flood, g_report_flood, g_soft_delete_view, g_soft_delete_posts, g_soft_delete_topics) VALUES('.($db_type != 'pgsql' ? '1, ' : '').'\''.$db->escape($lang['Administrators']).'\', \''.$db->escape($lang['Administrator']).'\', 0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 0, 0, 0, 0, 1, 1, 1)') or error('Unable to add group', __FILE__, __LINE__, $db->error());
	
		$db->query('INSERT INTO '.$db->prefix.'groups ('.($db_type != 'pgsql' ? 'g_id, ' : '').'g_title, g_user_title, g_moderator, g_mod_edit_users, g_mod_rename_users, g_mod_change_passwords, g_mod_ban_users, g_read_board, g_view_users, g_post_replies, g_post_topics, g_edit_posts, g_delete_posts, g_delete_topics, g_set_title, g_search, g_search_users, g_send_email, g_post_flood, g_search_flood, g_email_flood, g_report_flood, g_soft_delete_view, g_soft_delete_posts, g_soft_delete_topics) VALUES('.($db_type != 'pgsql' ? '2, ' : '').'\''.$db->escape($lang['Moderators']).'\', \''.$db->escape($lang['Moderator']).'\', 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 0, 0, 0, 0, 1, 1, 1)') or error('Unable to add group', __FILE__, __LINE__, $db->error());
	
		$db->query('INSERT INTO '.$db->prefix.'groups ('.($db_type != 'pgsql' ? 'g_id, ' : '').'g_title, g_user_title, g_moderator, g_mod_edit_users, g_mod_rename_users, g_mod_change_passwords, g_mod_ban_users, g_read_board, g_view_users, g_post_replies, g_post_topics, g_edit_posts, g_delete_posts, g_delete_topics, g_set_title, g_search, g_search_users, g_send_email, g_post_flood, g_search_flood, g_email_flood, g_report_flood, g_soft_delete_view, g_soft_delete_posts, g_soft_delete_topics) VALUES('.($db_type != 'pgsql' ? '3, ' : '').'\''.$db->escape($lang['Guests']).'\', NULL, 0, 0, 0, 0, 0, 1, 1, 0, 0, 0, 0, 0, 0, 1, 1, 0, 60, 30, 0, 0, 0, 0, 0)') or error('Unable to add group', __FILE__, __LINE__, $db->error());
	
		$db->query('INSERT INTO '.$db->prefix.'groups ('.($db_type != 'pgsql' ? 'g_id, ' : '').'g_title, g_user_title, g_moderator, g_mod_edit_users, g_mod_rename_users, g_mod_change_passwords, g_mod_ban_users, g_read_board, g_view_users, g_post_replies, g_post_topics, g_edit_posts, g_delete_posts, g_delete_topics, g_set_title, g_search, g_search_users, g_send_email, g_post_flood, g_search_flood, g_email_flood, g_report_flood, g_soft_delete_view, g_soft_delete_posts, g_soft_delete_topics) VALUES('.($db_type != 'pgsql' ? '4, ' : '').'\''.$db->escape($lang['Members']).'\', NULL, 0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 1, 0, 1, 1, 1, 60, 30, 60, 60, 0, 0, 0)') or error('Unable to add group', __FILE__, __LINE__, $db->error());
		
		$db->end_transaction();
	}
	
	public static function instert_default_menu() {
		global $db, $lang;
		
		$db->start_transaction();

		$db->query('INSERT INTO '.$db->prefix.'menu (url, name, disp_position, visible, sys_entry) VALUES(\'index.php\', \'Index\', 1, \'1\', 1)')
			or error('Unable to add Index menu item. Please check your configuration and try again', __FILE__, __LINE__, $db->error());
	
		$db->query('INSERT INTO '.$db->prefix.'menu (url, name, disp_position, visible, sys_entry) VALUES(\'userlist.php\', \'Users\', 2, \'1\', 1)')
			or error('Unable to add Users menu item. Please check your configuration and try again', __FILE__, __LINE__, $db->error());
	
		$db->query('INSERT INTO '.$db->prefix.'menu (url, name, disp_position, visible, sys_entry) VALUES(\'search.php\', \'Search\', 3, \'1\', 1)')
			or error('Unable to add Search menu item. Please check your configuration and try again', __FILE__, __LINE__, $db->error());
		
		$db->end_transaction();
	}
	
	public static function insert_default_data() {
		global $db, $db_type, $lang;
		
		$now = time();
		
		$db->start_transaction();

		$db->query('INSERT INTO '.$db->prefix.'ranks (rank, min_posts) VALUES(\''.$db->escape($lang['New member']).'\', 0)')
			or error('Unable to insert into table '.$db->prefix.'ranks. Please check your configuration and try again', __FILE__, __LINE__, $db->error());
	
		$db->query('INSERT INTO '.$db->prefix.'ranks (rank, min_posts) VALUES(\''.$db->escape($lang['Member']).'\', 10)')
			or error('Unable to insert into table '.$db->prefix.'ranks. Please check your configuration and try again', __FILE__, __LINE__, $db->error());

		require FORUM_ROOT.'include/general_functions.php';		
		new_notification('2', 'backstage/about.php', 'Welcome to Luna, discover the possibilities!', 'luni-logo', 'luni');
		
		$db->end_transaction();
	}
}