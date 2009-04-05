<?php

/****************************************************************************

Copyright (c) 2002-2009 Marco Von Ballmoos

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
require_once ('webcore/tasks/db/upgrade_per_app_260_270.php');

class PROJECT_180_190_MIGRATOR_TASK extends UPGRADE_PER_APP_260_270_TASK
{
  public $application_name = 'earthli Projects';
  public $version_from = '1.8.0';
  public $version_to = '1.9.0';

  protected function _execute ()
  {
    log_open_block ("Cleaning up indexes");
      $this->clean_up_folder_permissions ('project_folder_permissions');
      $this->clean_up_user_permissions ('project_user_permissions');
      $this->clean_up_id_indexes ('project_entries');
      $this->clean_up_id_indexes ('project_folders');
      $this->clean_up_id_indexes ('project_comments');
      $this->clean_up_id_indexes ('project_entries_to_branches');
      $this->_query ('ALTER TABLE `project_changes` DROP INDEX `object_id`');
      $this->_query ('ALTER TABLE `project_changes` ADD PRIMARY KEY ( `entry_id` )');
      $this->_query ('ALTER TABLE `project_jobs` DROP INDEX `entry_id`');
      $this->_query ('ALTER TABLE `project_jobs` ADD PRIMARY KEY ( `entry_id` )');
      $this->_query ('ALTER TABLE `project_jobs_to_branches` DROP INDEX `entry_to_branch_id`');
      $this->_query ('ALTER TABLE `project_jobs_to_branches` ADD PRIMARY KEY ( `entry_to_branch_id` )');
      $this->_query ('ALTER TABLE `project_changes_to_branches` DROP INDEX `entry_to_branch_id`');
      $this->_query ('ALTER TABLE `project_changes_to_branches` ADD PRIMARY KEY ( `entry_to_branch_id` )');
      $this->clean_up_subscriptions ('project_subscriptions');
      $this->add_full_text_to_comments ('project_comments');
      $this->add_folder_id_index ('project_entries');
    log_close_block ();
    log_open_block ("Renaming subscriber fields");
      $this->rename_subscriber_fields ('project_subscribers');
      $this->rename_action_table ('project_actions', 'project_history_items');
    log_close_block ();
  }
}

?>