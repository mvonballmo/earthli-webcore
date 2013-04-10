<?php

/**
 * @copyright Copyright (c) 2002-2013 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage command
 * @version 3.3.0
 * @since 2.7.0
 * @access private
 */

/****************************************************************************

Copyright (c) 2002-2013 Marco Von Ballmoos

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
 * Return the commands for a {@link SEARCH}.
 * @package webcore
 * @subpackage command
 * @version 3.3.0
 * @since 2.7.0
 * @access private
 */
class SEARCH_COMMANDS extends COMMANDS
{
  /**
   * @param SEARCH $search Configure commands for this object.
   */
  public function __construct ($search)
  {
    parent::__construct ($search->app);

    $cmd = $this->make_command ();
    $cmd->id = 'edit';
    $cmd->caption = 'Edit';
    $cmd->link = "edit_search.php?id=$search->id";
    $cmd->icon = '{icons}buttons/edit';
    $cmd->executable = true;
    $cmd->importance = Command_importance_high;
    $this->append ($cmd);

    $cmd = $this->make_command ();
    $cmd->id = 'delete';
    $cmd->caption = 'Delete';
    $cmd->link = "delete_search.php?id=$search->id";
    $cmd->icon = '{icons}buttons/delete';
    $cmd->executable = true;
    $cmd->importance = Command_importance_high - 2 * Command_importance_increment;
    $this->append ($cmd);
  }
}

?>