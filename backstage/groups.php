<?php

/*
 * Copyright (C) 2013-2015 Luna
 * Based on code by FluxBB copyright (C) 2008-2012 FluxBB
 * Based on code by Rickard Andersson copyright (C) 2002-2008 PunBB
 * Licensed under GPLv3 (http://getluna.org/license.php)
 */

define('FORUM_ROOT', '../');
require FORUM_ROOT.'include/common.php';

if (!$luna_user['is_admmod'])
	header("Location: login.php");
// Add/edit a group (stage 1)
if (isset($_POST['add_group']) || isset($_GET['edit_group'])) {
	if (isset($_POST['add_group'])) {
		$base_group = intval($_POST['base_group']);

		$result = $db->query('SELECT * FROM '.$db->prefix.'groups WHERE g_id='.$base_group) or error('Unable to fetch user group info', __FILE__, __LINE__, $db->error());
		$group = $db->fetch_assoc($result);

		$mode = 'add';
	} else { // We are editing a group
		$group_id = intval($_GET['edit_group']);
		if ($group_id < 1)
			message_backstage(__('Bad request. The link you followed is incorrect, outdated or you\'re simply not allowed to hang around here.', 'luna'), false, '404 Not Found');
	
		$result = $db->query('SELECT * FROM '.$db->prefix.'groups WHERE g_id='.$group_id) or error('Unable to fetch user group info', __FILE__, __LINE__, $db->error());
		if (!$db->num_rows($result))
			message_backstage(__('Bad request. The link you followed is incorrect, outdated or you\'re simply not allowed to hang around here.', 'luna'), false, '404 Not Found');

		$group = $db->fetch_assoc($result);

		$mode = 'edit';
	}

	$page_title = array(luna_htmlspecialchars($luna_config['o_board_title']), __('Admin', 'luna'), __('User groups', 'luna'));
	$required_fields = array('req_title' => __('Group title', 'luna'));
	$focus_element = array('groups2', 'req_title');
	define('FORUM_ACTIVE_PAGE', 'admin');
	require 'header.php';
	load_admin_nav('users', 'groups');
?>
<form class="form-horizontal" id="groups2" method="post" action="groups.php" onsubmit="return process_form(this)">
	<div class="panel panel-default">
		<div class="panel-heading">
			<h3 class="panel-title"><?php echo __('Setup group options and permissions', 'luna') ?><span class="pull-right"><input class="btn btn-primary" type="submit" name="add_edit_group" value="<?php echo __('Save', 'luna') ?>" /></span></h3>
		</div>
		<div class="panel-body">
			<input type="hidden" name="mode" value="<?php echo $mode ?>" />
		<?php if ($mode == 'edit'): ?>					<input type="hidden" name="group_id" value="<?php echo $group_id ?>" />
		<?php endif; ?><?php if ($mode == 'add'): ?>					<input type="hidden" name="base_group" value="<?php echo $base_group ?>" />
		<?php endif; ?>					<fieldset>
				<p><?php echo __('Below options and permissions are the default permissions for the user group. These options apply if no forum specific permissions are in effect.', 'luna') ?></p>
				<div class="form-group">
					<label class="col-sm-3 control-label"><?php echo __('Group title', 'luna') ?></label>
					<div class="col-sm-9">
						<input type="text" class="form-control" name="req_title" maxlength="50" value="<?php if ($mode == 'edit') echo luna_htmlspecialchars($group['g_title']); ?>" tabindex="1" />
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-3 control-label"><?php echo __('User title', 'luna') ?><span class="help-block"><?php echo __('The title will override the user rank', 'luna') ?></span></label>
					<div class="col-sm-9">
						<input type="text" class="form-control" name="user_title" maxlength="50" value="<?php echo luna_htmlspecialchars($group['g_user_title']) ?>" tabindex="2" />
					</div>
				</div>
	<?php if ($group['g_id'] != FORUM_ADMIN): if ($group['g_id'] != FORUM_GUEST): if ($mode != 'edit' || $luna_config['o_default_user_group'] != $group['g_id']): ?>
				<hr />
				<div class="form-group">
					<label class="col-sm-3 control-label"> <?php echo __('Moderator privileges', 'luna') ?></label>
					<div class="col-sm-9">
						<div class="checkbox">
							<label>
								<input type="checkbox" name="moderator" value="1"<?php if ($group['g_moderator'] == '1') echo ' checked' ?> tabindex="5" />
								<?php echo __('In order for a user to have moderator abilities, they must be assigned to moderate one or more forums. This is done via the user administration page of the user\'s profile.', 'luna') ?>
							</label>
						</div>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-3 control-label"><?php echo __('Edit user profiles', 'luna') ?></label>
					<div class="col-sm-9">
						<div class="checkbox">
							<label>
								<input type="checkbox" name="mod_edit_users" value="1"<?php if ($group['g_mod_edit_users'] == '1') echo ' checked' ?> tabindex="7" />
								<?php echo __('If moderator privileges are enabled, allow users to edit user profiles.', 'luna') ?>
							</label>
						</div>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-3 control-label"><?php echo __('Rename users', 'luna') ?></label>
					<div class="col-sm-9">
						<div class="checkbox">
							<label>
								<input type="checkbox" name="mod_rename_users" value="1"<?php if ($group['g_mod_rename_users'] == '1') echo ' checked' ?> tabindex="9" />
								<?php echo __('If moderator privileges are enabled, allow users to rename users.', 'luna') ?>
							</label>
						</div>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-3 control-label"><?php echo __('Change passwords', 'luna') ?></label>
					<div class="col-sm-9">
						<div class="checkbox">
							<label>
								<input type="checkbox" name="mod_change_passwords" value="1"<?php if ($group['g_mod_change_passwords'] == '1') echo ' checked' ?> tabindex="11" />
								<?php echo __('If moderator privileges are enabled, allow users to change user passwords.', 'luna') ?>
							</label>
						</div>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-3 control-label"><?php echo __('Ban users', 'luna') ?></label>
					<div class="col-sm-9">
						<div class="checkbox">
							<label>
								<input type="checkbox" name="mod_ban_users" value="1"<?php if ($group['g_mod_ban_users'] == '1') echo ' checked' ?> tabindex="13" />
								<?php echo __('If moderator privileges are enabled, allow users to ban users.', 'luna') ?>
							</label>
						</div>
					</div>
				</div>
	<?php endif; endif; ?>
	<?php if ($group['g_id'] != FORUM_ADMIN): if ($group['g_id'] != FORUM_GUEST): ?>
				<hr />
				<div class="form-group">
					<label class="col-sm-3 control-label"><?php echo __('Show deleted content', 'luna') ?></label>
					<div class="col-sm-9">
						<div class="checkbox">
							<label>
								<input type="checkbox" name="soft_delete_view" value="1" <?php if ($group['g_soft_delete_view'] == '1') echo ' checked' ?> />
								<?php echo __('Allow users to view topics and posts that have been soft deleted.', 'luna') ?>
							</label>
						</div>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-3 control-label"><?php echo __('Soft delete posts', 'luna') ?></label>
					<div class="col-sm-9">
						<div class="checkbox">
							<label>
								<input type="checkbox" name="soft_delete_posts" value="1" <?php if ($group['g_soft_delete_posts'] == '1') echo ' checked' ?> />
								<?php echo __('Allow users to soft delete posts from all users.', 'luna') ?>
							</label>
						</div>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-3 control-label"><?php echo __('Soft delete topics', 'luna') ?></label>
					<div class="col-sm-9">
						<div class="checkbox">
							<label>
								<input type="checkbox" name="soft_delete_topics" value="1" <?php if ($group['g_soft_delete_topics'] == '1') echo ' checked' ?> />
								<?php echo __('Allow users to soft delete topics from all users.', 'luna') ?>
							</label>
						</div>
					</div>
				</div>
	<?php endif; endif; ?>
	<?php if ($group['g_id'] != FORUM_ADMIN): if ($group['g_id'] != FORUM_GUEST): ?>
				<hr />
				<div class="form-group">
					<label class="col-sm-3 control-label"><?php echo __('Use Inbox', 'luna') ?></label>
					<div class="col-sm-9">
						<div class="checkbox">
							<label>
								<input type="checkbox" name="inbox_allow" value="1" <?php if ($group['g_pm'] == '1') echo ' checked' ?> />
								<?php echo __('Allow users to send messages with Inbox.', 'luna') ?>
							</label>
						</div>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-3 control-label"><?php echo __('Inbox', 'luna') ?></label>
					<div class="col-sm-9">
						<input type="text" class="form-control" name="inbox_limit" maxlength="5" value="<?php echo $group['g_pm_limit'] ?>" />
						<p class="help-block"><?php echo __('The maximum amount of messages a user in this group can have in his Inbox. 0 is no limit.', 'luna') ?></p>
					</div>
				</div>
	<?php endif; endif; ?>
				<hr />
				<div class="form-group">
					<label class="col-sm-3 control-label"><?php echo __('Read board', 'luna') ?></label>
					<div class="col-sm-9">
						<div class="checkbox">
							<label>
								<input type="checkbox" name="read_board" value="1"<?php if ($group['g_read_board'] == '1') echo ' checked' ?> tabindex="15" />
								<?php echo __('If this is disabled, users will only be able to login and logout.', 'luna') ?>
							</label>
						</div>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-3 control-label"><?php echo __('View user information', 'luna') ?></label>
					<div class="col-sm-9">
						<div class="checkbox">
							<label>
								<input type="checkbox" name="view_users" value="1"<?php if ($group['g_view_users'] == '1') echo ' checked' ?> tabindex="17" />
								<?php echo __('Allow users to view the user list and user profiles.', 'luna') ?>
							</label>
						</div>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-3 control-label"><?php echo __('Post replies', 'luna') ?></label>
					<div class="col-sm-9">
						<div class="checkbox">
							<label>
								<input type="checkbox" name="post_replies" value="1"<?php if ($group['g_post_replies'] == '1') echo ' checked' ?> tabindex="19" />
								<?php echo __('Allow users to post replies in topics.', 'luna') ?>
							</label>
						</div>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-3 control-label"><?php echo __('Post topics', 'luna') ?></label>
					<div class="col-sm-9">
						<div class="checkbox">
							<label>
								<input type="checkbox" name="post_topics" value="1"<?php if ($group['g_post_topics'] == '1') echo ' checked' ?> tabindex="21" />
								<?php echo __('Allow users to post new topics.', 'luna') ?>
							</label>
						</div>
					</div>
				</div>
	<?php if ($group['g_id'] != FORUM_GUEST): ?>
				<hr />
				<div class="form-group">
					<label class="col-sm-3 control-label"><?php echo __('Edit posts', 'luna') ?></label>
					<div class="col-sm-9">
						<div class="checkbox">
							<label>
								<input type="checkbox" name="edit_posts" value="1"<?php if ($group['g_edit_posts'] == '1') echo ' checked' ?> tabindex="23" />
								<?php echo __('Allow users to edit their own posts.', 'luna') ?>
							</label>
						</div>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-3 control-label"><?php echo __('Delete posts', 'luna') ?></label>
					<div class="col-sm-9">
						<div class="checkbox">
							<label>
								<input type="checkbox" name="delete_posts" value="1"<?php if ($group['g_delete_posts'] == '1') echo ' checked' ?> tabindex="25" />
								<?php echo __('Allow users to delete their own posts.', 'luna') ?>
							</label>
						</div>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-3 control-label"><?php echo __('Delete topics', 'luna') ?></label>
					<div class="col-sm-9">
						<div class="checkbox">
							<label>
								<input type="checkbox" name="delete_topics" value="1"<?php if ($group['g_delete_topics'] == '1') echo ' checked' ?> tabindex="27" />
								<?php echo __('Allow users to delete their own topics (including any replies).', 'luna') ?>
							</label>
						</div>
					</div>
				</div>
	<?php endif;
	if ($group['g_id'] != FORUM_GUEST): ?>
				<hr />
				<div class="form-group">
					<label class="col-sm-3 control-label"><?php echo __('Set own user title', 'luna') ?></label>
					<div class="col-sm-9">
						<div class="checkbox">
							<label>
								<input type="checkbox" name="set_title" value="1"<?php if ($group['g_set_title'] == '1') echo ' checked' ?> tabindex="31" />
								<?php echo __('Allow users to set their own user title.', 'luna') ?>
							</label>
						</div>
					</div>
				</div>
	<?php endif; ?>
				<hr />
				<div class="form-group">
					<label class="col-sm-3 control-label"><?php echo __('Use search', 'luna') ?></label>
					<div class="col-sm-9">
						<div class="checkbox">
							<label>
								<input type="checkbox" name="search" value="1"<?php if ($group['g_search'] == '1') echo ' checked' ?> tabindex="33" />
								<?php echo __('Allow users to use the search feature.', 'luna') ?>
							</label>
						</div>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-3 control-label"><?php echo __('Search user list', 'luna') ?></label>
					<div class="col-sm-9">
						<div class="checkbox">
							<label>
								<input type="checkbox" name="search_users" value="1"<?php if ($group['g_search_users'] == '1') echo ' checked' ?> tabindex="35" />
								<?php echo __('Allow users to search for other users in the user list.', 'luna') ?>
							</label>
						</div>
					</div>
				</div>
	<?php if ($group['g_id'] != FORUM_GUEST): ?>
				<hr />
				<div class="form-group">
					<label class="col-sm-3 control-label"><?php echo __('Send e-mails', 'luna') ?></label>
					<div class="col-sm-9">
						<div class="checkbox">
							<label>
								<input type="checkbox" name="send_email" value="1"<?php if ($group['g_send_email'] == '1') echo ' checked' ?> tabindex="37" />
								<?php echo __('Allow users to send e-mails to other users.', 'luna') ?>
							</label>
						</div>
					</div>
				</div>
	<?php endif; ?>
				<hr />
				<div class="form-group">
					<label class="col-sm-3 control-label"><?php echo __('Post flood interval', 'luna') ?><span class="help-block"><?php echo __('Time users have to wait between posts', 'luna') ?></span></label>
					<div class="col-sm-9">
						<div class="input-group">
							<input type="text" class="form-control" name="post_flood" maxlength="4" value="<?php echo $group['g_post_flood'] ?>" tabindex="35" />
							<span class="input-group-addon"><?php echo __('seconds', 'luna') ?></span>
						</div>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-3 control-label"><?php echo __('Search flood interval', 'luna') ?><span class="help-block"><?php echo __('Time users have to wait between searches', 'luna') ?></span></label>
					<div class="col-sm-9">
						<div class="input-group">
							<input type="text" class="form-control" name="search_flood" maxlength="4" value="<?php echo $group['g_search_flood'] ?>" tabindex="36" />
							<span class="input-group-addon"><?php echo __('seconds', 'luna') ?></span>
						</div>
					</div>
				</div>
	<?php if ($group['g_id'] != FORUM_GUEST): ?>
				<div class="form-group">
					<label class="col-sm-3 control-label"><?php echo __('Email flood interval', 'luna') ?><span class="help-block"><?php echo __('Time users have to wait between emails', 'luna') ?></span></label>
					<div class="col-sm-9">
						<div class="input-group">
							<input type="text" class="form-control" name="email_flood" maxlength="4" value="<?php echo $group['g_email_flood'] ?>" tabindex="37" />
							<span class="input-group-addon"><?php echo __('seconds', 'luna') ?></span>
						</div>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-3 control-label"><?php echo __('Report flood interval', 'luna') ?><span class="help-block"><?php echo __('Time users have to wait between reports', 'luna') ?></span></label>
					<div class="col-sm-9">
						<div class="input-group">
							<input type="text" class="form-control" name="report_flood" maxlength="4" value="<?php echo $group['g_report_flood'] ?>" tabindex="38" />
							<span class="input-group-addon"><?php echo __('seconds', 'luna') ?></span>
						</div>
					</div>
				</div>
	<?php endif; endif; ?>
	<?php if ($group['g_moderator'] == '1' ): ?>							<p class="warntext"><?php echo __('Please note that in order for a user to have moderator abilities, they must be assigned to moderate one or more forums. This is done via the user administration page of the user\'s profile.', 'luna') ?></p>
	<?php endif; ?>	
			</fieldset>
		</div>
	</div>
</form>
<?php

	require 'footer.php';
}


