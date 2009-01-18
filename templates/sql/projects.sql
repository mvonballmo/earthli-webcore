# phpMyAdmin SQL Dump
# version 2.5.4
# http://www.phpmyadmin.net
#
# Host: localhost
# Generation Time: Nov 16, 2004 at 09:43 PM
# Server version: 4.0.15
# PHP Version: 4.3.2
#
# Database : `earthli`
#

# --------------------------------------------------------

#
# Table structure for table `project_actions`
#

CREATE TABLE `project_actions` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `object_id` int(10) unsigned NOT NULL default '0',
  `object_type` enum('folder','entry','comment','group','user','branch','release','component','attachment') NOT NULL default 'folder',
  `access_id` int(10) unsigned NOT NULL default '0',
  `kind` enum('Created','Updated','Deleted','Restored','Hidden','Hidden update','Locked','Published') NOT NULL default 'Created',
  `user_id` int(10) unsigned NOT NULL default '0',
  `time_created` datetime NOT NULL default '0000-00-00 00:00:00',
  `publication_state` enum('silent','sent','queued') NOT NULL default 'silent',
  `title` varchar(200) NOT NULL default '',
  `description` text NOT NULL,
  `system_description` text NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `object_id` (`object_id`,`object_type`)
) TYPE=MyISAM;

# --------------------------------------------------------

#
# Table structure for table `project_attachments`
#

CREATE TABLE `project_attachments` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `object_id` int(10) unsigned NOT NULL default '0',
  `state` tinyint(3) unsigned NOT NULL default '0',
  `type` enum('folder','entry','comment','user','group') NOT NULL default 'folder',
  `title` varchar(200) NOT NULL default '',
  `description` text NOT NULL,
  `file_name` varchar(200) NOT NULL default '',
  `original_file_name` varchar(200) NOT NULL default '',
  `size` int(11) NOT NULL default '0',
  `mime_type` varchar(100) NOT NULL default '',
  `is_image` tinyint(3) unsigned NOT NULL default '0',
  `is_archive` tinyint(3) unsigned NOT NULL default '0',
  `time_created` datetime default '0000-00-00 00:00:00',
  `time_modified` datetime default '0000-00-00 00:00:00',
  `creator_id` int(10) unsigned NOT NULL default '0',
  `modifier_id` int(10) unsigned NOT NULL default '0',
  `owner_id` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

# --------------------------------------------------------

#
# Table structure for table `project_branches`
#

CREATE TABLE `project_branches` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `folder_id` int(10) unsigned NOT NULL default '0',
  `parent_release_id` int(10) unsigned NOT NULL default '0',
  `state` tinyint(3) unsigned NOT NULL default '1',
  `title` varchar(100) NOT NULL default '',
  `description` text NOT NULL,
  `time_created` datetime default NULL,
  `time_modified` datetime default NULL,
  `creator_id` int(10) unsigned NOT NULL default '0',
  `modifier_id` int(10) unsigned NOT NULL default '0',
  `owner_id` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `id` (`id`)
) TYPE=MyISAM;

# --------------------------------------------------------

#
# Table structure for table `project_changes`
#

CREATE TABLE `project_changes` (
  `entry_id` int(11) unsigned NOT NULL default '0',
  `job_id` int(10) unsigned NOT NULL default '0',
  `number` int(10) unsigned NOT NULL default '0',
  `files` text NOT NULL,
  `applier_id` int(10) unsigned default NULL,
  `time_applied` datetime default NULL,
  KEY `object_id` (`entry_id`),
  KEY `time_applied` (`time_applied`),
  FULLTEXT KEY `files` (`files`)
) TYPE=MyISAM;

# --------------------------------------------------------

#
# Table structure for table `project_changes_to_branches`
#

CREATE TABLE `project_changes_to_branches` (
  `entry_to_branch_id` int(10) unsigned NOT NULL default '0',
  `branch_applier_id` int(10) unsigned default NULL,
  `branch_time_applied` datetime default NULL,
  KEY `entry_to_branch_id` (`entry_to_branch_id`),
  KEY `branch_time_applied` (`branch_time_applied`)
) TYPE=MyISAM;

# --------------------------------------------------------

#
# Table structure for table `project_comments`
#

