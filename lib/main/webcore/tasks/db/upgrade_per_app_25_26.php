<?php

/****************************************************************************

Copyright (c) 2002-2009 Marco Von Ballmoos

This file is part of earthli WebCore.

earthli WebCore is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

earthli WebCore is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with earthli WebCore; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

For more information about the earthli WebCore, visit:

http://www.earthli.com/software/webcore

****************************************************************************/

require_once ('webcore/db/migrator_task.php');

class UPGRADE_PER_APP_25_26_TASK extends MIGRATOR_TASK
{
  public function add_organizational ($table_name)
  {
    log_open_block ("Adding organizational to folders");
      $this->_query ("ALTER TABLE `$table_name` ADD `organizational` TINYINT NOT NULL AFTER `title` ;");
    log_close_block ();
  }

  public function add_owner_to_table ($table_name)
  {
    $this->_query ("ALTER TABLE `$table_name` ADD `owner_id` INT UNSIGNED NOT NULL ;");
    $this->_query ("UPDATE `$table_name` SET owner_id = creator_id");
  }

  public function add_version_info ($table_name, $title, $version)
  {
    $this->_query ("INSERT INTO `versions` VALUES('$title', '$version');");
  }

  public function add_attachments ($table_name, $action_table_name)
  {
    log_open_block ("Adding attachments...");

$s = <<<EOD
CREATE TABLE $table_name (
              `id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
              `object_id` INT UNSIGNED NOT NULL ,
              `state` TINYINT UNSIGNED NOT NULL ,
              `type` ENUM( 'folder', 'entry', 'comment', 'user', 'group' ) NOT NULL ,
              `title` VARCHAR( 200 ) NOT NULL ,
              `description` TEXT NOT NULL ,
              `file_name` VARCHAR( 200 ) NOT NULL ,
              `original_file_name` VARCHAR( 200 ) NOT NULL ,
              `size` INT NOT NULL ,
              `mime_type` VARCHAR( 100 ) NOT NULL ,
              `is_image` TINYINT UNSIGNED NOT NULL ,
              `is_archive` TINYINT UNSIGNED NOT NULL ,
              `time_created` datetime default '0000-00-00 00:00:00',
              `time_modified` datetime default '0000-00-00 00:00:00',
              `creator_id` int(10) unsigned NOT NULL default '0',
              `modifier_id` int(10) unsigned NOT NULL default '0',
              PRIMARY KEY ( `id` )
              );
EOD;

      $this->_query ($s);
      $this->_query ("ALTER TABLE $action_table_name CHANGE `object_type` `object_type` ENUM( 'folder', 'entry', 'comment', 'group', 'user', 'attachment' ) DEFAULT 'folder' NOT NULL");
    log_close_block ();
  }

  public function add_attachment_permissions($folder_table_name, $user_table_name)
  {
    log_open_block ("Add attachment permissions...");
      $this->_query ("ALTER TABLE `$user_table_name` ADD `deny_attachment_permissions` SMALLINT UNSIGNED NOT NULL AFTER `deny_entry_permissions` ;");
      $this->_query ("ALTER TABLE `$user_table_name` ADD `allow_attachment_permissions` SMALLINT UNSIGNED NOT NULL AFTER `allow_entry_permissions` ;");
      $this->_query ("ALTER TABLE `$folder_table_name` ADD `attachment_permissions` SMALLINT UNSIGNED NOT NULL AFTER `entry_permissions` ;");
    log_close_block ();
  }
}

?>