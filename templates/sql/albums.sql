# phpMyAdmin SQL Dump
# version 2.5.4
# http://www.phpmyadmin.net
#
# Host: localhost
# Generation Time: Nov 15, 2004 at 09:50 PM
# Server version: 4.0.15
# PHP Version: 4.3.2
#
# Database : `earthli`
#

# --------------------------------------------------------

#
# Table structure for table `album_actions`
#

CREATE TABLE `album_actions` (
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
# Table structure for table `album_attachments`
#

CREATE TABLE `album_attachments` (
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
# Table structure for table `album_comments`
#

CREATE TABLE `album_comments` (
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
  `publication_state` tinyint(3) unsigned NOT NULL default '0',
  `owner_id` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `entry_id` (`entry_id`),
  KEY `time_created` (`time_created`)
) TYPE=MyISAM;

# --------------------------------------------------------

#
# Table structure for table `album_entries`
#

CREATE TABLE `album_entries` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `folder_id` int(10) unsigned NOT NULL default '0',
  `type` enum('picture','journal') NOT NULL default 'picture',
  `state` tinyint(3) unsigned NOT NULL default '1',
  `title` varchar(100) NOT NULL default '',
  `description` text NOT NULL,
  `time_created` datetime default '0000-00-00 00:00:00',
  `time_modified` datetime default '0000-00-00 00:00:00',
  `creator_id` int(10) unsigned NOT NULL default '0',
  `modifier_id` int(10) unsigned NOT NULL default '0',
  `publication_state` tinyint(3) unsigned NOT NULL default '0',
  `date` datetime NOT NULL default '0000-00-00 00:00:00',
  `publisher_id` int(10) unsigned NOT NULL default '0',
  `time_published` datetime NOT NULL default '0000-00-00 00:00:00',
  `owner_id` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `folder_id` (`folder_id`),
  KEY `time_created` (`time_created`),
  FULLTEXT KEY `title` (`title`),
  FULLTEXT KEY `description` (`description`)
) TYPE=MyISAM;

# --------------------------------------------------------

#
# Table structure for table `album_folder_permissions`
#

CREATE TABLE `album_folder_permissions` (
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
# Table structure for table `album_folders`
#

CREATE TABLE `album_folders` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `title` varchar(100) NOT NULL default '',
  `organizational` tinyint(4) NOT NULL default '0',
  `summary` tinytext,
  `description` text,
  `icon_url` varchar(250) NOT NULL default '',
  `first_day` datetime NOT NULL default '0000-00-00 00:00:00',
  `last_day` datetime NOT NULL default '0000-00-00 00:00:00',
  `main_picture_id` int(10) unsigned NOT NULL default '0',
  `url_root` varchar(250) NOT NULL default '',
  `show_times` tinyint(4) NOT NULL default '0',
  `show_celsius` tinyint(4) NOT NULL default '1',
  `first_day_mode` tinyint(4) NOT NULL default '0',
  `last_day_mode` tinyint(4) NOT NULL default '0',
  `max_picture_width` int(10) unsigned NOT NULL default '0',
  `max_picture_height` int(10) unsigned NOT NULL default '0',
  `location` enum('local','remote') NOT NULL default 'local',
  `state` tinyint(3) unsigned NOT NULL default '1',
  `parent_id` int(10) unsigned default NULL,
  `root_id` int(10) unsigned NOT NULL default '0',
  `permissions_id` int(10) unsigned NOT NULL default '0',
  `time_created` datetime default '0000-00-00 00:00:00',
  `time_modified` datetime default '0000-00-00 00:00:00',
  `creator_id` int(10) unsigned NOT NULL default '0',
  `modifier_id` int(10) unsigned NOT NULL default '0',
  `publication_state` tinyint(3) unsigned NOT NULL default '0',
  `owner_id` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `id` (`id`),
  KEY `first_day` (`first_day`)
) TYPE=MyISAM;

# --------------------------------------------------------

#
# Table structure for table `album_journal`
#

CREATE TABLE `album_journal` (
  `entry_id` int(11) unsigned NOT NULL default '0',
  `hi_temp` tinyint(4) NOT NULL default '0',
  `lo_temp` tinyint(4) NOT NULL default '0',
  `weather` varchar(100) NOT NULL default '',
  `weather_type` tinyint(3) unsigned NOT NULL default '1',
  KEY `entry_id` (`entry_id`)
) TYPE=MyISAM;

# --------------------------------------------------------

#
# Table structure for table `album_pictures`
#

CREATE TABLE `album_pictures` (
  `entry_id` int(11) unsigned NOT NULL default '0',
  `file_name` varchar(200) NOT NULL default '',
  KEY `entry_id` (`entry_id`),
  FULLTEXT KEY `file_name` (`file_name`)
) TYPE=MyISAM;

# --------------------------------------------------------

#
# Table structure for table `album_subscribers`
#

CREATE TABLE `album_subscribers` (
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
# Table structure for table `album_subscriptions`
#

CREATE TABLE `album_subscriptions` (
  `subscriber_id` int(10) unsigned NOT NULL default '0',
  `kind` enum('folder','entry','comment','user') NOT NULL default 'folder',
  `ref_id` int(10) unsigned NOT NULL default '0',
  `watch_entries` tinyint(4) unsigned NOT NULL default '0',
  `watch_comments` tinyint(4) NOT NULL default '0'
) TYPE=MyISAM;

# --------------------------------------------------------

#
# Table structure for table `album_tree`
#

CREATE TABLE `album_tree` (
  `parent_id` int(10) unsigned NOT NULL default '0',
  `child_id` int(10) unsigned NOT NULL default '0'
) TYPE=MyISAM;

# --------------------------------------------------------

#
# Table structure for table `album_user_permissions`
#

CREATE TABLE `album_user_permissions` (
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
INSERT INTO album_user_permissions VALUES (1, 0, 0, 0, 0, 0, 255, 255, 255, 255, 255, 255, 255, 255);
# publisher (view rights)
INSERT INTO album_user_permissions VALUES (2, 0, 0, 0, 0, 0, 3, 3, 3, 3, 3, 3, 3, 48);
# registered users (view users and groups)
INSERT INTO album_user_permissions VALUES (2147483647, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 48);
# anon users (no rights)
INSERT INTO album_user_permissions VALUES (0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);

#
# Create default root folder
#

INSERT INTO album_folders (id, title, permissions_id, creator_id, modifier_id, owner_id, time_created, time_modified, max_picture_width, max_picture_height, first_day_mode, last_day_mode, first_day, last_day, url_root) VALUES (1, 'Root', 1, 1, 1, 1, NOW(), NOW(), 640, 480, 1, 2, NOW(), NOW(), '{root}/albums');
INSERT INTO album_actions (object_id, object_type, publication_state, user_id, time_created, system_description) VALUES(1, 'folder', 'queued', 1, NOW(), 'Created.');

# Default permissions for anonymous users (no rights)
INSERT INTO album_folder_permissions (ref_id, folder_id, kind) VALUES (0, 1, 'anonymous');
# Default permissions for all users (no rights)
INSERT INTO album_folder_permissions (ref_id, folder_id, kind) VALUES (0, 1, 'registered');

INSERT INTO album_actions (object_id, object_type, publication_state, user_id, time_created, system_description) VALUES(1, 'user', 'queued', 1, NOW(), 'Created.');
INSERT INTO album_actions (object_id, object_type, publication_state, user_id, time_created, system_description) VALUES(2, 'user', 'queued', 1, NOW(), 'Created.');

