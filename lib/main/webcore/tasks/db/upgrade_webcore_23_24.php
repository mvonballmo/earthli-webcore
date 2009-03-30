<?php

/****************************************************************************

Copyright (c) 2002-2008 Marco Von Ballmoos

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

require_once ('webcore/init.php');
require_once ('webcore/db/migrator_task.php');

class UPGRADE_WEBCORE_23_24_TASK extends MIGRATOR_TASK
{
  public $application_name = 'earthli WebCore';
  public $version_from = '2.3.0';
  public $version_to = '2.4.0';

  protected function _create_actions ($obj_type)
  {
    global $Page;

    while ($Page->database->next_record ())
    {
      $id = $Page->database->f ('id');
      if (! $id)
      {
        $id = $Page->database->f ('entry_id');
      }

      $objs [$id] = array ($Page->database->f ('time_created'),
                           $Page->database->f ('creator_id'),
                           $Page->database->f ('time_modified'),
                           $Page->database->f ('modifier_id'),
                           $Page->database->f ('publication_state'));
    }


    $this->_log ("Building history for [" . sizeof ($objs) . "] {$obj_type}s...", Msg_type_info);

    foreach ($objs as $id => $obj)
    {
      $creator_id = $obj [1];
      $time_created = $obj [0];

      $pub = Action_sent;

      $this->_query ('INSERT INTO project_actions (object_id, object_type, user_id, time_created, publication_state, title)' .
              " VALUES($id, '$obj_type', $creator_id, '$time_created', '$pub', 'Created')");

      $modifier_id = $obj [3];
      $time_modified = $obj [2];

      if ($time_modified != $time_created)
      {
        $this->_query ('INSERT INTO project_actions (object_id, object_type, user_id, time_created, publication_state, title)' .
                       " VALUES($id, '$obj_type', $modifier_id, '$time_modified', '$pub', 'Updated')");
      }
    }
  }

  protected function _execute ()
  {
    log_open_block ("Adding actions table");
      $this->_query ("CREATE TABLE `project_actions` (`id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,`object_id` INT UNSIGNED NOT NULL ,`object_type` ENUM( 'folder', 'entry', 'comment', 'group', 'user', 'branch', 'release' ) NOT NULL ,`user_id` INT UNSIGNED NOT NULL ,`time_created` DATETIME NOT NULL ,`publication_state` ENUM( 'silent', 'published', 'queued' ) NOT NULL ,`title` VARCHAR( 200 ) NOT NULL ,`description` TEXT NOT NULL ,`system_description` TEXT NOT NULL ,PRIMARY KEY ( `id` ) ,INDEX ( `object_id` , `object_type` ) );");
    log_close_block ();

    log_open_block ("Importing group history...");
      $this->_query ("SELECT * from groups");
      $this->_create_actions ('group');
    log_close_block ();

    log_open_block ("Importing user history...");
      $this->_query ("SELECT * from users");
      $this->_create_actions ('user');
    log_close_block ();
  }
}

?>