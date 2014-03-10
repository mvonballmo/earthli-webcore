<?php

/**
 * @copyright Copyright (c) 2002-2014 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage command
 * @version 3.5.0
 * @since 2.7.0
 * @access private
 */

/****************************************************************************

Copyright (c) 2002-2014 Marco Von Ballmoos

This file is part of earthli WebCore.

earthli WebCore is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

earthli WebCore is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with earthli WebCore; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

For more information about the earthli WebCore, visit:

http://www.earthli.com/software/webcore

****************************************************************************/

/** */
require_once ('webcore/cmd/commands.php');

/**
 * Return the commands for a {@link GROUP}.
 * @package webcore
 * @subpackage command
 * @version 3.5.0
 * @since 2.7.0
 * @access private
 */
class GROUP_COMMANDS extends COMMANDS
{
  /**
   * @param GROUP $group Configure commands for this object.
   */
  public function __construct ($group)
  {
    parent::__construct ($group->app);

    $cmd = $this->make_command ();
    $cmd->id = 'edit';
    $cmd->caption = 'Edit';
    $cmd->link = "edit_group.php?id=$group->id";
    $cmd->icon = '{icons}buttons/edit';
    $cmd->executable = $this->login->is_allowed (Privilege_set_group, Privilege_modify);
    $cmd->importance = Command_importance_high;
    $this->append ($cmd);

    $cmd = $this->make_command ();    
    $cmd->id = 'delete';
    $cmd->caption = 'Delete';
    $cmd->link = "delete_group.php?id=$group->id";
    $cmd->icon = '{icons}buttons/delete';
    $cmd->executable = $this->login->is_allowed (Privilege_set_group, Privilege_delete);
    $cmd->importance = Command_importance_high - 2 * Command_importance_increment;
    $this->append ($cmd);

    $cmd = $this->make_command ();    
    $cmd->id = 'history';
    $cmd->caption = 'History';
    $cmd->link = "view_group_history.php?id=$group->id";
    $cmd->icon = '{icons}buttons/history';
    $cmd->executable = $this->login->is_allowed (Privilege_set_group, Privilege_view_history);
    $cmd->importance = Command_importance_low;
    $this->append ($cmd);
  }
}

?>