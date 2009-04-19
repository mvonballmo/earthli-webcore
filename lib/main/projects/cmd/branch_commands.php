<?php

/**
 * @copyright Copyright (c) 2002-2009 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package projects
 * @subpackage command
 * @version 3.1.0
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
require_once ('webcore/cmd/commands.php');

/**
 * Commands which apply to an {@link BRANCH}.
 * @package projects
 * @subpackage command
 * @version 3.1.0
 * @since 1.9.0
 * @access private
 */
class BRANCH_COMMANDS extends COMMANDS
{
  /**
   * @param BRANCH $obj Configure commands for this branch.
   */
  public function __construct ($obj)
  {
    parent::__construct ($obj->app);

    $cmd = $this->make_command ();
    $this->append_group ('Edit');
    $cmd->id = 'edit';
    $cmd->title = 'Edit';
    $cmd->link = "edit_branch.php?id=$obj->id";
    $cmd->icon = '{icons}buttons/edit';
    $cmd->executable = $this->login->is_allowed (Privilege_set_folder, Privilege_modify, $obj);
    $cmd->importance = Command_importance_high - Command_importance_increment;
    $this->append ($cmd);

    $folder = $obj->parent_folder ();

    if (! $folder->is_root ())
    {
      $cmd = $this->make_command ();
      $cmd->id = 'delete';
      $cmd->title = 'Delete';
      $cmd->link = "delete_branch.php?id=$obj->id";
      $cmd->icon = '{icons}buttons/delete';
      $cmd->executable = ! $obj->deleted () && $this->login->is_allowed (Privilege_set_folder, Privilege_modify, $obj);
      $cmd->importance = Command_importance_high - 2 * Command_importance_increment;
      $this->append ($cmd);

      $cmd = $this->make_command ();
      $cmd->id = 'purge';
      $cmd->title = 'Purge';
      $cmd->link = "purge_branch.php?id=$obj->id";
      $cmd->icon = '{icons}buttons/purge';
      $cmd->executable = $this->login->is_allowed (Privilege_set_folder, Privilege_purge, $obj);
      $cmd->importance = Command_importance_high - 2 * Command_importance_increment;
      $this->append ($cmd);
    }

    $this->append_group ('View');

    $cmd = $this->make_command ();
    $cmd->id = 'print';
    $cmd->title = 'Print';
    $cmd->link = "view_branch_change_log.php?id=$obj->id&printable=1";
    $cmd->icon = '{icons}buttons/print';
    $cmd->executable = true;
    $cmd->importance = Command_importance_high - Command_importance_increment;
    $this->append ($cmd);

    $cmd = $this->make_command ();
    $cmd->id = 'history';
    $cmd->title = 'History';
    $cmd->link = "view_branch_history.php?id=$obj->id";
    $cmd->icon = '{icons}buttons/history';
    $cmd->executable = $this->login->is_allowed (Privilege_set_folder, Privilege_view_history, $obj);
    $cmd->importance = Command_importance_low;
    $this->append ($cmd);

    $cmd = $this->make_command ();
    $cmd->id = 'change_log';
    $cmd->title = 'View change log';
    $cmd->link = "view_branch_change_log.php?id=$obj->id";
    $cmd->icon = '{app_icons}buttons/change_log';
    $cmd->executable = true;
    $cmd->importance = Command_importance_high;
    $this->append ($cmd);

    if (! $folder->is_organizational () && ! $obj->locked ())
    {
      $this->append_group ('Create');

      $cmd = $this->make_command ();
      $cmd->id = 'new_release';
      $cmd->title = 'New release';
      $cmd->link = "create_release.php?id=$obj->id";
      $cmd->icon = '{app_icons}buttons/new_release';
      $cmd->executable = $this->login->is_allowed (Privilege_set_folder, Privilege_modify, $obj);
      $cmd->importance = Command_importance_low;
      $this->append ($cmd);

      $cmd = $this->make_command ();
      $cmd->id = 'new_branch';
      $cmd->title = 'New branch';
      $cmd->link = "create_branch.php?id=$folder->id&branch_id=$obj->id";
      $cmd->icon = '{app_icons}buttons/new_branch';
      $cmd->executable = $this->login->is_allowed (Privilege_set_folder, Privilege_modify, $obj);
      $cmd->importance = Command_importance_high - 2 * Command_importance_increment;
      $cmd->importance = Command_importance_low;
      $this->append ($cmd);

      $cmd = $this->make_command ();
      $cmd->id = 'new_job';
      $cmd->title = 'New job';
      $cmd->link = "create_job.php?id=$folder->id&branch_id=$obj->id";
      $cmd->icon = '{app_icons}buttons/new_job';
      $cmd->executable = $this->login->is_allowed (Privilege_set_entry, Privilege_create, $obj);
      $cmd->importance = Command_importance_high + Command_importance_increment;
      $this->append ($cmd);

      $cmd = $this->make_command ();
      $cmd->id = 'new_change';
      $cmd->title = 'New change';
      $cmd->link = "create_change.php?id=$folder->id&branch_id=$obj->id";
      $cmd->icon = '{app_icons}buttons/new_change';
      $cmd->executable = $this->login->is_allowed (Privilege_set_entry, Privilege_create, $obj);
      $cmd->importance = Command_importance_high + Command_importance_increment;
      $this->append ($cmd);
    }
  }
}

?>