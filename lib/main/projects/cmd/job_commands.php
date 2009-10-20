<?php

/**
 * @copyright Copyright (c) 2002-2009 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package projects
 * @subpackage command
 * @version 3.2.0
 * @since 1.9.0
 * @access private
 */

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

/** */
require_once ('webcore/cmd/entry_commands.php');

/**
 * Return the commands for a {@link JOB}.
 * @package projects
 * @subpackage command
 * @version 3.2.0
 * @since 1.9.0
 * @access private
 */
class JOB_COMMANDS extends ENTRY_COMMANDS
{
  /**
   * @param JOB $entry Configure commands for this object.
   */
  public function __construct ($entry)
  {
    parent::__construct($entry);

    $cmd = $this->command_at ('edit');
    $cmd->link = "edit_job.php?id=$entry->id";

    $cmd = $this->command_at ('delete');
    $cmd->link = "delete_job.php?id=$entry->id";

    $cmd = $this->command_at ('purge');
    $cmd->link = "purge_job.php?id=$entry->id";

    $cmd = $this->command_at ('clone');
    $cmd->link = "clone_job.php?id=$entry->id";

    $cmd = $this->command_at ('send');
    $cmd->link = "send_job.php?id=$entry->id";
  }

  /**
   * Add commands that edit the entry.
   * @param JOB $entry
   * @access private
   */
  protected function _add_editors ($entry)
  {
    parent::_add_editors ($entry);

    $branch_info = $entry->main_branch_info ();
    $last_page = urlencode ($this->env->url (Url_part_all));

    $branch = $branch_info->branch ();
    $release = $branch_info->release ();
    $locked = $branch->locked () || (isset ($release) && $release->locked ());

    $cmd = $this->make_command ();
    $cmd->id = 'work';
    $cmd->title = 'Start working';
    $cmd->link = "set_job_status.php?id=$entry->id&status=3&branch_id=$branch_info->branch_id&last_page=$last_page";
    $cmd->icon = '{app_icons}statuses/working';
    $cmd->importance = Command_importance_high + Command_importance_increment;
    $cmd->executable = ! $locked
                       && ! $branch_info->is_closed ()
                       && ($branch_info->status != 3)
                       && $this->login->is_allowed (Privilege_set_entry, Privilege_modify, $entry);
    $this->append ($cmd);

    $cmd = $this->make_command ();
    $cmd->id = 'work';
    $cmd->title = 'Stop working';
    $cmd->link = "set_job_status.php?id=$entry->id&status=4&branch_id=$branch_info->branch_id&last_page=$last_page";
    $cmd->icon = '{app_icons}statuses/stopped';
    $cmd->importance = Command_importance_high + Command_importance_increment;
    $cmd->executable = ! $locked
                       && ! $branch_info->is_closed ()
                       && ($branch_info->status == 3)
                       && $this->login->is_allowed (Privilege_set_entry, Privilege_modify, $entry);
    $this->append ($cmd);
    
    $cmd = $this->make_command ();
    $cmd->id = 'fix';
    $cmd->title = 'Mark as fixed';
    $cmd->link = "set_job_status.php?id=$entry->id&status=7&branch_id=$branch_info->branch_id&last_page=$last_page";
    $cmd->icon = '{app_icons}statuses/released';
    $cmd->importance = Command_importance_high + Command_importance_increment;
    $cmd->executable = ! $locked
                       && ! $branch_info->is_closed ()
                       && ($branch_info->status != 7)
                       && $this->login->is_allowed (Privilege_set_entry, Privilege_modify, $entry);
    $this->append ($cmd);

    $cmd = $this->make_command ();
    $cmd->id = 'reopen';
    $cmd->title = 'Re-open';
    $cmd->link = "set_job_status.php?id=$entry->id&status=1&branch_id=$branch_info->branch_id&last_page=$last_page";
    $cmd->icon = '{app_icons}statuses/reopened';
    $cmd->importance = Command_importance_high + Command_importance_increment;
    $cmd->executable = ! $locked
                       && $branch_info->is_closed ()
                       && ($branch_info->status != 1)
                       && $this->login->is_allowed (Privilege_set_entry, Privilege_modify, $entry);
    $this->append ($cmd);
  }

  /**
   * Add commands that attach objects to the entry.
   * @param JOB $entry
   * @param PROJECT $folder Entry is in this folder (also available as $entry-
   * >parent_folder ()).
   * @param USER $creator Folder belongs to this user (also available as $folder->creator ()).
   * @access private
   */
  protected function _add_creators ($entry)
  {
    parent::_add_creators ($entry);

    $folder_id = $entry->parent_folder_id ();

    $cmd = $this->make_command ();
    $cmd->id = 'new_change';
    $cmd->title = 'Add change';
    $cmd->link = "create_change.php?id=$folder_id&job_id=$entry->id";
    $cmd->icon = '{app_icons}buttons/new_change';
    $cmd->executable = $this->login->is_allowed (Privilege_set_entry, Privilege_create, $entry);
    $this->append($cmd);
  }
}

?>