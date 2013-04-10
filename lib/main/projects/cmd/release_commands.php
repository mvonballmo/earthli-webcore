<?php

/**
 * @copyright Copyright (c) 2002-2013 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package projects
 * @subpackage command
 * @version 3.3.0
 * @since 1.9.0
 * @access private
 */

/****************************************************************************

Copyright (c) 2002-2013 Marco Von Ballmoos

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
 * Return the commands for a {@link RELEASE}.
 * @package projects
 * @subpackage command
 * @version 3.3.0
 * @since 1.9.0
 * @access private
 */
class RELEASE_COMMANDS extends COMMANDS
{
  /**
   * @param RELEASE $obj Configure commands for this object.
   */
  public function __construct ($obj)
  {
    parent::__construct ($obj->app);

    $this->append_group ('Edit');

    $cmd = $this->make_command ();
    $cmd->id = 'edit';
    $cmd->caption = 'Edit';
    $cmd->link = "edit_release.php?id=$obj->id";
    $cmd->icon = '{icons}buttons/edit';
    $cmd->executable = $this->login->is_allowed (Privilege_set_release, Privilege_modify, $obj);
    $cmd->importance = Command_importance_high - Command_importance_increment;
    $this->append ($cmd);

    $cmd = $this->make_command ();
    $cmd->id = 'delete';
    $cmd->caption = 'Delete';
    $cmd->link = "delete_release.php?id=$obj->id";
    $cmd->icon = '{icons}buttons/delete';
    $cmd->executable = ! $obj->deleted () && $this->login->is_allowed (Privilege_set_release, Privilege_delete, $obj);
    $cmd->importance = Command_importance_high - 2 * Command_importance_increment;
    $this->append ($cmd);

    $cmd = $this->make_command ();
    $cmd->id = 'purge';
    $cmd->caption = 'Purge';
    $cmd->link = "purge_release.php?id=$obj->id";
    $cmd->icon = '{icons}buttons/purge';
    $cmd->executable = $this->login->is_allowed (Privilege_set_release, Privilege_purge, $obj);
    $cmd->importance = Command_importance_high - 2 * Command_importance_increment;
    $this->append ($cmd);

    $cmd = $this->make_command ();
    $cmd->id = 'ship';
    $cmd->caption = 'Ship';
    $cmd->link = "ship_release.php?id=$obj->id";
    $cmd->icon = '{icons}buttons/ship';
    $cmd->executable = $obj->planned () && $this->login->is_allowed (Privilege_set_release, Privilege_modify, $obj);
    $cmd->importance = Command_importance_high - Command_importance_increment;
    $this->append ($cmd);

    $this->append_group ('View');

    $cmd = $this->make_command ();
    $cmd->id = 'print';
    $cmd->caption = 'Print';
    $cmd->link = "view_release_change_log.php?id=$obj->id&printable=1";
    $cmd->icon = '{icons}buttons/print';
    $cmd->executable = true;
    $cmd->importance = Command_importance_low;
    $this->append ($cmd);

    $cmd = $this->make_command ();
    $cmd->id = 'history';
    $cmd->caption = 'View history';
    $cmd->link = "view_release_history.php?id=$obj->id";
    $cmd->icon = '{icons}buttons/history';
    $cmd->executable = $this->login->is_allowed (Privilege_set_release, Privilege_view_history, $obj);
    $cmd->importance = Command_importance_low;
    $this->append ($cmd);

    $cmd = $this->make_command ();
    $cmd->id = 'change_log';
    $cmd->caption = 'View change log';
    $cmd->link = "view_release_change_log.php?id=$obj->id";
    $cmd->icon = '{app_icons}buttons/change_log';
    $cmd->executable = true;
    $cmd->importance = Command_importance_high;
    $this->append ($cmd);

    $folder = $obj->parent_folder ();
    $branch = $obj->branch ();

    if (! $folder->is_organizational ())
    {
      $this->append_group ('Add');

      $cmd = $this->make_command ();
      $cmd->id = 'new_job';
      $cmd->caption = 'New job';
      $cmd->link = "create_job.php?id=$folder->id&branch_id=$branch->id&release_id=$obj->id";
      $cmd->icon = '{app_icons}buttons/new_job';
      $cmd->executable = ! $obj->locked () && $this->login->is_allowed (Privilege_set_entry, Privilege_create, $obj);
      $cmd->importance = Command_importance_high;
      $this->append ($cmd);

      $cmd = $this->make_command ();
      $cmd->id = 'new_change';
      $cmd->caption = 'New change';
      $cmd->link = "create_change.php?id=$folder->id&branch_id=$branch->id&release_id=$obj->id";
      $cmd->icon = '{app_icons}buttons/new_change';
      $cmd->executable = ! $obj->locked () && $this->login->is_allowed (Privilege_set_entry, Privilege_create, $obj);
      $cmd->importance = Command_importance_high;
      $this->append ($cmd);
    }
  }
}

?>