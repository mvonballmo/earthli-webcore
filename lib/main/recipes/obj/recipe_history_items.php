<?php

/**
 * @copyright Copyright (c) 2002-2014 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package recipes
 * @subpackage obj
 * @version 3.6.0
 * @since 1.3.0
 * @access private
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
require_once ('webcore/obj/webcore_history_items.php');

/**
 * Manages the audit trail of a {@link RECIPE}.
 * @package recipes
 * @subpackage obj
 * @version 3.6.0
 * @since 1.3.0
 * @access private
 */
class RECIPE_HISTORY_ITEM extends ENTRY_HISTORY_ITEM
{
  /**
   * Record class-specific differences.
   * 'orig' is the same as {@link $_object}, but is passed here for convenience.
   * @param RECIPE $orig
   * @param RECIPE $new
   * @access private
   */
  protected function _record_differences ($orig, $new)
  {
    parent::_record_differences ($orig, $new);

    $this->_record_string_difference ('Picture', $orig->picture_url, $new->picture_url);
    $this->_record_string_difference ('Originator', $orig->originator, $new->originator);

    $this->_record_text_difference ('Ingredients', $orig->ingredients, $new->ingredients);
    $this->_record_text_difference ('Instructions', $orig->instructions, $new->instructions);

    if ($orig->bullet_ingredients != $new->bullet_ingredients)
    {
      if ($new->bullet_ingredients)
      {
        $this->record_difference ('Set automatic bullets for ingredients.');
      }
      else
      {
        $this->record_difference ('Turned off automatic bullets for ingredients.');
      }
    }

    if ($orig->number_instructions != $new->number_instructions)
    {
      if ($new->number_instructions)
      {
        $this->record_difference ('Set automatic numbering for instructions.');
      }
      else
      {
        $this->record_difference ('Turned off automatic numbering for instructions.');
      }
    }
  }
}

?>