CREATE TABLE `project_comments` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `number` int(10) unsigned NOT NULL default '0',
  `title` varchar(100) NOT NULL default '',
  `description` text,
  `state` tinyint(3) unsigned NOT NULL default '1',
  `kind` tinyint(3) unsigned NOT NULL default '0',
  `entry_id` int(11) unsigned NOT NULL default '0',
  `parent_id` int(10) unsigned NOT NULL default '0',
  `root_id` int(10) unsigned NOT NULL default '0',
  `time_created` datetime default '0000-00-00 00:00:00',
  `time_modified` datetime default '0000-00-00 00:00:00',
  `creator_id` int(10) unsigned NOT NULL default '0',
  `modifier_id` int(10) unsigned NOT NULL default '0',
  `owner_id` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `entry_id` (`entry_id`),
  KEY `time_created` (`time_created`)
) TYPE=MyISAM;

# --------------------------------------------------------

#
# Table structure for table `project_components`
#

CREATE TABLE `project_components` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `folder_id` int(10) unsigned NOT NULL default '0',
  `title` varchar(100) NOT NULL default '',
  `description` text NOT NULL,
  `icon_url` varchar(250) NOT NULL default '',
  `state` tinyint(3) unsigned NOT NULL default '0',
  `creator_id` int(10) unsigned NOT NULL default '0',
  `modifier_id` int(10) unsigned NOT NULL default '0',
  `time_created` datetime NOT NULL default '0000-00-00 00:00:00',
  `time_modified` datetime NOT NULL default '0000-00-00 00:00:00',
  `owner_id` int(10) unsigned NOT NULL default '0',
  KEY `id` (`id`)
) TYPE=MyISAM;

# --------------------------------------------------------

#
# Table structure for table `project_entries`
#

CREATE TABLE `project_entries` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `type` enum('change','job') NOT NULL default 'change',
  `folder_id` int(10) unsigned NOT NULL default '0',
  `component_id` int(10) unsigned NOT NULL default '0',
  `state` tinyint(3) unsigned NOT NULL default '1',
  `title` varchar(100) NOT NULL default '',
  `description` text NOT NULL,
  `time_created` datetime default '0000-00-00 00:00:00',
  `time_modified` datetime default '0000-00-00 00:00:00',
  `creator_id` int(10) unsigned NOT NULL default '0',
  `modifier_id` int(10) unsigned NOT NULL default '0',
  `kind` tinyint(3) unsigned NOT NULL default '0',
  `release_id` int(11) unsigned NOT NULL default '0',
  `extra_description` text NOT NULL,
  `main_branch_id` int(11) unsigned NOT NULL default '0',
  `owner_id` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `time_created` (`time_created`),
  FULLTEXT KEY `title` (`title`),
  FULLTEXT KEY `description` (`description`),
  FULLTEXT KEY `extra_description` (`extra_description`)
) TYPE=MyISAM;

# --------------------------------------------------------

#
# Table structure for table `project_entries_to_branches`
#

CREATE TABLE `project_entries_to_branches` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `entry_id` int(10) unsigned NOT NULL default '0',
  `branch_id` int(10) unsigned NOT NULL default '0',
  `branch_release_id` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `id` (`id`),
  KEY `entry_id` (`entry_id`)
) TYPE=MyISAM;

# --------------------------------------------------------

#
# Table structure for table `project_folder_permissions`
#

CREATE TABLE `project_folder_permissions` (
  `ref_id` int(10) unsigned NOT NULL default '0',
  `folder_id` int(10) unsigned NOT NULL default '0',
  `importance` tinyint(3) unsigned NOT NULL default '0',
  `general_permissions` tinyint(3) unsigned NOT NULL default '0',
  `folder_permissions` smallint(5) unsigned NOT NULL default '0',
  `comment_permissions` smallint(5) unsigned NOT NULL default '0',
  `entry_permissions` smallint(5) unsigned NOT NULL default '0',
  `attachment_permissions` smallint(5) unsigned NOT NULL default '0',
  `kind` enum('user','group','registered','anonymous') NOT NULL default 'anonymous',
  KEY `ref_id` (`ref_id`),
  KEY `folder_id` (`folder_id`)
) TYPE=MyISAM;

# --------------------------------------------------------

#
# Table structure for table `project_folders`
#

CREATE TABLE `project_folders` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `title` varchar(100) NOT NULL default '',
  `organizational` tinyint(4) NOT NULL default '0',
  `icon_url` varchar(250) NOT NULL default '',
  `options_id` int(10) unsigned NOT NULL default '0',
  `summary` varchar(250) default NULL,
  `description` text,
  `state` tinyint(3) unsigned NOT NULL default '1',
  `parent_id` int(10) unsigned default NULL,
  `root_id` int(10) unsigned NOT NULL default '0',
  `permissions_id` int(10) unsigned NOT NULL default '0',
  `time_created` datetime default '0000-00-00 00:00:00',
  `time_modified` datetime default '0000-00-00 00:00:00',
  `creator_id` int(10) unsigned NOT NULL default '0',
  `modifier_id` int(10) unsigned NOT NULL default '0',
  `trunk_id` int(11) unsigned NOT NULL default '0',
  `owner_id` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `id_2` (`id`),
  KEY `id` (`id`)
) TYPE=MyISAM;

