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
require_once ('webcore/gui/entry_grid.php');

/** Display {@link RECIPE}s from a {@link QUERY}.
 * @package recipes
 * @subpackage gui
 * @version 1.7.0
 * @since 1.3.0 */
class RECIPE_GRID extends CONTENT_OBJECT_GRID
{
  /** @var string */
  var $object_name = 'Recipe';
  /** @var string */
  var $box_style = 'object-in-list';
  /** @var integer */
  var $spacing = 4;
  /** @var boolean */
  var $show_separator = FALSE;
  /** @var boolean */
  var $show_user = TRUE;
  /** @var boolean */
  var $show_folder = FALSE;

  /** @param RECIPE &$obj
   * @access private */
  function _draw_box (&$obj)
  {
    $folder =& $obj->parent_folder ();
    $creator =& $obj->creator ();
  ?>
  <div class="grid-title">
  <?php
    if ($this->show_folder)
      echo $folder->title_as_link () . $this->app->display_options->object_separator;
    echo $this->obj_link ($obj);
  ?>
  </div>
  <div>
    <?php
      $this->_draw_menu_for ($obj, Menu_size_compact);
    ?>
    <span class="detail"><?php if ($this->show_user) { echo $creator->title_as_link (); ?> - <?php } ?>
    <?php echo $obj->time_created->format (); ?></span>
  </div>
  <div class="description">
    <?php
      if ($obj->originator)
      {
    ?>
    <div>
      From the kitchen of <span class="field"><?php echo $obj->originator; ?></span>
    </div>
    <?php
      }
  
      if ($obj->description && ! $this->items_are_selectable)
        echo $obj->description_as_html ();
    ?>
  </div>
  <?php
  }
}

/** Display {@link RECIPE}s from a {@link QUERY}.
 * @package recipes
 * @subpackage gui
 * @version 1.7.0
 * @since 1.3.0 */
class RECIPE_SUMMARY_GRID extends DRAFTABLE_ENTRY_SUMMARY_GRID
{
  /** @var string */
  var $object_name = 'Recipe';

  /** Return the block of text to summarize.
   * @param OBJECT_IN_FOLDER &$obj
   * @access private */
  function _text_to_summarize (&$obj)
  {
    return $obj->description . ' ' . $obj->ingredients . ' ' . $obj->instructions;
  }  
}

?>