// Add/edit a group (stage 2)
elseif (isset($_POST['add_edit_group'])) {
	confirm_referrer('backstage/groups.php');

	$title = luna_trim($_POST['req_title']);
	$user_title = luna_trim($_POST['user_title']);

	if ($_POST['group_id'] != FORUM_ADMIN) {
		$moderator = isset($_POST['moderator']) ? '1' : '0';
		$mod_edit_users = $moderator == '1' && isset($_POST['mod_edit_users']) ? '1' : '0';
		$mod_rename_users = $moderator == '1' && isset($_POST['mod_rename_users']) ? '1' : '0';
		$mod_change_passwords = $moderator == '1' && isset($_POST['mod_change_passwords']) ? '1' : '0';
		$mod_ban_users = $moderator == '1' && isset($_POST['mod_ban_users']) ? '1' : '0';
		$inbox_allow = isset($_POST['inbox_allow']) ? '1' : '0';
		$inbox_limit = (isset($_POST['inbox_limit']) && $_POST['inbox_limit'] >= 0) ? intval($_POST['inbox_limit']) : '0';
		$read_board = isset($_POST['read_board']) ? '1' : '0';
		$view_users = isset($_POST['view_users']) ? '1' : '0';
		$post_replies = isset($_POST['post_replies']) ? '1' : '0';
		$post_topics = isset($_POST['post_topics']) ? '1' : '0';
		$edit_posts = isset($_POST['edit_posts']) ? '1' : '0';
		$delete_posts = isset($_POST['delete_posts']) ? '1' : '0';
		$delete_topics = isset($_POST['delete_topics']) ? '1' : '0';
		$set_title = isset($_POST['set_title']) ? '1' : '0';
		$search = isset($_POST['search']) ? '1' : '0';
		$search_users = isset($_POST['search_users']) ? '1' : '0';
		$send_email = isset($_POST['send_email']) ? '1' : '0';
		$post_flood = (isset($_POST['post_flood']) && $_POST['post_flood'] >= 0) ? intval($_POST['post_flood']) : '0';
		$search_flood = (isset($_POST['search_flood']) && $_POST['search_flood'] >= 0) ? intval($_POST['search_flood']) : '0';
		$email_flood = (isset($_POST['email_flood']) && $_POST['email_flood'] >= 0) ? intval($_POST['email_flood']) : '0';
		$report_flood = (isset($_POST['report_flood']) && $_POST['report_flood'] >= 0) ? intval($_POST['report_flood']) : '0';
		$soft_delete_view = isset($_POST['soft_delete_view']) ? '1' : '0';
		$soft_delete_posts = isset($_POST['soft_delete_posts']) ? '1' : '0';
		$soft_delete_topics = isset($_POST['soft_delete_topics']) ? '1' : '0';
	} else {
		$mod_edit_users = $mod_rename_users = $mod_change_passwords = $mod_ban_users = $read_board = $view_users = $post_replies = $post_topics = $edit_posts = $delete_posts = $delete_topics = $set_title = $search = $search_users = $send_email = $soft_delete_view = $soft_delete_posts = $soft_delete_topics = '1';
		$moderator = $post_flood = $search_flood = $email_flood = $report_flood = '0';
	}

	if ($title == '')
		message_backstage(__('You must enter a title.', 'luna'));

	$user_title = ($user_title != '') ? '\''.$db->escape($user_title).'\'' : 'NULL';

	if ($_POST['mode'] == 'add') {
		$result = $db->query('SELECT 1 FROM '.$db->prefix.'groups WHERE g_title=\''.$db->escape($title).'\'') or error('Unable to check group title collision', __FILE__, __LINE__, $db->error());
		if ($db->num_rows($result))
			message_backstage(sprintf(__('There is already a group with the title <strong>%s</strong>.', 'luna'), luna_htmlspecialchars($title)));

		$db->query('INSERT INTO '.$db->prefix.'groups (g_title, g_user_title, g_moderator, g_mod_edit_users, g_mod_rename_users, g_mod_change_passwords, g_mod_ban_users, g_read_board, g_view_users, g_post_replies, g_post_topics, g_edit_posts, g_delete_posts, g_delete_topics, g_set_title, g_search, g_search_users, g_send_email, g_post_flood, g_search_flood, g_email_flood, g_report_flood, g_pm, g_pm_limit, g_soft_delete_view, g_soft_delete_posts, g_soft_delete_topics) VALUES(\''.$db->escape($title).'\', '.$user_title.', '.$moderator.', '.$mod_edit_users.', '.$mod_rename_users.', '.$mod_change_passwords.', '.$mod_ban_users.', '.$read_board.', '.$view_users.', '.$post_replies.', '.$post_topics.', '.$edit_posts.', '.$delete_posts.', '.$delete_topics.', '.$set_title.', '.$search.', '.$search_users.', '.$send_email.', '.$post_flood.', '.$search_flood.', '.$email_flood.', '.$report_flood.', '.$inbox_allow.', '.$inbox_limit.', '.$soft_delete_view.', '.$soft_delete_posts.', '.$soft_delete_topics.')') or error('Unable to add group', __FILE__, __LINE__, $db->error());
		$new_group_id = $db->insert_id();

		// Now lets copy the forum specific permissions from the group which this group is based on
		$result = $db->query('SELECT forum_id, read_forum, post_replies, post_topics FROM '.$db->prefix.'forum_perms WHERE group_id='.intval($_POST['base_group'])) or error('Unable to fetch group forum permission list', __FILE__, __LINE__, $db->error());
		while ($cur_forum_perm = $db->fetch_assoc($result))
			$db->query('INSERT INTO '.$db->prefix.'forum_perms (group_id, forum_id, read_forum, post_replies, post_topics) VALUES('.$new_group_id.', '.$cur_forum_perm['forum_id'].', '.$cur_forum_perm['read_forum'].', '.$cur_forum_perm['post_replies'].', '.$cur_forum_perm['post_topics'].')') or error('Unable to insert group forum permissions', __FILE__, __LINE__, $db->error());
	} else {
		$result = $db->query('SELECT 1 FROM '.$db->prefix.'groups WHERE g_title=\''.$db->escape($title).'\' AND g_id!='.intval($_POST['group_id'])) or error('Unable to check group title collision', __FILE__, __LINE__, $db->error());
		if ($db->num_rows($result))
			message_backstage(sprintf(__('There is already a group with the title <strong>%s</strong>.', 'luna'), luna_htmlspecialchars($title)));

		$db->query('UPDATE '.$db->prefix.'groups SET g_title=\''.$db->escape($title).'\', g_user_title='.$user_title.', g_moderator='.$moderator.', g_mod_edit_users='.$mod_edit_users.', g_mod_rename_users='.$mod_rename_users.', g_mod_change_passwords='.$mod_change_passwords.', g_mod_ban_users='.$mod_ban_users.', g_read_board='.$read_board.', g_view_users='.$view_users.', g_post_replies='.$post_replies.', g_post_topics='.$post_topics.', g_edit_posts='.$edit_posts.', g_delete_posts='.$delete_posts.', g_delete_topics='.$delete_topics.', g_set_title='.$set_title.', g_search='.$search.', g_search_users='.$search_users.', g_send_email='.$send_email.', g_post_flood='.$post_flood.', g_search_flood='.$search_flood.', g_email_flood='.$email_flood.', g_report_flood='.$report_flood.', g_pm='.$inbox_allow.', g_pm_limit='.$inbox_limit.', g_soft_delete_view='.$soft_delete_view.', g_soft_delete_posts='.$soft_delete_posts.', g_soft_delete_topics='.$soft_delete_topics.' WHERE g_id='.intval($_POST['group_id'])) or error('Unable to update group', __FILE__, __LINE__, $db->error());
	}

	redirect('backstage/groups.php');
}