# --------------------------------------------------------

#
# Table structure for table `project_jobs`
#

CREATE TABLE `project_jobs` (
  `entry_id` int(11) unsigned NOT NULL default '0',
  `priority` tinyint(3) unsigned NOT NULL default '0',
  `status` tinyint(3) unsigned NOT NULL default '0',
  `assignee_id` int(10) unsigned NOT NULL default '0',
  `reporter_id` int(10) unsigned NOT NULL default '0',
  `closer_id` int(10) unsigned NOT NULL default '0',
  `time_closed` datetime NOT NULL default '0000-00-00 00:00:00',
  `time_scheduled` datetime NOT NULL default '0000-00-00 00:00:00',
  `long_description` text NOT NULL,
  `time_needed` datetime NOT NULL default '0000-00-00 00:00:00',
  `time_status_changed` datetime NOT NULL default '0000-00-00 00:00:00',
  `time_assignee_changed` datetime NOT NULL default '0000-00-00 00:00:00',
  KEY `entry_id` (`entry_id`),
  KEY `time_status_changed` (`time_status_changed`),
  KEY `time_owner_changed` (`time_assignee_changed`),
  KEY `time_closed` (`time_closed`)
) TYPE=MyISAM;

# --------------------------------------------------------

#
# Table structure for table `project_jobs_to_branches`
#

CREATE TABLE `project_jobs_to_branches` (
  `entry_to_branch_id` int(10) unsigned NOT NULL default '0',
  `branch_status` tinyint(3) unsigned default NULL,
  `branch_priority` tinyint(3) unsigned default NULL,
  `branch_closer_id` int(10) unsigned default NULL,
  `branch_time_closed` datetime default NULL,
  `branch_time_status_changed` datetime NOT NULL default '0000-00-00 00:00:00',
  KEY `branch_time_closed` (`branch_time_closed`),
  KEY `entry_to_branch_id` (`entry_to_branch_id`)
) TYPE=MyISAM;

# --------------------------------------------------------

#
# Table structure for table `project_options`
#

CREATE TABLE `project_options` (
  `folder_id` int(10) unsigned NOT NULL default '0',
  `assignee_group_type` tinyint(4) NOT NULL default '0',
  `assignee_group_id` int(10) unsigned NOT NULL default '0',
  `seconds_until_deadline` int(10) unsigned NOT NULL default '0',
  KEY `folder_id` (`folder_id`)
) TYPE=MyISAM;

# --------------------------------------------------------

#
# Table structure for table `project_releases`
#

CREATE TABLE `project_releases` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `branch_id` int(10) unsigned NOT NULL default '0',
  `state` tinyint(3) unsigned NOT NULL default '0',
  `title` varchar(100) NOT NULL default '',
  `description` text NOT NULL,
  `time_scheduled` datetime NOT NULL default '0000-00-00 00:00:00',
  `time_next_deadline` datetime default NULL,
  `time_tested` datetime default NULL,
  `time_testing_scheduled` datetime default NULL,
  `time_shipped` datetime NOT NULL default '0000-00-00 00:00:00',
  `time_created` datetime default NULL,
  `time_modified` datetime default NULL,
  `creator_id` int(10) unsigned NOT NULL default '0',
  `modifier_id` int(10) unsigned NOT NULL default '0',
  `summary` text,
  `owner_id` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `id` (`id`),
  KEY `branch_id` (`branch_id`),
  FULLTEXT KEY `summary` (`summary`)
) TYPE=MyISAM;

# --------------------------------------------------------

#
# Table structure for table `project_subscribers`
#

CREATE TABLE `project_subscribers` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `email` varchar(250) NOT NULL default '',
  `send_as_html` tinyint(4) NOT NULL default '1',
  `group_actions` tinyint(4) NOT NULL default '0',
  `send_own_changes` tinyint(4) NOT NULL default '0',
  `max_individual_messages` int(10) unsigned NOT NULL default '3',
  `max_items_per_message` int(10) unsigned NOT NULL default '25',
  `min_hours_to_wait` int(11) NOT NULL default '24',
  `show_actions` tinyint(3) unsigned NOT NULL default '0',
  `show_action_as_subject` tinyint(3) unsigned NOT NULL default '0',
  `time_messages_sent` datetime NOT NULL default '0000-00-00 00:00:00',
  `queued_action_ids` text NOT NULL,
  `preferred_text_length` int(10) unsigned default '0',
  PRIMARY KEY  (`id`),
  KEY `email` (`email`)
) TYPE=MyISAM;

