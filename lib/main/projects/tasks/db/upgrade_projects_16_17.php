<?php

/****************************************************************************

Copyright (c) 2002-2014 Marco Von Ballmoos

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
require_once ('webcore/tasks/db/upgrade_per_app_24_25.php');

class PROJECTS_16_17_MIGRATOR_TASK extends UPGRADE_PER_APP_24_25_TASK
{
  public $application_name = 'earthli Projects';
  public $version_from = '1.6.0';
  public $version_to = '1.7.0';

  protected function _execute ()
  {
    $this->update_actions ('project_actions', 'project_comments', 'project_folders', 'project_entries');
    $this->update_subscriptions ('project_subscribers', 'project_subscriptions');
    $this->update_permissions ('project_folder_permissions', 'project_user_permissions');
    $this->update_folders ('project_folders');
    $this->update_users ('project_user_permissions');

    log_open_block ("Adding extended times to jobs");
      $this->_query ('ALTER TABLE `project_jobs` ADD `time_status_changed` DATETIME NOT NULL ;');
      $this->_query ('ALTER TABLE `project_jobs_to_branches` ADD `branch_time_status_changed` DATETIME NOT NULL ;');
      $this->_query ('ALTER TABLE `project_jobs` ADD `time_owner_changed` DATETIME NOT NULL ;');
    log_close_block ();

    log_open_block ("Adding new status to releases");
      $this->_query ('ALTER TABLE `project_releases` ADD `time_shipped` DATETIME NOT NULL AFTER `time_scheduled` ;');
      $this->_query ('UPDATE `project_releases` SET time_shipped = time_scheduled WHERE (state = ' . Locked . ') OR (state = ' . Shipped . ')');
    log_close_block ();

    log_open_block ("Adding components");
      $this->_query ("ALTER TABLE `project_actions` CHANGE `object_type` `object_type` ENUM( 'folder', 'entry', 'comment', 'group', 'user', 'branch', 'release', 'component' ) DEFAULT 'folder' NOT NULL");
      $this->_query ("CREATE TABLE `project_components` (`id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,`folder_id` INT UNSIGNED NOT NULL ,`title` VARCHAR( 100 ) NOT NULL ,`description` TEXT NOT NULL ,`icon_url` VARCHAR( 250 ) NOT NULL ,INDEX ( `id` ) );");
      $this->_query ("ALTER TABLE `project_components` ADD `creator_id` INT UNSIGNED NOT NULL ,ADD `modifier_id` INT UNSIGNED NOT NULL ,ADD `time_created` DATETIME NOT NULL ,ADD `time_modified` DATETIME NOT NULL ;");
      $this->_query ("ALTER TABLE `project_components` ADD `state` TINYINT UNSIGNED NOT NULL AFTER `icon_url` ;");
      $this->_query ("ALTER TABLE `project_entries` ADD `component_id` INT UNSIGNED NOT NULL AFTER `folder_id` ;");
    log_close_block ();
  }
}

?>