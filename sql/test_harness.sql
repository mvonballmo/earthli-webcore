# phpMyAdmin SQL Dump
# version 2.5.4
# http://www.phpmyadmin.net
#
# Host: localhost
# Generation Time: Oct 12, 2004 at 08:17 PM
# Server version: 4.0.15
# PHP Version: 4.3.2
#
# Database : `earthli`
#

# --------------------------------------------------------

#
# Table structure for table `test_harness_history_items`
#

CREATE TABLE `test_harness_history_items` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `object_id` int(10) unsigned NOT NULL default '0',
  `object_type` enum('folder','entry','comment','group','user','attachment') NOT NULL default 'folder',
  `user_id` int(10) unsigned NOT NULL default '0',
  `access_id` int(10) unsigned NOT NULL default '0',
  `kind` enum('Created','Updated','Deleted','Restored','Hidden','Hidden update','Locked','Published') NOT NULL default 'Created',
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
# Table structure for table `test_harness_attachments`
#

CREATE TABLE `test_harness_attachments` (
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
# Table structure for table `test_harness_comments`
#

CREATE TABLE `test_harness_comments` (
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
# Table structure for table `test_harness_folder_permissions`
#

CREATE TABLE `test_harness_folder_permissions` (
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
# Table structure for table `test_harness_folders`
#

CREATE TABLE `test_harness_folders` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `title` varchar(100) NOT NULL default '',
  `organizational` tinyint(4) NOT NULL default '0',
  `icon_url` varchar(250) NOT NULL default '',
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
  `owner_id` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `id_2` (`id`),
  KEY `id` (`id`)
) TYPE=MyISAM;

# --------------------------------------------------------

#
# Table structure for table `test_harness_subscribers`
#

CREATE TABLE `test_harness_subscribers` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `email` varchar(250) NOT NULL default '',
  `send_as_html` tinyint(4) NOT NULL default '1',
  `group_actions` tinyint(4) NOT NULL default '0',
  `send_own_changes` tinyint(3) unsigned NOT NULL default '1',
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
# Table structure for table `test_harness_subscriptions`
#

CREATE TABLE `test_harness_subscriptions` (
  `subscriber_id` int(10) unsigned NOT NULL default '0',
  `kind` enum('folder','entry','comment','user') NOT NULL default 'folder',
  `ref_id` int(10) unsigned NOT NULL default '0',
  `watch_entries` tinyint(4) unsigned NOT NULL default '0',
  `watch_comments` tinyint(4) NOT NULL default '0',
  KEY `email` (`subscriber_id`)
) TYPE=MyISAM;

# --------------------------------------------------------

#
# Table structure for table `test_harness_tree`
#

CREATE TABLE `test_harness_tree` (
  `parent_id` int(10) unsigned NOT NULL default '0',
  `child_id` int(10) unsigned NOT NULL default '0'
) TYPE=MyISAM;

# --------------------------------------------------------

#
# Table structure for table `test_harness_user_permissions`
#

CREATE TABLE `test_harness_user_permissions` (
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

# --------------------------------------------------------

#
# Table structure for table `test_harness_entries`
#

CREATE TABLE `test_harness_entries` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `title` varchar(100) NOT NULL default '',
  `picture_url` varchar(250) NOT NULL default '',
  `originator` varchar(255) NOT NULL default '',
  `description` text NOT NULL,
  `folder_id` int(10) unsigned NOT NULL default '0',
  `state` tinyint(3) unsigned NOT NULL default '1',
  `time_created` datetime default '0000-00-00 00:00:00',
  `time_modified` datetime default '0000-00-00 00:00:00',
  `creator_id` int(10) unsigned NOT NULL default '0',
  `modifier_id` int(10) unsigned NOT NULL default '0',
  `publisher_id` int(10) unsigned NOT NULL default '0',
  `time_published` datetime NOT NULL default '0000-00-00 00:00:00',
  `owner_id` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `id` (`id`),
  FULLTEXT KEY `title` (`title`),
  FULLTEXT KEY `description` (`description`)
) TYPE=MyISAM;

#
# Table structure for table `groups`
#

CREATE TABLE `test_harness_groups` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `title` varchar(100) NOT NULL default '',
  `description` text NOT NULL,
  `state` tinyint(3) unsigned NOT NULL default '0',
  `time_created` datetime default '0000-00-00 00:00:00',
  `time_modified` datetime default '0000-00-00 00:00:00',
  `creator_id` int(10) unsigned NOT NULL default '0',
  `modifier_id` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `id` (`id`)
) TYPE=MyISAM;

# --------------------------------------------------------

#
# Table structure for table `users`
#

CREATE TABLE `test_harness_users` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `title` varchar(50) NOT NULL default '',
  `password` varchar(100) default NULL,
  `ip_address` int(11) default NULL,
  `real_first_name` varchar(100) default NULL,
  `real_last_name` varchar(100) default NULL,
  `description` text,
  `state` tinyint(3) unsigned NOT NULL default '1',
  `icon_url` varchar(250) default NULL,
  `home_page_url` varchar(250) default NULL,
  `email` varchar(200) default NULL,
  `picture_url` varchar(250) default NULL,
  `signature` text,
  `time_created` datetime NOT NULL default '0000-00-00 00:00:00',
  `time_modified` datetime NOT NULL default '0000-00-00 00:00:00',
  `creator_id` int(10) unsigned NOT NULL default '1',
  `modifier_id` int(10) unsigned NOT NULL default '1',
  `kind` enum('anonymous','registered') NOT NULL default 'anonymous',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `title` (`title`),
  KEY `id` (`id`),
  KEY `title_password` (`title`,`password`)
) TYPE=MyISAM;

