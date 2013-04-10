<?php

/**
 * @copyright Copyright (c) 2002-2013 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package projects
 * @subpackage command
 * @version 3.4.0
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
 * Return the commands for a {@link COMPONENT}.
 * @package projects
 * @subpackage command
 * @version 3.4.0
 * @since 1.9.0
 * @access private
 */
class COMPONENT_COMMANDS extends COMMANDS
{
  /**
   * @param COMPONENT $comp Configure commands for this object.
   */
  public function __construct ($comp)
  {
    parent::__construct ($comp->app);

    $cmd = $this->make_command ();
    $cmd->id = 'edit';
    $cmd->caption = 'Edit';
    $cmd->link = "edit_component.php?id=$comp->id";
    $cmd->icon = '{icons}buttons/edit';
    $cmd->executable = $this->login->is_allowed (Privilege_set_folder, Privilege_modify, $comp);
    $cmd->importance = Command_importance_high;
    $this->append ($cmd);

    $cmd = $this->make_command ();    
    $cmd->id = 'delete';
    $cmd->caption = 'Delete';
    $cmd->link = "delete_component.php?id=$comp->id";
    $cmd->icon = '{icons}buttons/delete';
    $cmd->executable = ! $comp->deleted () && $this->login->is_allowed (Privilege_set_folder, Privilege_delete, $comp);
    $cmd->importance = Command_importance_high - 2 * Command_importance_increment;
    $this->append ($cmd);

    $cmd = $this->make_command ();    
    $cmd->id = 'purge';
    $cmd->caption = 'Purge';
    $cmd->link = "purge_component.php?id=$comp->id";
    $cmd->icon = '{icons}buttons/purge';
    $cmd->executable = $this->login->is_allowed (Privilege_set_folder, Privilege_purge, $comp);
    $cmd->importance = Command_importance_high - 2 * Command_importance_increment;
    $this->append ($cmd);

    $cmd = $this->make_command ();    
    $cmd->id = 'history';
    $cmd->caption = 'History';
    $cmd->link = "view_component_history.php?id=$comp->id";
    $cmd->icon = '{icons}buttons/history';
    $cmd->executable = $this->login->is_allowed (Privilege_set_folder, Privilege_view_history, $comp);
    $cmd->importance = Command_importance_low;
    $this->append ($cmd);
  }
}

?>