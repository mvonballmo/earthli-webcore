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
require_once ('webcore/tasks/db/upgrade_per_app_25_26.php');

class PROJECT_17_18_MIGRATOR_TASK extends UPGRADE_PER_APP_25_26_TASK
{
  public $application_name = 'earthli Projects';
  public $version_from = '1.7.0';
  public $version_to = '1.8.0';

  protected function _execute ()
  {
    log_open_block ("Adding test time to release");
      $this->_query ('ALTER TABLE `project_releases` ADD `time_testing_scheduled` DATETIME AFTER `time_scheduled` ;');
      $this->_query ('ALTER TABLE `project_releases` ADD `time_tested` DATETIME AFTER `time_scheduled` ;');
      $this->_query ('ALTER TABLE `project_releases` ADD `time_next_deadline` DATETIME AFTER `time_scheduled` ;');
      $this->_query ('ALTER TABLE `project_releases` ADD `summary` TEXT');
      $this->_query ('ALTER TABLE `project_releases` ADD FULLTEXT (`summary`)');
    log_close_block ();

    log_open_block ("Changing owner to assignee");
      $this->_query ('ALTER TABLE `project_options` CHANGE `owner_group_type` `assignee_group_type` TINYINT( 4 ) DEFAULT \'0\' NOT NULL');
      $this->_query ('ALTER TABLE `project_options` CHANGE `owner_group_id` `assignee_group_id` INT( 10 ) UNSIGNED DEFAULT \'0\' NOT NULL');
      $this->_query ('ALTER TABLE `project_jobs` CHANGE `owner_id` `assignee_id` INT( 10 ) UNSIGNED DEFAULT \'0\' NOT NULL');
      $this->_query ('ALTER TABLE `project_jobs` CHANGE `time_owner_changed` `time_assignee_changed` DATETIME DEFAULT \'0000-00-00 00:00:00\' NOT NULL');
    log_close_block ();

    $this->add_attachments ('project_attachments', 'project_actions');
    $this->_query ("ALTER TABLE `project_actions` CHANGE `object_type` `object_type` ENUM( 'folder', 'entry', 'comment', 'group', 'user', 'branch', 'release', 'component', 'attachment' ) DEFAULT 'folder' NOT NULL");
    $this->_query ("ALTER TABLE `project_branches` DROP `publication_state`");
    $this->_query ("ALTER TABLE `project_releases` DROP `publication_state`");
    $this->add_attachment_permissions ('project_folder_permissions', 'project_user_permissions');

    log_open_block ("Adding owners to objects");
      $this->add_owner_to_table ('project_attachments');
      $this->add_owner_to_table ('project_comments');
      $this->add_owner_to_table ('project_entries');
      $this->add_owner_to_table ('project_folders');
      $this->add_owner_to_table ('project_releases');
      $this->add_owner_to_table ('project_branches');
      $this->add_owner_to_table ('project_components');
    log_close_block ();

    $this->add_version_info ('project_application', 'earthli Projects', '1.8.0');
    $this->add_organizational ('project_folders');

    log_open_block ('Replacing obsolete tags');
      $this->_query ("UPDATE project_entries SET description=REPLACE(description, '<code>', '<c>') WHERE LOCATE('<code>', description) > 0;");
      $this->_query ("UPDATE project_entries SET description=REPLACE(description, '</code>', '</c>') WHERE LOCATE('</code>', description) > 0;");
    log_close_block ();

    log_open_block ('Adding project options');
      $this->_query ("ALTER TABLE `project_options` ADD `seconds_until_deadline` INT UNSIGNED NOT NULL ;");
    log_close_block ();
    
    log_open_block ('Adding fulltext indexes');
      $this->_query ('ALTER TABLE `project_entries` ADD FULLTEXT (`extra_description`)');
    log_close_block ();
  }
}

?>