<?php

/****************************************************************************

Copyright (c) 2002-2010 Marco Von Ballmoos

This file is part of earthli Projects.

earthli Projects is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

earthli Projects is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with earthli Projects; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

For more information about the earthli Projects, visit:

http://www.earthli.com/software/webcore/projects

****************************************************************************/

require_once ('webcore/init.php');
require_once ('webcore/tasks/db/upgrade_per_app_23_24.php');

class PROJECTS_15_16_MIGRATOR_TASK extends UPGRADE_PER_APP_23_24_TASK
{
  public $application_name = 'earthli Projects';
  public $version_from = '1.5.0';
  public $version_to = '1.6.0';

  protected function _execute ()
  {
    log_open_block ("Repairing change to branch map...");

      $this->_query ("SELECT entry.id as entry_id, time_created, creator_id, etob.id as entry_to_branch_id from project_entries entry INNER JOIN project_entries_to_branches etob ON entry.id = etob.entry_id LEFT JOIN project_changes_to_branches ctob on etob.id = ctob.entry_to_branch_id WHERE ISNULL(ctob.entry_to_branch_id)");

      $changes = array ();

      while ($Page->database->next_record ())
      {
        $changes [$Page->database->f ('entry_to_branch_id')] = array ($Page->database->f ('time_created'),
                                                                      $Page->database->f ('creator_id'),
                                                                      $Page->database->f ('entry_id'));
      }

      if (sizeof ($changes))
      {
        log_message ("Repairing [" . sizeof ($changes) . "] changes...", Msg_type_warning, Msg_channel_migrate);

        foreach ($changes as $etob_id => $change)
        {
          $time_applied = $change [0];
          $applier_id = $change [1];
          $change_id = $change [2];

          $this->_query ("INSERT INTO project_changes_to_branches (entry_to_branch_id, branch_time_applied, branch_applier_id) VALUES ($etob_id, '$time_applied', $applier_id)");
          $this->_query ("UPDATE project_changes SET applier_id = $applier_id, time_applied = '$time_applied' WHERE entry_id = $change_id");
        }
      }

    log_close_block ();

    log_open_block ("Cleaning text for comments...");
      $this->clean_text ('project_comments');
    log_close_block ();

    log_open_block ("Cleaning text for folders...");
      $this->clean_text ('project_folders');
    log_close_block ();

    log_open_block ("Cleaning text for entries...");
      $this->clean_text ('project_entries');
    log_close_block ();

    log_open_block ("Cleaning text for releases...");
      $this->clean_text ('project_releases');
    log_close_block ();

    log_open_block ("Cleaning text for branches...");
      $this->clean_text ('project_branches');
    log_close_block ();

    log_open_block ("Updating subcribers table");
      $this->_query ("ALTER TABLE `project_subscribers` CHANGE `send_as_newsletter` `group_objects` TINYINT( 4 ) DEFAULT '0' NOT NULL;");
      $this->_query ("ALTER TABLE `project_subscribers` ADD `group_actions` TINYINT DEFAULT '0' NOT NULL AFTER `group_objects`;");
    log_close_block ();

    log_open_block ("Adding actions table");
      $this->_query ("CREATE TABLE `project_actions` (`id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,`object_id` INT UNSIGNED NOT NULL ,`object_type` ENUM( 'folder', 'entry', 'comment', 'group', 'user', 'branch', 'release' ) NOT NULL ,`access_id` INT UNSIGNED NOT NULL ,`kind` ENUM( 'Created', 'Updated', 'Deleted', 'Restored', 'Hidden', 'Hidden update' ) NOT NULL ,`user_id` INT UNSIGNED NOT NULL ,`time_created` DATETIME NOT NULL ,`publication_state` ENUM( 'silent', 'published', 'queued' ) NOT NULL ,`title` VARCHAR( 200 ) NOT NULL ,`description` TEXT NOT NULL ,`system_description` TEXT NOT NULL ,PRIMARY KEY ( `id` ) ,INDEX ( `object_id` , `object_type` ) );");
    log_close_block ();

    log_open_block ("Importing folder history...");
      $this->_query ("SELECT * from project_folders");
      $this->_create_actions ('folder', 'project_actions');
    log_close_block ();

    log_open_block ("Importing change history...");
      $this->_query ("SELECT * from project_entries WHERE type = 'change'");
      $this->_create_actions ('entry', 'project_actions');
    log_close_block ();

    log_open_block ("Importing job history...");
      $this->_query ("SELECT * from project_entries WHERE type = 'job'");
      $this->_create_actions ('entry', 'project_actions');
    log_close_block ();

    log_open_block ("Importing release history...");
      $this->_query ("SELECT rel.*, bra.folder_id from project_releases rel INNER JOIN project_branches bra ON rel.branch_id = bra.id");
      $this->_create_actions ('release', 'project_actions');
    log_close_block ();

    log_open_block ("Importing branch history...");
      $this->_query ("SELECT * from project_branches");
      $this->_create_actions ('branch', 'project_actions');
    log_close_block ();

    log_open_block ("Importing comment history...");
      $this->_query ("SELECT com.*, entry.folder_id from project_comments com INNER JOIN project_entries entry ON entry.id = com.entry_id");
      $this->_create_actions ('comment', 'project_actions');
    log_close_block ();

    log_open_block ("Importing group history...");
      $this->_query ("SELECT * from groups");
      $this->_create_actions ('group', 'project_actions');
    log_close_block ();

    log_open_block ("Importing user history...");
      $this->_query ("SELECT * from users");
      $this->_create_actions ('user', 'project_actions');
    log_close_block ();
  }
}

?>