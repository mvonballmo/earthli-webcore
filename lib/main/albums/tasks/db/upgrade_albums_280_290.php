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
require_once ('webcore/tasks/db/upgrade_per_app_260_270.php');

class ALBUMS_280_290_MIGRATOR_TASK extends UPGRADE_PER_APP_260_270_TASK
{
  public $application_name = 'earthli Albums';
  public $version_from = '2.8.0';
  public $version_to = '2.9.0';

  protected function _execute ()
  {
    log_open_block ("Cleaning up indexes");
      $this->clean_up_folder_permissions ('album_folder_permissions');
      $this->clean_up_user_permissions ('album_user_permissions');
      $this->clean_up_id_indexes ('album_entries');
      $this->clean_up_id_indexes ('album_folders');
      $this->clean_up_id_indexes ('album_comments');
      $this->_query ('ALTER TABLE `album_journal` DROP INDEX `entry_id`');
      $this->_query ('ALTER TABLE `album_journal` ADD PRIMARY KEY ( `entry_id` )');
      $this->_query ('ALTER TABLE `album_pictures` DROP INDEX `entry_id`');
      $this->_query ('ALTER TABLE `album_pictures` ADD PRIMARY KEY ( `entry_id` )');
      $this->clean_up_subscriptions ('album_subscriptions');
      $this->add_full_text_to_comments ('album_comments');
    log_close_block ();
    log_open_block ("Renaming subscriber fields");
      $this->rename_subscriber_fields ('album_subscribers');
      $this->rename_action_table ('album_actions', 'album_history_items');
    log_close_block ();
  }
}

?>