# --------------------------------------------------------

#
# Table structure for table `project_subscriptions`
#

CREATE TABLE `project_subscriptions` (
  `subscriber_id` int(10) unsigned NOT NULL default '0',
  `kind` enum('folder','entry','comment','user') NOT NULL default 'folder',
  `ref_id` int(10) unsigned NOT NULL default '0',
  `watch_entries` tinyint(4) unsigned NOT NULL default '0',
  `watch_comments` tinyint(4) NOT NULL default '0'
) TYPE=MyISAM;

# --------------------------------------------------------

#
# Table structure for table `project_tree`
#

CREATE TABLE `project_tree` (
  `parent_id` int(10) unsigned NOT NULL default '0',
  `child_id` int(10) unsigned NOT NULL default '0'
) TYPE=MyISAM;

# --------------------------------------------------------

#
# Table structure for table `project_user_permissions`
#

CREATE TABLE `project_user_permissions` (
  `user_id` int(10) unsigned NOT NULL default '0',
  `deny_general_permissions` tinyint(3) unsigned NOT NULL default '0',
  `deny_folder_permissions` smallint(5) unsigned NOT NULL default '0',
  `deny_comment_permissions` smallint(5) unsigned NOT NULL default '0',
  `deny_entry_permissions` smallint(5) unsigned NOT NULL default '0',
  `deny_attachment_permissions` smallint(5) unsigned NOT NULL default '0',
  `allow_general_permissions` tinyint(3) unsigned NOT NULL default '0',
  `allow_folder_permissions` smallint(5) unsigned NOT NULL default '0',
  `allow_comment_permissions` smallint(5) unsigned NOT NULL default '0',
  `allow_entry_permissions` smallint(5) unsigned NOT NULL default '0',
  `allow_attachment_permissions` smallint(5) unsigned NOT NULL default '0',
  `user_permissions` smallint(5) unsigned NOT NULL default '0',
  `group_permissions` smallint(5) unsigned NOT NULL default '0',
  `global_permissions` tinyint(3) unsigned NOT NULL default '0',
  KEY `user_id` (`user_id`)
) TYPE=MyISAM;

#
# Create default user permissions (root and publisher)
#

# root (all rights)
INSERT INTO project_user_permissions VALUES (1, 0, 0, 0, 0, 0, 255, 255, 255, 255, 255, 255, 255, 255);
# publisher (view rights)
INSERT INTO project_user_permissions VALUES (2, 0, 0, 0, 0, 0, 3, 3, 3, 3, 3, 3, 3, 48);
# registered users (view users and groups)
INSERT INTO project_user_permissions VALUES (2147483647, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 48);
# anon users (no rights)
INSERT INTO project_user_permissions VALUES (0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);

#
# Create default root folder
#

INSERT INTO project_options (folder_id) VALUES(1);
INSERT INTO project_branches (id, folder_id, title, creator_id, modifier_id, owner_id, time_created, time_modified) VALUES(1, 1, 'Dev', 1, 1, 1, NOW(), NOW());
INSERT INTO project_actions (object_id, object_type, publication_state, user_id, time_created, system_description) VALUES(1, 'branch', 'queued', 1, NOW(), 'Created.');
INSERT INTO project_folders (id, title, permissions_id, creator_id, modifier_id, owner_id, time_created, time_modified, options_id, trunk_id) VALUES(1, 'Root', 1, 1, 1, 1, NOW(), NOW(), 1, 1);
INSERT INTO project_actions (object_id, object_type, publication_state, user_id, time_created, system_description) VALUES(1, 'folder', 'queued', 1, NOW(), 'Created.');

# Default permissions for anonymous users (no rights)
INSERT INTO project_folder_permissions (ref_id, folder_id, kind) VALUES (0, 1, 'anonymous');
# Default permissions for all users (no rights)
INSERT INTO project_folder_permissions (ref_id, folder_id, kind) VALUES (0, 1, 'registered');

INSERT INTO project_actions (object_id, object_type, publication_state, user_id, time_created, system_description) VALUES(1, 'user', 'queued', 1, NOW(), 'Created.');
INSERT INTO project_actions (object_id, object_type, publication_state, user_id, time_created, system_description) VALUES(2, 'user', 'queued', 1, NOW(), 'Created.');

