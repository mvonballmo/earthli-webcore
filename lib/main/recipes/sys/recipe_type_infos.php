<?php

/**
 * @copyright Copyright (c) 2002-2014 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package recipes
 * @subpackage sys
 * @version 3.5.0
 * @since 1.7.0
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
require_once ('webcore/sys/webcore_type_infos.php');

/**
 * Describes the {@link RECIPE} class.
 * @package recipes
 * @subpackage sys
 * @version 3.5.0
 * @since 1.4.0
 * @access private
 */
class RECIPE_TYPE_INFO extends DRAFTABLE_ENTRY_TYPE_INFO
{
  /**
   * @var string
   */
  public $id = 'recipe';

  /**
   * @var string
   */
  public $singular_title = 'Recipe';

  /**
   * @var string
   */
  public $plural_title = 'Recipes';

  /**
   * @var string
   */
  public $icon = '{app_icons}buttons/new_recipe';

  /**
   * @var string
   */
  public $edit_page = 'edit_recipe.php';
}

/**
 * Describes the {@link FOLDER} class.
 * @package recipes
 * @subpackage sys
 * @version 3.5.0
 * @since 1.4.0
 * @access private
 */
class RECIPE_BOOK_TYPE_INFO extends FOLDER_TYPE_INFO
{
  /**
   * @var string
   */
  public $singular_title = 'Recipe book';

  /**
   * @var string
   */
  public $plural_title = 'Recipe books';

  /**
   * @var string
   */
  public $edit_page = 'edit_recipe_book.php';
}

?>