<?php

/**
 * @copyright Copyright (c) 2002-2005 Marco Von Ballmoos
 * @author Marco Von Ballmoos <marco@earthli.com>
 * @filesource
 * @package recipes
 * @subpackage forms
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
require_once ('webcore/forms/object_in_folder_form.php');

/** Edit or create a {@link RECIPE}.
 * @package recipes
 * @subpackage forms
 * @version 1.7.0
 * @since 1.3.0 */
class RECIPE_FORM extends DRAFTABLE_ENTRY_FORM
{
  /** @param RECIPE_BOOK &$folder Add or create recipes in this recipe book. */
  function RECIPE_FORM (&$folder)
  {
    DRAFTABLE_ENTRY_FORM::DRAFTABLE_ENTRY_FORM ($folder);

    $field =& new TEXT_FIELD ();
    $field->id = 'originator';
    $field->title = 'Originator';
    $field->max_length = 100;
    $this->add_field ($field);

    $field =& new TEXT_FIELD ();
    $field->id = 'instructions';
    $field->title = 'Instructions';
    $field->required = TRUE;
    $field->max_length = 65535;
    $this->add_field ($field);

    $field =& new TEXT_FIELD ();
    $field->id = 'ingredients';
    $field->title = 'Ingredients';
    $field->required = TRUE;
    $field->max_length = 65535;
    $this->add_field ($field);

    $field =& new BOOLEAN_FIELD ();
    $field->id = 'bullet_ingredients';
    $field->title = '';
    $field->description = 'Show ingredients as a bulleted list';
    $this->add_field ($field);

    $field =& new BOOLEAN_FIELD ();
    $field->id = 'number_instructions';
    $field->title = '';
    $field->description = 'Show instructions as a numbered list';
    $this->add_field ($field);
  }

  /** Load initial properties from this recipe.
   * @param RECIPE &$obj */
  function load_from_object (&$obj)
  {
    parent::load_from_object ($obj);

    $this->set_value ('originator', $obj->originator);
    $this->set_value ('instructions', $obj->instructions);
    $this->set_value ('ingredients', $obj->ingredients);

    $this->set_value ('bullet_ingredients', $obj->bullet_ingredients);
    $this->set_value ('number_instructions', $obj->number_instructions);
  }

  function load_with_defaults ()
  {
    parent::load_with_defaults ();

    $this->set_value ('bullet_ingredients', 1);
    $this->set_value ('number_instructions', 0);
  }

  /** Store the form's values to this recipe.
    * @param RECIPE &$obj
    * @access private */
  function _store_to_object (&$obj)
  {
    $obj->originator = $this->value_as_text ('originator');
    $obj->ingredients = $this->value_as_text ('ingredients');
    $obj->instructions = $this->value_as_text ('instructions');
    $obj->bullet_ingredients = $this->value_for ('bullet_ingredients');
    $obj->number_instructions = $this->value_for ('number_instructions');

    parent::_store_to_object ($obj);
  }

  /** @param FORM_RENDERER &$renderer
   * @access private */
  function _draw_controls (&$renderer)
  {
    $renderer->start ();
    $renderer->draw_text_line_row ('title');
    $renderer->draw_text_line_row ('originator');
    $renderer->draw_check_box_row ('is_visible');
    $renderer->draw_separator ();
    $renderer->draw_text_box_row ('description');
    $renderer->draw_separator ();

    $renderer->draw_text_box_row ('ingredients');
    $renderer->draw_check_box_row ('bullet_ingredients');
    $renderer->draw_separator ();

    $renderer->draw_text_box_row ('instructions');
    $renderer->draw_check_box_row ('number_instructions');
    $renderer->draw_separator ();

    $renderer->draw_submit_button_row ();

    $this->_draw_history_item_controls ($renderer, FALSE);

    $renderer->finish ();
  }
}
?>
