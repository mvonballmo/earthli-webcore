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
require_once ('webcore/cmd/folder_commands.php');

/**
 * Actions which apply to a {@link PROJECT}.
 * @package projects
 * @subpackage command
 * @version 3.1.0
 * @since 1.9.0
 * @access private
 */
class PROJECT_COMMANDS extends FOLDER_COMMANDS
{
  /**
   * @param PROJECT $folder Configure commands for this object.
   */
  public function __construct ($folder)
  {
    parent::__construct ($folder);

    $cmd = $this->command_at ('new');
    $cmd->title = 'New project';
  }

  /**
   * Add commands that create items in the folder.
   * @param FOLDER $folder
   * @access private
   */
  protected function _add_creators ($folder)
  {
    $cmd = $this->make_command ();
    $cmd->id = 'new_job';
    $cmd->title = 'New job';
    $cmd->link = "create_job.php?id=$folder->id";
    $cmd->icon = '{app_icons}buttons/new_job';
    $cmd->executable = $this->login->is_allowed (Privilege_set_entry, Privilege_create, $folder);
    $cmd->importance = Command_importance_high;
    $this->append ($cmd);

    $cmd = $this->make_command ();
    $cmd->id = 'new_change';
    $cmd->title = 'New change';
    $cmd->link = "create_change.php?id=$folder->id";
    $cmd->icon = '{app_icons}buttons/new_change';
    $cmd->executable = $this->login->is_allowed (Privilege_set_entry, Privilege_create, $folder);
    $cmd->importance = Command_importance_high;
    $this->append ($cmd);

    $cmd = $this->make_command ();
    $cmd->id = 'new_release';
    $cmd->title = 'New release';
    $cmd->link = "create_release.php?id=$folder->trunk_id";
    $cmd->icon = '{app_icons}buttons/new_release';
    $cmd->executable = $this->login->is_allowed (Privilege_set_folder, Privilege_modify, $folder);
    $cmd->importance = Command_importance_low + Command_importance_increment;
    $this->append ($cmd);

    $cmd = $this->make_command ();
    $cmd->id = 'new_branch';
    $cmd->title = 'New branch';

    $branch =  $folder->trunk ();
    if (isset ($branch))
    {
      $cmd->link = "create_branch.php?id=$folder->id&branch_id=$branch->id";
    }
    else
    {
      $cmd->link = "create_branch.php?id=$folder->id";
    }

    $cmd->icon = '{app_icons}buttons/new_branch';
    $cmd->executable = $this->login->is_allowed (Privilege_set_folder, Privilege_modify, $folder);
    $cmd->importance = Command_importance_low + Command_importance_increment;
    $this->append ($cmd);

    $cmd = $this->make_command ();
    $cmd->id = 'new_component';
    $cmd->title = 'New component';
    $cmd->link = "create_component.php?id=$folder->id";
    $cmd->icon = '{app_icons}buttons/new_component';
    $cmd->executable = $this->login->is_allowed (Privilege_set_folder, Privilege_modify, $folder);
    $cmd->importance = Command_importance_low + Command_importance_increment;
    $this->append ($cmd);
  }
}

?>