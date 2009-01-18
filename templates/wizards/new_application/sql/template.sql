-- --------------------------------------------------------

-- 
-- Table structure for table `???_articles`
-- 

CREATE TABLE `???_articles` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `title` varchar(100) NOT NULL default '',
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
  KEY `folder_id` (`folder_id`),
  FULLTEXT KEY `title` (`title`),
  FULLTEXT KEY `description` (`description`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

-- 
-- Table structure for table `???_attachments`
-- 

CREATE TABLE `???_attachments` (
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
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

-- 
-- Table structure for table `???_comments`
-- 

CREATE TABLE `???_comments` (
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
  KEY `time_created` (`time_created`),
  FULLTEXT KEY `title` (`title`),
  FULLTEXT KEY `description` (`description`),
  FULLTEXT KEY `title_2` (`title`),
  FULLTEXT KEY `description_2` (`description`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

-- 
-- Table structure for table `???_folder_permissions`
-- 

CREATE TABLE `???_folder_permissions` (
  `ref_id` int(10) unsigned NOT NULL default '0',
  `folder_id` int(10) unsigned NOT NULL default '0',
  `importance` tinyint(3) unsigned NOT NULL default '0',
  `general_permissions` tinyint(3) unsigned NOT NULL default '0',
  `folder_permissions` smallint(5) unsigned NOT NULL default '0',
  `comment_permissions` smallint(5) unsigned NOT NULL default '0',
  `entry_permissions` smallint(5) unsigned NOT NULL default '0',
  `attachment_permissions` smallint(5) unsigned NOT NULL default '0',
  `kind` enum('user','group','registered','anonymous') NOT NULL default 'anonymous',
  PRIMARY KEY  (`ref_id`,`folder_id`,`kind`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

-- 
-- Table structure for table `???_folders`
-- 

CREATE TABLE `???_folders` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `title` varchar(100) NOT NULL default '',
  `organizational` tinyint(4) NOT NULL default '0',
  `icon_url` varchar(250) NOT NULL default '',
  `summary` text,
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
  UNIQUE KEY `id_2` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

-- 
-- Table structure for table `???_history_items`
-- 

CREATE TABLE `???_history_items` (
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
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

-- 
-- Table structure for table `???_subscribers`
-- 

CREATE TABLE `???_subscribers` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `email` varchar(250) NOT NULL default '',
  `send_as_html` tinyint(4) NOT NULL default '1',
  `group_history_items` tinyint(4) NOT NULL default '0',
  `send_own_changes` tinyint(3) unsigned NOT NULL default '1',
  `max_individual_messages` int(10) unsigned NOT NULL default '3',
  `max_items_per_message` int(10) unsigned NOT NULL default '25',
  `min_hours_to_wait` int(11) NOT NULL default '24',
  `show_history_items` tinyint(3) unsigned NOT NULL default '0',
  `show_history_item_as_subject` tinyint(3) unsigned NOT NULL default '0',
  `time_messages_sent` datetime NOT NULL default '0000-00-00 00:00:00',
  `queued_history_item_ids` text NOT NULL,
  `preferred_text_length` int(10) unsigned default '0',
  PRIMARY KEY  (`id`),
  KEY `email` (`email`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

-- 
-- Table structure for table `???_subscriptions`
-- 

CREATE TABLE `???_subscriptions` (
  `subscriber_id` int(10) unsigned NOT NULL default '0',
  `kind` enum('folder','entry','comment','user') NOT NULL default 'folder',
  `ref_id` int(10) unsigned NOT NULL default '0',
  `watch_entries` tinyint(4) unsigned NOT NULL default '0',
  `watch_comments` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`subscriber_id`,`kind`,`ref_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

-- 
-- Table structure for table `???_tree`
-- 

CREATE TABLE `???_tree` (
  `parent_id` int(10) unsigned NOT NULL default '0',
  `child_id` int(10) unsigned NOT NULL default '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

-- 
-- Table structure for table `???_user_permissions`
-- 

CREATE TABLE `???_user_permissions` (
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
  PRIMARY KEY  (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Create default user permissions (root and publisher)
--

-- root (all rights)
INSERT INTO ???_user_permissions VALUES (1, 0, 0, 0, 0, 0, 255, 255, 255, 255, 255, 255, 255, 255);
-- publisher (view rights)
INSERT INTO ???_user_permissions VALUES (2, 0, 0, 0, 0, 0, 3, 3, 3, 3, 3, 3, 3, 48);
-- registered users (view users and groups)
INSERT INTO ???_user_permissions VALUES (2147483647, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 48);
-- anon users (no rights)
INSERT INTO ???_user_permissions VALUES (0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);

--
-- Create default root folder
--

INSERT INTO ???_folders (id, title, permissions_id, creator_id, modifier_id, owner_id, time_created, time_modified) VALUES (1, 'Root', 1, 1, 1, 1, NOW(), NOW());
INSERT INTO ???_history_items (object_id, object_type, publication_state, user_id, time_created, system_description) VALUES(1, 'folder', 'queued', 1, NOW(), 'Created.');

-- Default permissions for anonymous users (no rights)
INSERT INTO ???_folder_permissions (ref_id, folder_id, kind) VALUES (0, 1, 'anonymous');
-- Default permissions for all users (no rights)
INSERT INTO ???_folder_permissions (ref_id, folder_id, kind) VALUES (0, 1, 'registered');

INSERT INTO ???_history_items (object_id, object_type, publication_state, user_id, time_created, system_description) VALUES(1, 'user', 'queued', 1, NOW(), 'Created.');
INSERT INTO ???_history_items (object_id, object_type, publication_state, user_id, time_created, system_description) VALUES(2, 'user', 'queued', 1, NOW(), 'Created.');

INSERT INTO versions (title, version) VALUES ('com.earthli.???', '2.8.0');
