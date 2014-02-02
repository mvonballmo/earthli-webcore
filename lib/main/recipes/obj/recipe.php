<?php

/**
 * @copyright Copyright (c) 2002-2014 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package recipes
 * @subpackage obj
 * @version 3.4.0
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
require_once ('webcore/obj/entry.php');

/**
 * Contains all the information about a recipe.
 * @package recipes
 * @subpackage obj
 * @version 3.4.0
 * @since 1.3.0
 */
class RECIPE extends DRAFTABLE_ENTRY
{
  /**
   * The picture/logo/icon associated with this project.
   * @var string
   */
  public $picture_url;

  /**
   * Name of the inventor.
   * @var string
   */
  public $originator;

  /**
   * HTML text describing the ingredients.
   * @var string
   */
  public $ingredients;

  /**
   * HTML text describing the instructions.
   * @var string
   */
  public $instructions;

  /**
   * Show ingredients as a bullet list?
   * @var boolean
   */
  public $bullet_ingredients;

  /**
   * Show instructions as a numbered list?
   * @var boolean
   */
  public $number_instructions;

  /**
   * Render the {@link $originator} as HTML.
   * @return string
   */
  public function originator_as_html ()
  {
    $formatter = $this->html_formatter ();
    $force_pars = $formatter->force_paragraphs;
    $formatter->force_paragraphs = false;
    $Result = $this->_text_as_html ($this->originator, $formatter);
    $formatter->force_paragraphs = $force_pars;
    return $Result;
  }

  /**
   * Render the {@link $originator} as HTML.
   * @return string
   */
  public function originator_as_plain_text ()
  {
    $formatter = $this->plain_text_formatter ();
    $force_pars = $formatter->force_paragraphs;
    $formatter->force_paragraphs = false;
    $Result = $this->_text_as_plain_text ($this->originator, $formatter);
    $formatter->force_paragraphs = $force_pars;
    return $Result;
  }

  /**
   * Render the {@link $ingredients} as HTML.
   * @return string
   */
  public function ingredients_as_html ()
  {
    if ($this->bullet_ingredients)
    {
      return $this->_text_as_html ("<ul>$this->ingredients</ul>");
    }

    return $this->_text_as_html ($this->ingredients);
  }

  /**
   * Render the {@link $instructions} as HTML.
   * @return string
   */
  public function instructions_as_html ()
  {
    if ($this->number_instructions)
    {
      return $this->_text_as_html ("<ol>$this->instructions</ol>");
    }

    return $this->_text_as_html ($this->instructions);
  }

  /**
   * Render the {@link $ingredients} as plain text.
   * @return string
   */
  public function ingredients_as_plain_text ()
  {
    if ($this->bullet_ingredients)
    {
      return $this->_text_as_plain_text ("<ul>$this->ingredients</ul>");
    }

    return $this->_text_as_plain_text ($this->ingredients);
  }

  /**
   * Render the {@link $instructions} as plain text.
   * @return string
   */
  public function instructions_as_plain_text ()
  {
    if ($this->number_instructions)
    {
      return $this->_text_as_plain_text ("<ol>$this->instructions</ol>");
    }

    return $this->_text_as_plain_text ($this->instructions);
  }
  
  /**
   * @param DATABASE $db
   */
  public function load ($db)
  {
    parent::load ($db);
    $this->title = $db->f ("title");
    $this->description = $db->f ("description");
    $this->picture_url = $db->f ("picture_url");
    $this->originator = $db->f ("originator");
    $this->ingredients = $db->f ("ingredients");
    $this->instructions = $db->f ("instructions");
    $this->bullet_ingredients = $db->f ("bullet_ingredients");
    $this->number_instructions = $db->f ("number_instructions");
  }

  /**
   * @param SQL_STORAGE $storage Store values to this object.
   */
  public function store_to ($storage)
  {
    parent::store_to ($storage);
    $tname = $this->table_name ();
    $storage->add ($tname, 'picture_url', Field_type_string, $this->picture_url);
    $storage->add ($tname, 'originator', Field_type_string, $this->originator);
    $storage->add ($tname, 'ingredients', Field_type_string, $this->ingredients);
    $storage->add ($tname, 'instructions', Field_type_string, $this->instructions);
    $storage->add ($tname, 'bullet_ingredients', Field_type_integer, $this->bullet_ingredients);
    $storage->add ($tname, 'number_instructions', Field_type_integer, $this->number_instructions);
  }

  /**
   * Return default handler objects for supported tasks.
   * @param string $handler_type Specific functionality required.
   * @param object $options
   * @return object
   * @access private
   */
  protected function _default_handler_for ($handler_type, $options = null)
  {
    switch ($handler_type)
    {
      case Handler_print_renderer:
      case Handler_html_renderer:
      case Handler_text_renderer:
        include_once ('recipes/gui/recipe_renderer.php');
        return new RECIPE_RENDERER ($this->app, $options);
      case Handler_commands:
        include_once ('recipes/cmd/recipe_commands.php');
        return new RECIPE_COMMANDS ($this);
      case Handler_history_item:
        include_once ('recipes/obj/recipe_history_items.php');
        return new RECIPE_HISTORY_ITEM ($this->app);
      default:
        return parent::_default_handler_for ($handler_type, $options);
    }
  }
}

?>