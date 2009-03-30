<?php

/****************************************************************************

Copyright (c) 2002-2008 Marco Von Ballmoos

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
require_once ('webcore/tasks/db/upgrade_per_app_221_23.php');

class PROJECTS_141_15_MIGRATOR_TASK extends UPGRADE_PER_APP_221_23_TASK
{
  public $application_name = 'earthli Projects';
  public $version_from = '1.4.1';
  public $version_to = '1.5.0';

  protected function _execute ()
  {
    log_open_block ("Adding [time_needed] to JOB...");
      $this->_query ("ALTER TABLE `project_jobs` ADD `time_needed` DATETIME NOT NULL");
      $this->_query ("ALTER TABLE `project_objects` ADD `extra_description` TEXT NOT NULL");
    log_close_block ();

    log_open_block ("Updating names [revision => release]...");
      $this->_query ("ALTER TABLE `project_revisions` RENAME `project_releases`");
    log_close_block ();

    log_open_block ("Updating names [object => entry]...");
      $this->_query ("ALTER TABLE `project_objects` RENAME `project_entries`");
      $this->_query ("ALTER TABLE `project_subscriptions` CHANGE `watch_objects` `watch_entries` TINYINT( 4 ) UNSIGNED DEFAULT '0' NOT NULL");
      $this->_query ("ALTER TABLE `project_jobs` CHANGE `object_id` `entry_id` INT( 11 ) UNSIGNED DEFAULT '0' NOT NULL");
      $this->_query ("ALTER TABLE `project_changes` CHANGE `object_id` `entry_id` INT( 11 ) UNSIGNED DEFAULT '0' NOT NULL");
      $this->_query ("ALTER TABLE `project_comments` CHANGE `object_id` `entry_id` INT( 11 ) UNSIGNED DEFAULT '0' NOT NULL");
    log_close_block ();

    log_open_block ("Adding branching data structures...");
      $this->_query ("ALTER TABLE `project_entries` ADD `main_branch_id` INT( 11 ) UNSIGNED DEFAULT '0' NOT NULL");
      $this->_query ("CREATE TABLE project_branches (id int(10) unsigned NOT NULL auto_increment, folder_id int(10) unsigned NOT NULL default '0', parent_release_id int(10) unsigned NOT NULL default '0', state tinyint(3) unsigned NOT NULL default '1', title varchar(100) NOT NULL default '', description text NOT NULL, time_created datetime default NULL, time_modified datetime default NULL, creator_id int(10) unsigned NOT NULL default '0', modifier_id int(10) unsigned NOT NULL default '0', publication_state tinyint(3) unsigned NOT NULL default '0', PRIMARY KEY  (id), KEY id (id))");
      $this->_query ("CREATE TABLE `project_entries_to_branches` (`id` INT UNSIGNED NOT NULL AUTO_INCREMENT, `entry_id` INT UNSIGNED NOT NULL, `branch_id` INT UNSIGNED NOT NULL, `branch_release_id` INT UNSIGNED NOT NULL, PRIMARY KEY  (id), KEY id (id) )");
      $this->_query ("CREATE TABLE `project_jobs_to_branches` (`entry_to_branch_id` INT UNSIGNED NOT NULL, `branch_status` TINYINT UNSIGNED,`branch_priority` TINYINT UNSIGNED,`branch_closer_id` INT UNSIGNED,`branch_time_closed` DATETIME)");
      $this->_query ("CREATE TABLE `project_changes_to_branches` (`entry_to_branch_id` INT UNSIGNED NOT NULL, `branch_applier_id` INT UNSIGNED,`branch_time_applied` DATETIME)");
      $this->_query ("ALTER TABLE `project_folders` ADD `trunk_id` INT( 11 ) UNSIGNED DEFAULT '0' NOT NULL");
      $this->_query ("ALTER TABLE `project_releases` CHANGE `folder_id` `branch_id` INT( 10 ) UNSIGNED DEFAULT '0' NOT NULL");
      $this->_query ("ALTER TABLE `project_releases` ADD `time_scheduled` DATETIME NOT NULL AFTER `description`");
      $this->_query ("ALTER TABLE `project_changes` ADD `applier_id` INT UNSIGNED, ADD `time_applied` DATETIME");
    log_close_block ();

    log_open_block ("Creating [Dev] branch for each project...");

      $this->_query ("SELECT * from project_folders");

      $folder_ids = array ();
      while ($Page->database->next_record ())
        $folder_ids [] = $Page->database->f ('id');

      $branch_map = array ();
      foreach ($folder_ids as $id)
      {
        $this->_query ("INSERT INTO project_branches (folder_id, title, time_created, time_modified, creator_id, modifier_id) VALUES($id, 'Dev Branch', NOW(), NOW(), $default_user_id, $default_user_id)");
        $this->_query ("SELECT MAX(id) from project_branches");
        $Page->database->next_record ();
        $branch_id = $Page->database->f (0);
        $this->_query ("UPDATE project_folders SET trunk_id = $branch_id WHERE id = $id");
        $branch_map [$id] = $branch_id;
      }

      $this->_query ("SELECT * from project_releases");

      $release_ids = array ();
      while ($Page->database->next_record ())
        $release_ids [$Page->database->f ('id')] = $Page->database->f ('branch_id');
    log_close_block ();

    log_open_block ("Mapping releases to [Dev] branches...");

      foreach ($release_ids as $id => $folder_id)
      {
        $branch_id = $branch_map [$folder_id];
        if (! $branch_id)
        {
          $this->_log ("Did not find mapping for id = [$id], folder id = [$folder_id]", Msg_type_warning);
        }
        else
        {
          $this->_query ("UPDATE project_releases SET time_scheduled = time_created, branch_id = $branch_id, state = 5 WHERE id = $id");
        }
      }
    log_close_block ();

    log_open_block ("Mapping changes to [Dev] branches...");

      $this->_query ("SELECT * from project_entries WHERE type = 'change'");

      $change_ids = array ();
      while ($Page->database->next_record ())
        $change_ids [$Page->database->f ('id')] = array ($Page->database->f ('folder_id'),
                                                         $Page->database->f ('revision_id'),
                                                         $Page->database->f ('time_created'),
                                                         $Page->database->f ('creator_id'));

      foreach ($change_ids as $id => $other_ids)
      {
        $folder_id = $other_ids [0];
        $branch_id = $branch_map [$folder_id];
        $revision_id = $other_ids [1];
        $time_applied = $other_ids [2];
        $applier_id = $other_ids [3];

        if (! $branch_id)
        {
          log_message ("No project found for id [$folder_id] (change [$id] not imported).", Msg_type_warning, Msg_channel_migrate);
        }
        else
        {
          $this->_query ("INSERT INTO project_entries_to_branches (entry_id, branch_id, branch_release_id) VALUES ($id, $branch_id, $revision_id)");
          $this->_query ("SELECT MAX(id) from project_entries_to_branches");
          $Page->database->next_record ();
          $entry_to_branch_id = $Page->database->f (0);
          $this->_query ("INSERT INTO project_changes_to_branches (entry_to_branch_id, branch_time_applied, branch_applier_id) VALUES ($entry_to_branch_id, '$time_applied', $applier_id)");
          $this->_query ("UPDATE project_entries SET main_branch_id = $branch_id WHERE id = $id");
          $this->_query ("UPDATE project_changes SET applier_id = $applier_id, time_applied = '$time_applied' WHERE entry_id = $id");
        }
      }
    log_close_block ();

    log_open_block ("Converting job statuses (building job status map)...");

      // convert re-opened status
      $this->_query ("UPDATE project_jobs SET status = 1 WHERE status = 50");
      // convert closed status
      $this->_query ("UPDATE project_jobs SET status = 9 WHERE status = 100");
      // convert abandoned status
      $this->_query ("UPDATE project_jobs SET status = 10 WHERE status = 150");
      // convert working status
      $this->_query ("UPDATE project_jobs SET status = 3 WHERE status = 70");
      // convert fixed status
      $this->_query ("UPDATE project_jobs SET status = 7 WHERE status = 90");
    log_close_block ();

    log_open_block ("Converting job priorities (building job priority map)...");

      // convert "Not needed for release"
      $this->_query ("UPDATE project_jobs SET priority = 0 WHERE priority = 10");
      // convert "Needed for release"
      $this->_query ("UPDATE project_jobs SET priority = 1 WHERE priority = 20");
      // convert "Needed for release: workaround exists"
      $this->_query ("UPDATE project_jobs SET priority = 2 WHERE priority = 30");
      // convert "Needed for release: no workaround found"
      $this->_query ("UPDATE project_jobs SET priority = 3 WHERE priority = 40");
      // convert "Showstopper: cannot continue working"
      $this->_query ("UPDATE project_jobs SET priority = 4 WHERE priority = 50");
    log_close_block ();

    log_open_block ("Mapping jobs to [Dev] branches...");

      $this->_query ("SELECT * from project_entries INNER JOIN project_jobs ON project_entries.id = project_jobs.entry_id");

      $job_ids = array ();
      while ($Page->database->next_record ())
        $job_ids [$Page->database->f ('id')] = array ($Page->database->f ('folder_id'),
                                                      $Page->database->f ('revision_id'),
                                                      $Page->database->f ('status'),
                                                      $Page->database->f ('priority'),
                                                      $Page->database->f ('closer_id'),
                                                      $Page->database->f ('owner_id'),
                                                      $Page->database->f ('time_closed'));

      foreach ($job_ids as $id => $other_ids)
      {
        $folder_id = $other_ids[0];
        $branch_id = $branch_map [$folder_id];

        $revision_id = $other_ids[1];
        $status = $other_ids[2];
        $priority = $other_ids[3];
        $closer_id = $other_ids[4];
        $owner_id = $other_ids[5];
        $time_closed = $other_ids[6];

        if (! $branch_id)
        {
          log_message ("No project found for id [$folder_id] (job [$id] not imported).", Msg_type_warning, Msg_channel_migrate);
        }
        else
        {
          $this->_query ("INSERT INTO project_entries_to_branches (entry_id, branch_id, branch_release_id) VALUES ($id, $branch_id, $revision_id)");
          $this->_query ("SELECT MAX(id) from project_entries_to_branches");
          $Page->database->next_record ();
          $entry_to_branch_id = $Page->database->f (0);
          $this->_query ("INSERT INTO project_jobs_to_branches (entry_to_branch_id, branch_status, branch_priority, branch_closer_id, branch_time_closed) VALUES ($entry_to_branch_id, $status, $priority, $closer_id, '$time_closed')");
          $this->_query ("UPDATE project_entries SET main_branch_id = $branch_id WHERE id = $id");
        }
      }
    log_close_block ();

    log_open_block ("Renaming [revision_id] to [release_id] in entries...");
      $this->_query ("ALTER TABLE `project_entries` CHANGE `revision_id` `release_id` INT( 11 ) UNSIGNED DEFAULT '0' NOT NULL");
    log_close_block ();

    log_open_block ("Removing [priority, status, closer_id, time_closed] from jobs...");
      $this->_query ("ALTER TABLE `project_jobs` DROP `priority`, DROP `status`, DROP `closer_id`, DROP `time_closed`");
    log_close_block ();

    log_open_block ("Building project tree...");
      $this->_query ("CREATE TABLE `project_tree` (`parent_id` INT UNSIGNED NOT NULL ,`child_id` INT UNSIGNED NOT NULL)");

      $this->_query ("SELECT * from project_folders");
      build_folder_roots ('project_tree');

    log_close_block ();
  }
}

?>