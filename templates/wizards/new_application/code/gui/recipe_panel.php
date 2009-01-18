<?php

/**
 * @copyright Copyright (c) 2002-2005 Marco Von Ballmoos
 * @author Marco Von Ballmoos <marco@earthli.com>
 * @filesource
 * @package recipes
 * @subpackage gui
 * @version 1.7.0
 * @since 1.3.0
 */

/****************************************************************************

Copyright (c) 2002-2005 Marco Von Ballmoos

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

http://www.earthli.com/software/webcore/recipes

****************************************************************************/

/** */
require_once ('webcore/gui/panel.php');

/** Manage a list of {@link PANEL}s for all {@link RECIPE_BOOK}s.
 * @package recipes
 * @subpackage gui
 * @version 1.7.0
 * @since 1.3.0 */
class RECIPE_INDEX_PANEL_MANAGER extends INDEX_PANEL_MANAGER
{
  /** Create the set of panels to use.
   * @access private */
  function _add_panels ()
  {
    parent::_add_panels ();

    $panel =& $this->panel_at ('folder');
    $panel->rows = 10;
    $panel->columns = 2;
  }  
}

?>
