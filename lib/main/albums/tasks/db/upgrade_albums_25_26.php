<?php

/****************************************************************************

Copyright (c) 2002-2009 Marco Von Ballmoos

This file is part of earthli Albums.

earthli Albums is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

earthli Albums is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with earthli Albums; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

For more information about the earthli Albums, visit:

http://www.earthli.com/software/webcore/albums

****************************************************************************/

require_once ('webcore/init.php');
require_once ('webcore/tasks/db/upgrade_per_app_23_24.php');

class ALBUMS_25_26_MIGRATOR_TASK extends UPGRADE_PER_APP_23_24_TASK
{
  public $application_name = 'earthli Albums';
  public $version_from = '2.5.0';
  public $version_to = '2.6.0';

  protected function _execute ()
  {
    log_open_block ("Cleaning text for comments...");
      $this->clean_text ('album_comments');
    log_close_block ();

    log_open_block ("Cleaning text for folders...");
      $this->clean_text ('album_folders');
    log_close_block ();

    log_open_block ("Cleaning text for entries...");
      $this->clean_text ('album_entries');
    log_close_block ();

    log_open_block ("Updating subcribers table");
      $this->_query ("ALTER TABLE `album_subscribers` CHANGE `send_as_newsletter` `group_objects` TINYINT( 4 ) DEFAULT '0' NOT NULL;");
      $this->_query ("ALTER TABLE `album_subscribers` ADD `group_actions` TINYINT DEFAULT '0' NOT NULL AFTER `group_objects`;");
    log_close_block ();

    log_open_block ("Adding picture url to album folders...");
      $this->_query ("ALTER TABLE `album_folders` ADD `picture_url` VARCHAR( 100 ) NOT NULL AFTER `description`;");
    log_close_block ();

    log_open_block ("Adding actions table");
      $this->_query ("CREATE TABLE `album_actions` (`id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,`object_id` INT UNSIGNED NOT NULL ,`object_type` ENUM( 'folder', 'entry', 'comment', 'group', 'user' ) NOT NULL ,`user_id` INT UNSIGNED NOT NULL ,`access_id` INT UNSIGNED NOT NULL ,`kind` ENUM( 'Created', 'Updated', 'Deleted', 'Restored', 'Hidden', 'Hidden update' ) NOT NULL ,`time_created` DATETIME NOT NULL ,`publication_state` ENUM( 'silent', 'published', 'queued' ) NOT NULL ,`title` VARCHAR( 200 ) NOT NULL ,`description` TEXT NOT NULL ,`system_description` TEXT NOT NULL ,PRIMARY KEY ( `id` ) ,INDEX ( `object_id` , `object_type` ) );");
    log_close_block ();

    log_open_block ("Importing folder history...");
      $this->_query ("SELECT * from album_folders");
      $this->_create_actions ('folder', 'album_actions');
    log_close_block ();

    log_open_block ("Importing change history...");
      $this->_query ("SELECT * from album_entries WHERE type = 'picture'");
      $this->_create_actions ('entry', 'album_actions');
    log_close_block ();

    log_open_block ("Importing journal history...");
      $this->_query ("SELECT * from album_entries WHERE type = 'journal'");
      $this->_create_actions ('entry', 'album_actions');
    log_close_block ();

    log_open_block ("Importing comment history...");
      $this->_query ("SELECT com.*, entry.folder_id from album_comments com INNER JOIN project_entries entry ON entry.id = com.entry_id");
      $this->_create_actions ('comment', 'album_actions');
    log_close_block ();
  }
}

?>