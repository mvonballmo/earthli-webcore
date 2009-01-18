-- phpMyAdmin SQL Dump
-- version 2.6.3-pl1
-- http://www.phpmyadmin.net
-- 
-- Host: localhost
-- Generation Time: Dec 10, 2005 at 10:05 PM
-- Server version: 4.1.14
-- PHP Version: 4.3.11
-- 
-- Database: `earthli`
-- 

-- --------------------------------------------------------

-- 
-- Table structure for table `icons`
-- 

CREATE TABLE `icons` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `title` varchar(100) NOT NULL default '',
  `category` varchar(50) NOT NULL default '',
  `url` varchar(200) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM;

-- 
-- Dumping data for table `icons`
-- 

INSERT INTO `icons` VALUES (1, 'WebCore', 'Logos', '{icons}products/webcore');

-- --------------------------------------------------------

-- 
-- Table structure for table `themes`
-- 

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
) ENGINE=MyISAM;

-- 
-- Dumping data for table `themes`
-- 

INSERT INTO `themes` VALUES (1, 'Midnight', '{styles}themes/midnight', '', '', '', '', '', NOW());
INSERT INTO `themes` VALUES (2, 'Continental', '{styles}themes/blue_tan', '', '', '', '', '', NOW());
INSERT INTO `themes` VALUES (3, 'Ice', '{styles}themes/ice', '', '', '', '', '', NOW());
INSERT INTO `themes` VALUES (4, 'OS X (Jaguar)', '{styles}themes/osx', '', '', '', '', '', NOW());
INSERT INTO `themes` VALUES (5, 'OS X (Panther)', '{styles}themes/osx_panther', '', '', '', '', '', NOW());
INSERT INTO `themes` VALUES (6, 'Shale', '{styles}themes/shale', '', '', '', '', '', NOW());
INSERT INTO `themes` VALUES (7, 'Tangerine', '{styles}themes/tangerine', '', '', '', '', '', NOW());
