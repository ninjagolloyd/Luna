<?php

/*
 * Copyright (C) 2013-2015 Luna
 * Based on code by FluxBB copyright (C) 2008-2012 FluxBB
 * Based on code by Rickard Andersson copyright (C) 2002-2008 PunBB
 * Licensed under GPLv3 (http://getluna.org/license.php)
 */

define('FORUM_ROOT', dirname(__FILE__).'/');
require FORUM_ROOT.'include/common.php';

// Load the me functions script
require FORUM_ROOT.'include/me_functions.php';

// Include UTF-8 function
require FORUM_ROOT.'include/utf8/substr_replace.php';
require FORUM_ROOT.'include/utf8/ucwords.php'; // utf8_ucwords needs utf8_substr_replace
require FORUM_ROOT.'include/utf8/strcasecmp.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id < 2)
	message($lang['Bad request'], false, '404 Not Found');

$result = $db->query('SELECT u.username, u.email, u.title, u.realname, u.url, u.facebook, u.msn, u.twitter, u.google, u.location, u.signature, u.disp_topics, u.disp_posts, u.email_setting, u.notify_with_post, u.auto_notify, u.show_smilies, u.show_img, u.show_img_sig, u.show_avatars, u.show_sig, u.timezone, u.dst, u.language, u.style, u.num_posts, u.last_post, u.registered, u.registration_ip, u.admin_note, u.date_format, u.time_format, u.last_visit, u.color_scheme, u.accent, g.g_id, g.g_user_title, g.g_moderator FROM '.$db->prefix.'users AS u LEFT JOIN '.$db->prefix.'groups AS g ON g.g_id=u.group_id WHERE u.id='.$id) or error('Unable to fetch user info', __FILE__, __LINE__, $db->error());
if (!$db->num_rows($result))
	message($lang['Bad request'], false, '404 Not Found');

$user = $db->fetch_assoc($result);

$last_post = format_time($user['last_post']);

$user_personality = array();

$user_username = luna_htmlspecialchars($user['username']);
$user_usertitle = get_title($user);
$avatar_field = generate_avatar_markup($id);
$avatar_user_card = draw_user_avatar($id, 'visible-lg-block');

$user_title_field = get_title($user);
$user_personality[] = '<b>'.$lang['Title'].':</b> '.(($luna_config['o_censoring'] == '1') ? censor_words($user_title_field) : $user_title_field);

$user_personality[] = '<b>'.$lang['Posts table'].':</b> '.$posts_field = forum_number_format($user['num_posts']);

if ($user['num_posts'] > 0)
	$user_personality[] = '<b>'.$lang['Last post'].':</b> '.$last_post;

$user_activity[] = '<b>'.$lang['Registered table'].':</b> '.format_time($user['registered'], true);

$user_personality[] = '<b>'.$lang['Registered'].':</b> '.format_time($user['registered'], true);

$user_personality[] = '<b>'.$lang['Last visit info'].':</b> '.format_time($user['last_visit'], true);

if ($user['realname'] != '')
	$user_personality[] = '<b>'.$lang['Realname'].':</b> '.luna_htmlspecialchars(($luna_config['o_censoring'] == '1') ? censor_words($user['realname']) : $user['realname']);

if ($user['location'] != '')
	$user_personality[] = '<b>'.$lang['Location'].':</b> '.luna_htmlspecialchars(($luna_config['o_censoring'] == '1') ? censor_words($user['location']) : $user['location']);

$posts_field = '';
if ($luna_user['g_search'] == '1') {
	$quick_searches = array();
	if ($user['num_posts'] > 0) {
		$quick_searches[] = '<a class="btn btn-primary btn-sm" href="search.php?action=show_user_topics&amp;user_id='.$id.'">'.$lang['Show topics'].'</a>';
		$quick_searches[] = '<a class="btn btn-primary btn-sm" href="search.php?action=show_user_posts&amp;user_id='.$id.'">'.$lang['Show posts'].'</a>';
	}
	if ($luna_user['is_admmod'] && $luna_config['o_topic_subscriptions'] == '1')
		$quick_searches[] = '<a class="btn btn-primary btn-sm" href="search.php?action=show_subscriptions&amp;user_id='.$id.'">'.$lang['Show subscriptions'].'</a>';

	if (!empty($quick_searches))
		$posts_field .= implode('', $quick_searches);
}

if ($posts_field != '')
	$user_personality[] = '<br /><div class="btn-group">'.$posts_field.'</div>';