// Set default group
elseif (isset($_POST['set_default_group'])) {
	confirm_referrer('backstage/groups.php');
	
	$group_id = intval($_POST['default_group']);

	// Make sure it's not the admin or guest groups
	if ($group_id == FORUM_ADMIN || $group_id == FORUM_GUEST)
		message_backstage(__('Bad request. The link you followed is incorrect, outdated or you\'re simply not allowed to hang around here.', 'luna'), false, '404 Not Found');

	// Make sure it's not a moderator group
	$result = $db->query('SELECT 1 FROM '.$db->prefix.'groups WHERE g_id='.$group_id.' AND g_moderator=0') or error('Unable to check group moderator status', __FILE__, __LINE__, $db->error());
	if (!$db->num_rows($result))
		message_backstage(__('Bad request. The link you followed is incorrect, outdated or you\'re simply not allowed to hang around here.', 'luna'), false, '404 Not Found');

	$db->query('UPDATE '.$db->prefix.'config SET conf_value='.$group_id.' WHERE conf_name=\'o_default_user_group\'') or error('Unable to update board config', __FILE__, __LINE__, $db->error());

	// Regenerate the config cache
	if (!defined('FORUM_CACHE_FUNCTIONS_LOADED'))
		require FORUM_ROOT.'include/cache.php';

	generate_config_cache();

	redirect('backstage/groups.php');
}


