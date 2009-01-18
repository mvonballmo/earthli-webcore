# phpMyAdmin SQL Dump
# version 2.5.4
# http://www.phpmyadmin.net
#
# Host: localhost
# Generation Time: Nov 15, 2004 at 10:33 PM
# Server version: 4.0.15
# PHP Version: 4.3.2
#
# Database : `earthli`
#

# --------------------------------------------------------

#
# Table structure for table `icons`
#

CREATE TABLE `icons` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `title` varchar(100) NOT NULL default '',
  `category` varchar(50) NOT NULL default '',
  `url` varchar(200) NOT NULL default '',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

#
# Dumping data for table `icons`
#

INSERT INTO `icons` VALUES (1, 'Albums', 'Applications', '{icons}general/albums');
INSERT INTO `icons` VALUES (2, 'News', 'Applications', '{icons}general/news');
INSERT INTO `icons` VALUES (3, 'Projects', 'Applications', '{icons}general/projects');
INSERT INTO `icons` VALUES (4, 'Recipes', 'Applications', '{icons}general/recipes');
INSERT INTO `icons` VALUES (5, 'earthli Rabbit', 'Logos', '{icons}general/earthli_rabbit_logo');
INSERT INTO `icons` VALUES (6, 'WebCore', 'Logos', '{icons}general/webcore');

# --------------------------------------------------------

#
# Table structure for table `themes`
#

CREATE TABLE `themes` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `title` varchar(100) NOT NULL default '',
  `main_CSS_file_name` varchar(100) NOT NULL default '',
  `font_name_CSS_file_name` varchar(100) NOT NULL default '',
  `font_size_CSS_file_name` varchar(100) NOT NULL default '',
  `icon_set` varchar(100) NOT NULL default '',
  `icon_extension` varchar(5) NOT NULL default '',
  `renderer_class_name` varchar(100) NOT NULL default '',
  `time_created` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

#
# Dumping data for table `themes`
#

INSERT INTO `themes` VALUES (1, 'Alien', '{styles}themes/alien', '', '', '', '', '', NOW());
INSERT INTO `themes` VALUES (2, 'Continental', '{styles}themes/blue_tan', '', '', '', '', '', NOW());
INSERT INTO `themes` VALUES (3, 'Ice', '{styles}themes/ice', '', '', '', '', '', NOW());
INSERT INTO `themes` VALUES (4, 'OS X (Jaguar)', '{styles}themes/osx', '', '', '', '', '', NOW());
INSERT INTO `themes` VALUES (5, 'OS X (Panther)', '{styles}themes/osx_panther', '', '', '', '', '', NOW());
INSERT INTO `themes` VALUES (6, 'Shale', '{styles}themes/shale', '', '', '', '', '', NOW());
INSERT INTO `themes` VALUES (7, 'Tangerine', '{styles}themes/tangerine', '', '', '', '', '', NOW());

