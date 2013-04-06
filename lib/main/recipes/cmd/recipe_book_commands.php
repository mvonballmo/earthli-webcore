<?php

/**
 * @copyright Copyright (c) 2002-2010 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package recipes
 * @subpackage command
 * @version 3.3.0
 * @since 1.7.0
 * @access private
 */

/****************************************************************************

Copyright (c) 2002-2010 Marco Von Ballmoos

This file is part of earthli Recipes.

earthli Recipes is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

earthli Recipes is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with earthli Recipes; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

For more information about the earthli Recipes, visit:

http://earthli.com/software/webcore/app_recipes.php

****************************************************************************/

/** */
require_once ('webcore/cmd/folder_commands.php');

/**
 * Return the commands for a {@link RECIPE_BOOK}.
 * @package recipes
 * @subpackage command
 * @version 3.3.0
 * @since 1.7.0
 * @access private
 */
class RECIPE_BOOK_COMMANDS extends FOLDER_COMMANDS
{
  /**
   * @param RECIPE_BOOK $folder Configure commands for this object.
   */
  public function __construct ($folder)
  {
    parent::__construct ($folder);

    $cmd = $this->command_at ('new');
    $cmd->caption = 'New recipe book';

    $cmd = $this->command_at ('new_entry');
    $cmd->caption = 'New recipe';
    $cmd->icon = '{app_icons}buttons/new_recipe';
    if ($folder->is_organizational()) 
    {
      $cmd->link = "select_folder.php?page_name=create_recipe.php";
    }
    else
    {
      $cmd->link = "create_recipe.php?id=$folder->id";
    }
  }
}

?>