// Remove a group
elseif (isset($_GET['del_group'])) {
	confirm_referrer('backstage/groups.php');
	
	$group_id = isset($_POST['group_to_delete']) ? intval($_POST['group_to_delete']) : intval($_GET['del_group']);
	if ($group_id < 5)
		message_backstage(__('Bad request. The link you followed is incorrect, outdated or you\'re simply not allowed to hang around here.', 'luna'), false, '404 Not Found');

	// Make sure we don't remove the default group
	if ($group_id == $luna_config['o_default_user_group'])
		message_backstage(__('The default group cannot be removed. In order to delete this group, you must first setup a different group as the default.', 'luna'));

	// Check if this group has any members
	$result = $db->query('SELECT g.g_title, COUNT(u.id) FROM '.$db->prefix.'groups AS g INNER JOIN '.$db->prefix.'users AS u ON g.g_id=u.group_id WHERE g.g_id='.$group_id.' GROUP BY g.g_id, g_title') or error('Unable to fetch group info', __FILE__, __LINE__, $db->error());

	// If the group doesn't have any members or if we've already selected a group to move the members to
	if (!$db->num_rows($result) || isset($_POST['del_group'])) {
		if (isset($_POST['del_group_comply']) || isset($_POST['del_group'])) {
			if (isset($_POST['del_group'])) {
				$move_to_group = intval($_POST['move_to_group']);
				$db->query('UPDATE '.$db->prefix.'users SET group_id='.$move_to_group.' WHERE group_id='.$group_id) or error('Unable to move users into group', __FILE__, __LINE__, $db->error());
			}

			// Delete the group and any forum specific permissions
			$db->query('DELETE FROM '.$db->prefix.'groups WHERE g_id='.$group_id) or error('Unable to delete group', __FILE__, __LINE__, $db->error());
			$db->query('DELETE FROM '.$db->prefix.'forum_perms WHERE group_id='.$group_id) or error('Unable to delete group forum permissions', __FILE__, __LINE__, $db->error());

			redirect('backstage/groups.php');
		} else {
			$result = $db->query('SELECT g_title FROM '.$db->prefix.'groups WHERE g_id='.$group_id) or error('Unable to fetch group title', __FILE__, __LINE__, $db->error());
			$group_title = $db->result($result);

			$page_title = array(luna_htmlspecialchars($luna_config['o_board_title']), __('Admin', 'luna'), __('User groups', 'luna'));
			define('FORUM_ACTIVE_PAGE', 'admin');
			require 'header.php';
				load_admin_nav('users', 'groups');

?>
<form method="post" action="groups.php?del_group=<?php echo $group_id ?>">
	<div class="panel panel-danger">
		<div class="panel-heading">
			<h3 class="panel-title"><?php printf(__('Are you sure that you want to delete the group <strong>%s</strong>?', 'luna'), luna_htmlspecialchars($group_title)) ?></h3>
		</div>
		<div class="panel-body">
			<input type="hidden" name="group_to_delete" value="<?php echo $group_id ?>" />
			<p><?php echo __('<b>Warning:</b> After you deleted a group you cannot restore it.', 'luna') ?></p>
		</div>
		<div class="panel-footer">
			<button class="btn btn-danger" type="submit" name="del_group_comply" tabindex="1"><span class="fa fa-fw fa-minus"></span> <?php echo __('Delete', 'luna') ?></button>
		</div>
	</div>
</form>
<?php

			require 'footer.php';
		}
	}

	list($group_title, $group_members) = $db->fetch_row($result);

	$page_title = array(luna_htmlspecialchars($luna_config['o_board_title']), __('Admin', 'luna'), __('User groups', 'luna'));
	define('FORUM_ACTIVE_PAGE', 'admin');
	require 'header.php';

?>
<div class="panel panel-default">
	<div class="panel-heading">
		<h3 class="panel-title"><?php echo __('Delete group', 'luna') ?></h3>
	</div>
	<div class="panel-body">
		<form id="groups" method="post" action="groups.php?del_group=<?php echo $group_id ?>">
			<fieldset>
				<p><?php printf(__('The group <strong>%s</strong> currently has <strong>%s</strong> members. Please select a group to which these members will be assigned upon deletion.', 'luna'), luna_htmlspecialchars($group_title), forum_number_format($group_members)) ?></p>
				<label><?php echo __('Move users to', 'luna') ?>
					<select class="form-control" name="move_to_group">
<?php

	$result = $db->query('SELECT g_id, g_title FROM '.$db->prefix.'groups WHERE g_id!='.FORUM_GUEST.' AND g_id!='.$group_id.' ORDER BY g_title') or error('Unable to fetch user group list', __FILE__, __LINE__, $db->error());

	while ($cur_group = $db->fetch_assoc($result)) {
		if ($cur_group['g_id'] == FORUM_MEMBER) // Pre-select the pre-defined Members group
			echo "\t\t\t\t\t\t\t\t\t\t".'<option value="'.$cur_group['g_id'].'" selected>'.luna_htmlspecialchars($cur_group['g_title']).'</option>'."\n";
		else
			echo "\t\t\t\t\t\t\t\t\t\t".'<option value="'.$cur_group['g_id'].'">'.luna_htmlspecialchars($cur_group['g_title']).'</option>'."\n";
	}

?>
					</select>
				</label>
			</fieldset>
			<p class="control-group">
				<input class="btn btn-danger" type="submit" name="del_group" value="<?php echo __('Delete group', 'luna') ?>" />
			</p>
		</form>
	</div>
</div>
<?php

	require 'footer.php';
} else {
	
	$page_title = array(luna_htmlspecialchars($luna_config['o_board_title']), __('Admin', 'luna'), __('User groups', 'luna'));
	define('FORUM_ACTIVE_PAGE', 'admin');
	require 'header.php';
		load_admin_nav('users', 'groups');

?>
<div class="row">
	<div class="col-sm-4">
		<div class="panel panel-default">
			<form id="groups" method="post" action="groups.php">
				<div class="panel-heading">
					<h3 class="panel-title"><?php echo __('Add new group', 'luna') ?><span class="pull-right"><button class="btn btn-primary" type="submit" name="add_group" tabindex="2"><span class="fa fa-fw fa-plus"></span> <?php echo __('Add', 'luna') ?></button></span></h3>
				</div>
				<div class="panel-body">
					<fieldset>
						<span class="help-block"><?php echo __('Select a group the new group will be based on.', 'luna') ?></span>
						<select class="form-control" id="base_group" name="base_group" tabindex="1">
<?php

$result = $db->query('SELECT g_id, g_title FROM '.$db->prefix.'groups WHERE g_id!='.FORUM_ADMIN.' AND g_id!='.FORUM_GUEST.' ORDER BY g_title') or error('Unable to fetch user group list', __FILE__, __LINE__, $db->error());

while ($cur_group = $db->fetch_assoc($result)) {
	if ($cur_group['g_id'] == $luna_config['o_default_user_group'])
		echo "\t\t\t\t\t\t\t\t\t\t\t".'<option value="'.$cur_group['g_id'].'" selected>'.luna_htmlspecialchars($cur_group['g_title']).'</option>'."\n";
	else
		echo "\t\t\t\t\t\t\t\t\t\t\t".'<option value="'.$cur_group['g_id'].'">'.luna_htmlspecialchars($cur_group['g_title']).'</option>'."\n";
}

?>
						</select>
					</fieldset>
				</div>
			</form>
		</div>
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title"><?php echo __('Set default group', 'luna') ?><span class="pull-right"><button class="btn btn-primary" type="submit" name="set_default_group"><span class="fa fa-fw fa-check"></span> <?php echo __('Save', 'luna') ?></button></span></h3>
			</div>
			<div class="panel-body">
				<form id="groups" method="post" action="groups.php">
					<fieldset>
						<span class="help-block"><?php echo __('The default group in which new users will be placed.', 'luna') ?></span>
						<select class="form-control" id="default_group" name="default_group" tabindex="3">
<?php

$result = $db->query('SELECT g_id, g_title FROM '.$db->prefix.'groups WHERE g_id>'.FORUM_GUEST.' AND g_moderator=0 ORDER BY g_title') or error('Unable to fetch user group list', __FILE__, __LINE__, $db->error());

while ($cur_group = $db->fetch_assoc($result)) {
	if ($cur_group['g_id'] == $luna_config['o_default_user_group'])
		echo "\t\t\t\t\t\t\t\t\t\t\t".'<option value="'.$cur_group['g_id'].'" selected>'.luna_htmlspecialchars($cur_group['g_title']).'</option>'."\n";
	else
		echo "\t\t\t\t\t\t\t\t\t\t\t".'<option value="'.$cur_group['g_id'].'">'.luna_htmlspecialchars($cur_group['g_title']).'</option>'."\n";
}

?>
						</select>
					</fieldset>
				</form>
			</div>
		</div>
	</div>
	<div class="col-sm-8">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title"><?php echo __('Manage groups', 'luna') ?></h3>
			</div>
			<table class="table">
				<tbody>
<?php

$cur_index = 5;

$result = $db->query('SELECT g_id, g_title FROM '.$db->prefix.'groups ORDER BY g_id') or error('Unable to fetch user group list', __FILE__, __LINE__, $db->error());

while ($cur_group = $db->fetch_assoc($result)) {
?>
					<tr>
						<td>
						<a class="btn btn-primary" href="groups.php?edit_group=<?php echo $cur_group['g_id'] ?>" tabindex="<?php echo $cur_index++ ?>"><span class="fa fa-fw fa-pencil-square-o"></span> <?php echo __('Edit', 'luna') ?></a>
						</td>
						<td class="col-lg-10"><?php echo luna_htmlspecialchars($cur_group['g_title']) ?></td>
						<td>
							<?php if ($cur_group['g_id'] > FORUM_MEMBER) { ?>
								<a class="btn btn-danger" href="groups.php?del_group=<?php echo $cur_group['g_id'] ?>" tabindex="<?php echo $cur_index++ ?>"><span class="fa fa-fw fa-minus"></span> <?php echo __('Delete', 'luna') ?></a>
							<?php } ?>
						</td>
					</tr>
<?php
}
?>
				</tbody>
			</table>
		</div>
	</div>
</div>
<?php
	
	require 'footer.php';
}