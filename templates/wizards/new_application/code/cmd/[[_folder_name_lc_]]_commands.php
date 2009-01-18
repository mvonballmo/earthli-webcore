<?php

/**
 * @copyright Copyright (c) 2006 [[_author_name_]]
 * @author [[_author_name_]] <[[_author_email_]]>
 * @filesource
 * @package [[_app_name_]]
 * @subpackage pages
 * @version 1.0.0
 * @since 1.0.0
 */

/****************************************************************************

Copyright (c) 2006 [[_author_name_]]

This file is part of [[_app_title_]].

[[_app_title_]] is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

[[_app_title_]] is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with [[_app_title_]]; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

For more information about the [[_app_title_]], visit:

[[_app_url_]]

****************************************************************************/

/** */
require_once ('webcore/cmd/folder_commands.php');

/** Return the commands for a {@link RECIPE_BOOK}.
 * @package recipes
 * @subpackage command
 * @version 1.7.0
 * @since 1.7.0
 * @access private */
class RECIPE_BOOK_COMMANDS extends FOLDER_COMMANDS
{
  /** @param RECIPE_BOOK &$folder Configure commands for this object. */
  function RECIPE_BOOK_COMMANDS (&$folder)
  {
    FOLDER_COMMANDS::FOLDER_COMMANDS ($folder);

    $cmd =& $this->command_at ('new');
    $cmd->title = 'New recipe book';
  }

  /** Add buttons that create items in the folder.
   * @param FOLDER &$folder
   * @access private */
  function _add_creators (&$folder)
  {
    parent::_add_creators ($folder);

    $cmd = $this->make_command ();
    
    $cmd->id = 'new_recipe';
    $cmd->title = 'New recipe';
    $cmd->link = "create_recipe.php?id=$folder->id";
    $cmd->icon = '{app_icons}buttons/new_recipe';
    $cmd->executable = $this->login->is_allowed (Privilege_set_entry, Privilege_create, $folder);
    $cmd->importance = Command_importance_high;
    $this->append ($cmd);
  }
}

?>