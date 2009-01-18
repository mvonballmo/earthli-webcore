# phpMyAdmin SQL Dump
# version 2.5.4
# http://www.phpmyadmin.net
#
# Host: localhost
# Generation Time: Apr 19, 2004 at 10:45 PM
# Server version: 4.0.15
# PHP Version: 4.3.2
#
# Database : `earthli`
#

# --------------------------------------------------------

# --------------------------------------------------------

#
# Table structure for table `groups`
#

CREATE TABLE `groups` (
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

CREATE TABLE `users` (
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
  `email_visibility` enum('hidden','scrambled','visible') NOT NULL default 'hidden',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `title` (`title`),
  KEY `id` (`id`),
  KEY `title_password` (`title`,`password`)
) TYPE=MyISAM;

# --------------------------------------------------------

#
# Table structure for table `users_to_groups`
#

CREATE TABLE `users_to_groups` (
  `user_id` int(10) unsigned NOT NULL default '0',
  `group_id` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`user_id`,`group_id`)
) TYPE=MyISAM;

#
# Create default users (root and publisher)
#

INSERT INTO users (title, password, time_created, time_modified, creator_id, modifier_id, kind) VALUES ('root', md5('password'), NOW(), NOW(), 0, 0, 'registered');
INSERT INTO users (title, password, time_created, time_modified, creator_id, modifier_id, kind) VALUES ('publisher', md5('password'), NOW(), NOW(), 1, 1, 'registered');

