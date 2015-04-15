<?php

/*
 * Copyright (C) 2013-2015 Luna
 * Based on code by FluxBB copyright (C) 2008-2012 FluxBB
 * Based on code by Rickard Andersson copyright (C) 2002-2008 PunBB
 * Licensed under GPLv3 (http://getluna.org/license.php)
 */

// Make sure no one attempts to run this script "directly"
if (!defined('FORUM'))
	exit;

// Send no-cache headers
header('Expires: Thu, 21 Jul 1977 07:30:00 GMT'); // When yours truly first set eyes on this world! :)
header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache'); // For HTTP/1.0 compatibility

// Send the Content-type header in case the web server is setup to send something else
header('Content-type: text/html; charset=utf-8');

// Prevent site from being embedded in a frame 
header('X-Frame-Options: deny'); 

// Define $p if it's not set to avoid a PHP notice
$p = isset($p) ? $p : null;

// Generate avatar
$user_avatar = draw_user_avatar($luna_user['id'], 'avatar');

// Navbar data
$links = array();
$menu_title = $luna_config['o_board_title'];
$inbox_menu_item = '';

$num_new_pm = 0;
if ($luna_config['o_pms_enabled'] == '1' && $luna_user['g_pm'] == '1' && $luna_user['use_pm'] == '1') {
	// Check for new messages
	$result = $db->query('SELECT COUNT(id) FROM '.$db->prefix.'messages WHERE showed=0 AND show_message=1 AND owner='.$luna_user['id']) or error('Unable to check the availibility of new messages', __FILE__, __LINE__, $db->error());
	$num_new_pm = $db->result($result);
	
	if ($num_new_pm > 0)
		$new_inbox = $num_new_pm.' ';
	else
		$new_inbox = '';

	$inbox_menu_item = '<li><a href="inbox.php">'.$new_inbox.'<span class="fa fa-fw fa-paper-plane-o"></span><span class="visible-xs-inline"> Inbox</span></a></li>';
}

// Check for new notifications
$result = $db->query('SELECT COUNT(id) FROM '.$db->prefix.'notifications WHERE viewed = 0 AND user_id = '.$luna_user['id']) or error ('Unable to load notifications', __FILE__, __LINE__, $db->error());
$num_notifications = $db->result($result);

if ($luna_config['o_notification_flyout'] == 1) {
	if ($num_notifications == '0') {
		$notificon = '<span class="fa fa-fw fa-circle-o"></span>';
		$ind_notification[] = '<li><a href="notifications.php">'.$lang['No new notifications'].'</a></li>';
	} else {
		$notificon = $num_notifications.' <span class="fa fa-fw fa-circle"></span>';
		
		$notification_result = $db->query('SELECT * FROM '.$db->prefix.'notifications WHERE user_id = '.$luna_user['id'].' AND viewed = 0 ORDER BY time DESC LIMIT 10') or error ('Unable to load notifications', __FILE__, __LINE__, $db->error());
		while ($cur_notifi = $db->fetch_assoc($notification_result)) {
			$notifitime = format_time($cur_notifi['time'], false, null, $luna_config['o_time_format'], true, true);
			$ind_notification[] = '<li><a href="'.$cur_notifi['link'].'"><span class="fa fa-fw luni luni-fw '.$cur_notifi['icon'].'"></span> '.$cur_notifi['message'].' <span class="timestamp pull-right">'.$notifitime.'</span></a></li>';
		}
	}

	$notifications = implode('<li class="divider"></li>', $ind_notification);
	$notification_menu_item = '
					<li class="dropdown">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown">'.$notificon.'<span class="visible-xs-inline"> '.$lang['Notifications'].'</span></a>
					<ul class="dropdown-menu notification-menu">
						<li role="presentation" class="dropdown-header">'.$lang['Notifications'].'</li>
						<li class="divider"></li>
						'.$notifications.'
						<li class="divider"></li>
						<li><a class="pull-right" href="notifications.php">'.$lang['More'].' <i class="fa fa-fw fa-arrow-right"></i></a></li>
					</ul>
				</li>';
} else {
	if ($num_notifications == '0')
		$notificon = '<span class="fa fa-fw fa-circle-o"></span>';
	else
		$notificon = $num_notifications.' <span class="fa fa-fw fa-circle"></span>';

	$notification_menu_item = '<li><a href="notifications.php">'.$notificon.'<span class="visible-xs-inline"> '.$lang['Notifications'].'</span></a></li>';
}

// Generate navigation items
if (!$luna_user['is_admmod'])
	$backstage = '';
else
	$backstage = '<li><a href="backstage/"><span class="fa fa-fw fa-tachometer"></span><span class="visible-xs-inline"> '.$lang['Backstage'].'</span></a></li>';

$result = $db->query('SELECT id, url, name, disp_position, visible FROM '.$db->prefix.'menu ORDER BY disp_position') or error('Unable to fetch menu items', __FILE__, __LINE__, $db->error());

if ($luna_user['is_guest'])
	$usermenu = '<li id="navregister"'.((FORUM_ACTIVE_PAGE == 'register') ? ' class="active"' : '').'><a href="register.php">'.$lang['Register'].'</a></li>
				 <li><a href="#" data-toggle="modal" data-target="#login-form">'.$lang['Login'].'</a></li>';
else
	$usermenu = $backstage.$notification_menu_item.$inbox_menu_item.'
				<li class="dropdown">
					<a href="#" class="dropdown-toggle avatar-item" data-toggle="dropdown">'.luna_htmlspecialchars($luna_user['username']).' '.$user_avatar.' <span class="fa fa-fw fa-angle-down"></span></a>
					<ul class="dropdown-menu">
						<li><a href="profile.php?id='.$luna_user['id'].'">'.$lang['Profile'].'</a></li>
						<li><a href="settings.php">'.$lang['Settings'].'</a></li>
						<li class="divider"></li>
						<li><a href="help.php">'.$lang['Help'].'</a></li>
						<li class="divider"></li>
						<li><a href="login.php?action=out&amp;id='.$luna_user['id'].'&amp;csrf_token='.luna_hash($luna_user['id'].luna_hash(get_remote_address())).'">'.$lang['Logout'].'</a></li>
					</ul>
				</li>
	';

if ($db->num_rows($result) > 0)
	while ($cur_item = $db->fetch_assoc($result))
		if ($cur_item['visible'] == '1')
			$links[] = '<li><a href="'.$cur_item['url'].'">'.$cur_item['name'].'</a></li>';