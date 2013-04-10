<?php

/****************************************************************************

Copyright (c) 2002-2013 Marco Von Ballmoos

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
require_once ('webcore/tasks/db/upgrade_per_app_221_23.php');

class ALBUMS_24_25_MIGRATOR_TASK extends UPGRADE_PER_APP_221_23_TASK
{
  public $application_name = 'earthli Albums';
  public $version_from = '2.4.0';
  public $version_to = '2.5.0';

  protected function _execute ()
  {
    log_open_block ("Updating names [object => entry]...");
      $this->_query ("ALTER TABLE `album_objects` RENAME `album_entries`");
      $this->_query ("ALTER TABLE `album_subscriptions` CHANGE `watch_objects` `watch_entries` TINYINT( 4 ) UNSIGNED DEFAULT '0' NOT NULL");
      $this->_query ("ALTER TABLE `album_journal` CHANGE `object_id` `entry_id` INT( 11 ) UNSIGNED DEFAULT '0' NOT NULL");
      $this->_query ("ALTER TABLE `album_pictures` CHANGE `object_id` `entry_id` INT( 11 ) UNSIGNED DEFAULT '0' NOT NULL");
      $this->_query ("ALTER TABLE `album_comments` CHANGE `object_id` `entry_id` INT( 11 ) UNSIGNED DEFAULT '0' NOT NULL");
    log_close_block ();

    log_open_block ("Building album tree...");

      $this->_query ("CREATE TABLE `album_tree` (`parent_id` INT UNSIGNED NOT NULL ,`child_id` INT UNSIGNED NOT NULL)");

      $this->_query ("SELECT * from album_folders");
      $this->build_folder_roots ('album_tree');

    log_close_block ();
  }
}

?>