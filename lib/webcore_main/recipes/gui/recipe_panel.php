<?php

/**
 * @copyright Copyright (c) 2002-2008 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package recipes
 * @subpackage gui
 * @version 3.0.0
 * @since 1.3.0
 */

/****************************************************************************

Copyright (c) 2002-2008 Marco Von Ballmoos

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
require_once ('webcore/gui/panel.php');

/**
 * Performs setup for various {@link PANEL_MANAGER}s.
 * @package recipes
 * @subpackage gui
 * @version 1.8.1
 * @since 1.8.1
 */
class RECIPE_PANEL_MANAGER_HELPER extends PANEL_MANAGER_HELPER
{
  /**
   * Apply global options to a panel manager.
   * Does nothing by default.
   * @param PANEL_MANAGER &$manager
   */
  function configure (&$manager)
  {
    if ($manager->is_panel ('folder'))
    {
      $panel =& $manager->panel_at ('folder');
      $panel->rows = 10;
      $panel->columns = 2;
      $manager->move_panel_to ('folder', 0, Panel_selection);
    }
  }
}

?>