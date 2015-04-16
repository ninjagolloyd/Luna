<?php

/*
 * Copyright (C) 2013-2015 Luna
 * Based on code by FluxBB copyright (C) 2008-2012 FluxBB
 * Based on code by Rickard Andersson copyright (C) 2002-2008 PunBB
 * Licensed under GPLv3 (http://getluna.org/license.php)
 */
// Tell common.php that we don't want output buffering
define('FORUM_DISABLE_BUFFERING', 1);

define('FORUM_ROOT', '../');
require FORUM_ROOT.'include/common.php';

if (!$luna_user['is_admmod'])
	header("Location: login.php");
$action = isset($_REQUEST['action']) ? luna_trim($_REQUEST['action']) : '';

if ($action == 'rebuild') {
	$per_page = isset($_GET['i_per_page']) ? intval($_GET['i_per_page']) : 0;
	$start_at = isset($_GET['i_start_at']) ? intval($_GET['i_start_at']) : 0;

	// Check per page is > 0
	if ($per_page < 1)
		message_backstage($lang['Posts must be integer message']);

	@set_time_limit(0);

	// If this is the first cycle of posts we empty the search index before we proceed
	if (isset($_GET['i_empty_index'])) {
		confirm_referrer('backstage/maintenance.php');
	
		$db->truncate_table('search_matches') or error('Unable to empty search index match table', __FILE__, __LINE__, $db->error());
		$db->truncate_table('search_words') or error('Unable to empty search index words table', __FILE__, __LINE__, $db->error());

		// Reset the sequence for the search words (not needed for SQLite)
		switch ($db_type) {
			case 'mysql':
			case 'mysqli':
			case 'mysql_innodb':
			case 'mysqli_innodb':
				$result = $db->query('ALTER TABLE '.$db->prefix.'search_words auto_increment=1') or error('Unable to update table auto_increment', __FILE__, __LINE__, $db->error());
				break;

			case 'pgsql';
				$result = $db->query('SELECT setval(\''.$db->prefix.'search_words_id_seq\', 1, false)') or error('Unable to update sequence', __FILE__, __LINE__, $db->error());
		}
	}

	$page_title = array(luna_htmlspecialchars($luna_config['o_board_title']), $lang['Rebuilding search index']);

?>
<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title><?php echo generate_page_title($page_title) ?></title>
		<style type="text/css">
			body {
				font: 12px "Segoe UI Light", "Segoe UI", Arial, Helvetica, sans-serif;
				color: #333333;
				background-color: #FFFFFF
			}
			
			h1 {
				font-size: 16px;
				font-weight: normal;
			}
		</style>
	</head>
	<body>
		<h1><?php echo $lang['Rebuilding index info'] ?></h1>
		<hr />
<?php

	$query_str = '';

	require FORUM_ROOT.'include/search_idx.php';

	// Fetch posts to process this cycle
	$result = $db->query('SELECT p.id, p.message, t.subject, t.first_post_id FROM '.$db->prefix.'posts AS p INNER JOIN '.$db->prefix.'topics AS t ON t.id=p.topic_id WHERE p.id >= '.$start_at.' ORDER BY p.id ASC LIMIT '.$per_page) or error('Unable to fetch posts', __FILE__, __LINE__, $db->error());

	$end_at = 0;
	while ($cur_item = $db->fetch_assoc($result)) {
		echo '<p><span>'.sprintf($lang['Processing post'], $cur_item['id']).'</span></p>'."\n";

		if ($cur_item['id'] == $cur_item['first_post_id'])
			update_search_index('post', $cur_item['id'], $cur_item['message'], $cur_item['subject']);
		else
			update_search_index('post', $cur_item['id'], $cur_item['message']);

		$end_at = $cur_item['id'];
	}

	// Check if there is more work to do
	if ($end_at > 0) {
		$result = $db->query('SELECT id FROM '.$db->prefix.'posts WHERE id > '.$end_at.' ORDER BY id ASC LIMIT 1') or error('Unable to fetch next ID', __FILE__, __LINE__, $db->error());

		if ($db->num_rows($result) > 0)
			$query_str = '?action=rebuild&i_per_page='.$per_page.'&i_start_at='.$db->result($result);
	}

	$db->end_transaction();
	$db->close();

	exit('<script type="text/javascript">window.location="maintenance.php'.$query_str.'"</script><hr /><p>'.sprintf($lang['Javascript redirect failed'], '<a href="maintenance.php'.$query_str.'">'.$lang['Click here'].'</a>').'</p>');
}

// Get the first post ID from the db
$result = $db->query('SELECT id FROM '.$db->prefix.'posts ORDER BY id ASC LIMIT 1') or error('Unable to fetch topic info', __FILE__, __LINE__, $db->error());
if ($db->num_rows($result))
	$first_id = $db->result($result);