# --------------------------------------------------------

#
# Table structure for table `users_to_groups`
#

CREATE TABLE `test_harness_users_to_groups` (
  `user_id` int(10) unsigned NOT NULL default '0',
  `group_id` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`user_id`,`group_id`)
) TYPE=MyISAM;

#
# Create default users (root and publisher)
#

INSERT INTO test_harness_users (title, password, time_created, time_modified) VALUES ('root', md5('password'), NOW(), NOW());
INSERT INTO test_harness_users (title, password, time_created, time_modified) VALUES ('publisher', md5('password'), NOW(), NOW());
INSERT INTO test_harness_users (title, password, time_created, time_modified) VALUES ('tester', md5('password'), NOW(), NOW());

#
# Create default root folder
#

INSERT INTO test_harness_folders (id, title, permissions_id, creator_id, modifier_id, time_created, time_modified) VALUES (id, 'Root', 1, 1, 1, NOW(), NOW());
INSERT INTO test_harness_history_items (object_id, object_type, publication_state, user_id, time_created, system_description) VALUES(1, 'folder', 'queued', 1, NOW(), 'Created.');

#
# Create default user permissions (root and publisher)
#

# root (all rights)
INSERT INTO test_harness_user_permissions VALUES (1, 0, 0, 0, 0, 0, 255, 255, 255, 255, 255, 255, 255, 255);
# publisher (view rights)
INSERT INTO test_harness_user_permissions VALUES (2, 0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 1, 20);
# tester (all rights)
INSERT INTO test_harness_user_permissions VALUES (3, 0, 0, 0, 0, 0, 255, 255, 255, 255, 255, 255, 255, 255);
# registered users (view users and groups)
INSERT INTO test_harness_user_permissions VALUES (2147483647, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 54);
# anon users (no rights)
INSERT INTO test_harness_user_permissions VALUES (0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);

# Default permissions for anonymous users (no rights)
INSERT INTO test_harness_folder_permissions (ref_id, folder_id, kind) VALUES (0, 1, 'anonymous');
# Default permissions for all users (no rights)
INSERT INTO test_harness_folder_permissions (ref_id, folder_id, kind) VALUES (0, 1, 'registered');

INSERT INTO test_harness_history_items (object_id, object_type, publication_state, user_id, time_created, system_description) VALUES(1, 'user', 'queued', 1, NOW(), 'Created.');
INSERT INTO test_harness_history_items (object_id, object_type, publication_state, user_id, time_created, system_description) VALUES(2, 'user', 'queued', 1, NOW(), 'Created.');
INSERT INTO test_harness_history_items (object_id, object_type, publication_state, user_id, time_created, system_description) VALUES(3, 'user', 'queued', 1, NOW(), 'Created.');


