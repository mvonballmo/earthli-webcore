<?php

/**
 * @copyright Copyright (c) 2002-2014 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package recipes
 * @subpackage db
 * @version 3.6.0
 * @since 1.3.0
 */

/****************************************************************************

Copyright (c) 2002-2014 Marco Von Ballmoos

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
require_once ('webcore/db/folder_entry_query.php');

/**
 * Retrieves {@link RECIPE}s related to a particular {@link RECIPE_BOOK}.
 * @package recipes
 * @subpackage db
 * @version 3.6.0
 * @since 1.3.0
 */
class FOLDER_RECIPE_QUERY extends FOLDER_ENTRY_QUERY
{
  /**
   * @param RECIPE_BOOK $folder Retrieve recipes from this recipe book.
   */
  public function __construct ($folder)
  {
    parent::__construct ($folder);
    $this->_order = 'entry.title';
  }
}
?>