if (isset($_POST['form_sent'])) {
	confirm_referrer('backstage/maintenance.php');

	$form = array(
		'maintenance'			=> isset($_POST['form']['maintenance']) ? '1' : '0',
		'maintenance_message'	=> luna_trim($_POST['form']['maintenance_message']),
	);

	if ($form['maintenance_message'] != '')
		$form['maintenance_message'] = luna_linebreaks($form['maintenance_message']);
	else {
		$form['maintenance_message'] = $lang['Default maintenance message'];
		$form['maintenance'] = '0';
	}

	foreach ($form as $key => $input) {
		// Only update values that have changed
		if (array_key_exists('o_'.$key, $luna_config) && $luna_config['o_'.$key] != $input) {
			if ($input != '' || is_int($input))
				$value = '\''.$db->escape($input).'\'';
			else
				$value = 'NULL';

			$db->query('UPDATE '.$db->prefix.'config SET conf_value='.$value.' WHERE conf_name=\'o_'.$db->escape($key).'\'') or error('Unable to update board config', __FILE__, __LINE__, $db->error());
		}
	}

	if ($action == 'clear_cache') {
		confirm_referrer('backstage/maintenance.php');
	
		delete_all(FORUM_ROOT.'cache');
		redirect('backstage/maitenance.php?cache_cleared=true');
	}

	// Regenerate the config cache
	if (!defined('FORUM_CACHE_FUNCTIONS_LOADED'))
		require FORUM_ROOT.'include/cache.php';

	generate_config_cache();
	clear_feed_cache();

	redirect('backstage/maintenance.php?saved=true');
}

$page_title = array(luna_htmlspecialchars($luna_config['o_board_title']), $lang['Admin'], $lang['Maintenance']);
define('FORUM_ACTIVE_PAGE', 'admin');
require 'header.php';
	load_admin_nav('maintenance', 'maintenance');

if (isset($_GET['saved']))
	echo '<div class="alert alert-success"><h4>'.$lang['Settings saved'].'</h4></div>';
if (isset($_GET['cache_cleared']))
	echo '<div class="alert alert-success"><h4>'.$lang['Cache cleared'].'</h4></div>';
?>
<form class="form-horizontal" method="post" action="maintenance.php">
	<div class="panel panel-default">
		<div class="panel-heading">
			<h3 class="panel-title" id="maintenance"><?php echo $lang['Maintenance'] ?><span class="pull-right"><button class="btn btn-primary" type="submit" name="save"><span class="fa fa-fw fa-check"></span> <?php echo $lang['Save'] ?></button></span></h3>
		</div>
		<div class="panel-body">
			<input type="hidden" name="form_sent" value="1" />
			<fieldset>
				<div class="form-group">
					<label class="col-sm-3 control-label"><?php echo $lang['Maintenance'] ?><span class="help-block"><?php echo $lang['Maintenance message help'] ?></span></label>
					<div class="col-sm-9">
						<div class="checkbox">
							<label>
								<input type="checkbox" name="form[maintenance]" value="1" <?php if ($luna_config['o_maintenance'] == '1') echo ' checked' ?> />
								<?php echo $lang['Maintenance mode help'] ?>
							</label>
						</div>
						<textarea class="form-control" name="form[maintenance_message]" rows="10"><?php echo luna_htmlspecialchars($luna_config['o_maintenance_message']) ?></textarea>
					</div>
				</div>
			</fieldset>
		</div>
	</div>
</form>
<div class="panel panel-default form-horizontal">
	<div class="panel-heading">
		<h3 class="panel-title" id="cache"><?php echo $lang['Cache'] ?></h3>
	</div>
	<div class="panel-body">
		<div class="form-group">
			<label class="col-sm-3 control-label"><?php echo $lang['Cache'] ?><span class="help-block"><?php echo $lang['Cache info'] ?></span></label>
			<div class="col-sm-9">
				<a href="maintenance.php?cache_cleared=true" class="btn btn-danger"><span class="fa fa-fw fa-trash"></span> <?php echo $lang['Clear cache'] ?></a>
			</div>
		</div>
	</div>
</div>
<form class="form-horizontal" method="get" action="maintenance.php">
	<div class="panel panel-default">
		<div class="panel-heading">
			<h3 class="panel-title"><?php echo $lang['Rebuild index subhead'] ?><span class="pull-right"><button class="btn btn-primary" type="submit" name="rebuild_index"?><span class="fa fa-fw fa-repeat"></span> <?php echo $lang['Rebuild index'] ?></button></span></h3>
		</div>
		<div class="panel-body">
			<input type="hidden" name="action" value="rebuild" />
			<fieldset>
				<p><?php echo $lang['Rebuild index info'] ?></p>
				<div class="form-group">
					<label class="col-sm-3 control-label"><?php echo $lang['Posts per cycle label'] ?><span class="help-block"><?php echo $lang['Posts per cycle help'] ?></span></label>
					<div class="col-sm-9">
						<input type="text" class="form-control" name="i_per_page" maxlength="7" value="300" tabindex="1" />
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-3 control-label"><?php echo $lang['Starting post label'] ?><span class="help-block"><?php echo $lang['Starting post help'] ?></span></label>
					<div class="col-sm-9">
						<input type="text" class="form-control" name="i_start_at" maxlength="7" value="<?php echo (isset($first_id)) ? $first_id : 0 ?>" tabindex="2" />
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-3 control-label"><?php echo $lang['Empty index label'] ?></label>
					<div class="col-sm-9">
						<div class="checkbox">
							<label>
								<input type="checkbox" name="i_empty_index" value="1" tabindex="3" checked />
								<?php echo $lang['Empty index help'] ?></label>
							</label>
						</div>
					</div>
				</div>
				<p><?php echo $lang['Rebuild completed info'] ?></p>
			</fieldset>
		</div>
	</div>
</form>
<?php

require 'footer.php';