$user_messaging = array();

if ($user['email_setting'] == '0' && !$luna_user['is_guest'] && $luna_user['g_send_email'] == '1')
	$user_messaging[] = '<div class="input-group"><a href="mailto:'.luna_htmlspecialchars($user['email']).'" class="input-group-addon" id="mail-addon"><span class="fa fa-fw fa-envelope-o"></span></a><input type="text" class="form-control" value="'.luna_htmlspecialchars($user['email']).'" aria-describedby="mail-addon" readonly></div>';

elseif ($user['email_setting'] == '1' && !$luna_user['is_guest'] && $luna_user['g_send_email'] == '1')
	$user_messaging[] = '<a class="btn btn-default btn-block" href="misc.php?email='.$id.'"><span class="fa fa-fw fa-send-o"></span> '.$lang['Send email'].'</a>';

if ($user['url'] != '')
	$user_messaging[] = '<div class="input-group"><a href="'.luna_htmlspecialchars(($luna_config['o_censoring'] == '1') ? censor_words($user['url']) : $user['url']).'" class="input-group-addon" id="website-addon"><span class="fa fa-fw fa-link"></span></a><input type="text" class="form-control" value="'.luna_htmlspecialchars(($luna_config['o_censoring'] == '1') ? censor_words($user['url']) : $user['url']).'" aria-describedby="website-addon" readonly></div>';

if ($user['msn'] != '')
	$user_messaging[] = '<div class="input-group"><a href="mailto:'.luna_htmlspecialchars(($luna_config['o_censoring'] == '1') ? censor_words($user['msn']) : $user['msn']).'" class="input-group-addon" id="microsoft-addon"><span class="fa fa-fw fa-windows"></span></a><input type="text" class="form-control" value="'.luna_htmlspecialchars(($luna_config['o_censoring'] == '1') ? censor_words($user['msn']) : $user['msn']).'" aria-describedby="microsoft-addon" readonly></div>';

if ($user['facebook'] != '')
	$user_messaging[] = '<div class="input-group"><a href="http://facebook.com/'.luna_htmlspecialchars(($luna_config['o_censoring'] == '1') ? censor_words($user['facebook']) : $user['facebook']).'" class="input-group-addon" id="facebook-addon"><span class="fa fa-fw fa-facebook-square"></span></a><input type="text" class="form-control" value="'.luna_htmlspecialchars(($luna_config['o_censoring'] == '1') ? censor_words($user['facebook']) : $user['facebook']).'" aria-describedby="facebook-addon" readonly></div>';

if ($user['twitter'] != '')
	$user_messaging[] = '<div class="input-group"><a href="http://twitter.com/'.luna_htmlspecialchars(($luna_config['o_censoring'] == '1') ? censor_words($user['twitter']) : $user['twitter']).'" class="input-group-addon" id="twitter-addon"><span class="fa fa-fw fa-twitter"></span></a><input type="text" class="form-control" value="'.luna_htmlspecialchars(($luna_config['o_censoring'] == '1') ? censor_words($user['twitter']) : $user['twitter']).'" aria-describedby="twitter-addon" readonly></div>';

if ($user['google'] != '')
	$user_messaging[] = '<div class="input-group"><a href="http://plus.google.com/'.luna_htmlspecialchars(($luna_config['o_censoring'] == '1') ? censor_words($user['google']) : $user['google']).'" class="input-group-addon" id="google-addon"><span class="fa fa-fw fa-google-plus"></span></a><input type="text" class="form-control" value="'.luna_htmlspecialchars(($luna_config['o_censoring'] == '1') ? censor_words($user['google']) : $user['google']).'" aria-describedby="google-addon" readonly></div>';

$user_activity = array();

if ($user['signature'] != '') {
	require FORUM_ROOT.'include/parser.php';
	$parsed_signature = parse_signature($user['signature']);
}

$last_post = format_time($user['last_post']);

if ($user['signature'] != '') {
	$parsed_signature = parse_signature($user['signature']);
}

if (($luna_config['o_signatures'] == '1') && (isset($parsed_signature)))
	$user_signature = $parsed_signature;

// View or edit?
$page_title = array(luna_htmlspecialchars($luna_config['o_board_title']).' / '.$lang['Profile']);
define('FORUM_ACTIVE_PAGE', 'me');
require load_page('header.php');

require load_page('profile.php');

require load_page('footer.php');