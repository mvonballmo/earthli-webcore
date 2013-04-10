<?php

/**
 * @copyright Copyright (c) 2002-2013 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package recipes
 * @subpackage forms
 * @version 3.4.0
 * @since 1.3.0
 */

/****************************************************************************

Copyright (c) 2002-2013 Marco Von Ballmoos

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
require_once ('webcore/forms/search_fields.php');

/**
 * Create a filter for {@link RECIPE}s.
 * @package recipes
 * @subpackage forms
 * @version 3.4.0
 * @since 1.5.0
 */
class SEARCH_RECIPE_FIELDS extends SEARCH_DRAFTABLE_FIELDS
{
  /**
   * @param APPLICATION $app Main application.
   */
  public function __construct ($app)
  {
    parent::__construct ($app);

    $this->_add_text ('originator', 'Originator');
    $this->_add_text ('ingredients', 'Ingredients');
    $this->_add_text ('instructions', 'Instructions');
  }
}

?>
