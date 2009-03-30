<?php

/****************************************************************************

Copyright (c) 2002-2008 Marco Von Ballmoos

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
require_once ('webcore/tasks/db/upgrade_per_app_24_25.php');

class ALBUMS_26_27_MIGRATOR_TASK extends UPGRADE_PER_APP_24_25_TASK
{
  public $application_name = 'earthli Albums';
  public $version_from = '2.6.0';
  public $version_to = '2.7.0';

  protected function _execute ()
  {
    $this->add_attachments ('album_attachments', 'album_actions');

    $this->update_actions ('album_actions');
    $this->update_subscriptions ('album_subscribers', 'album_subscriptions');
    $this->update_permissions ('album_folder_permissions', 'album_user_permissions');
    $this->update_drafting ('album_entries');
    $this->update_folders ('album_folders');
    $this->update_users ('album_user_permissions');

    log_open_block ("Updating album properties");
      $this->_query ("ALTER TABLE `album_folders` CHANGE `opt_show_times` `show_times` TINYINT( 4 ) DEFAULT '0' NOT NULL ,
                      CHANGE `opt_show_celsius` `show_celsius` TINYINT( 4 ) DEFAULT '1' NOT NULL ,
                      CHANGE `opt_first_day_mode` `first_day_mode` TINYINT( 4 ) DEFAULT '0' NOT NULL ,
                      CHANGE `opt_last_day_mode` `last_day_mode` TINYINT( 4 ) DEFAULT '0' NOT NULL ,
                      CHANGE `opt_max_picture_width` `max_picture_width` INT( 10 ) UNSIGNED DEFAULT '0' NOT NULL ,
                      CHANGE `opt_max_picture_height` `max_picture_height` INT( 10 ) UNSIGNED DEFAULT '0' NOT NULL");
      $this->_query ("ALTER TABLE `album_folders` ADD `location` ENUM( 'local', 'remote' ) NOT NULL AFTER `max_picture_height` ;");
    log_close_block ();
  }
}

?>