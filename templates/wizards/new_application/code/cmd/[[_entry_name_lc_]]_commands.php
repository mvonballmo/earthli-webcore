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
require_once ('webcore/cmd/entry_commands.php');

/** Return the commands for a {@link RECIPE}.
 * @package recipes
 * @subpackage command
 * @version 1.7.0
 * @since 1.7.0
 * @access private */
class RECIPE_COMMANDS extends DRAFTABLE_ENTRY_COMMANDS
{
  /** @param RECIPE &$entry Configure commands for this object. */
  function RECIPE_COMMANDS (&$entry)
  {
    ENTRY_COMMANDS::ENTRY_COMMANDS ($entry);

    $cmd =& $this->command_at ('edit');
    $cmd->link = "edit_recipe.php?id=$entry->id";
    
    $cmd =& $this->command_at ('delete');
    $cmd->link = "delete_recipe.php?id=$entry->id";

    $cmd =& $this->command_at ('purge');
    $cmd->link = "purge_recipe.php?id=$entry->id";

    $cmd =& $this->command_at ('clone');
    $cmd->link = "clone_recipe.php?id=$entry->id";

    $cmd =& $this->command_at ('send');
    $cmd->link = "send_recipe.php?id=$entry->id";
  }
}

?>