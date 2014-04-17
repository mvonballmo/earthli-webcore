<?php

/**
 * @copyright Copyright (c) 2002-2014 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package recipes
 * @subpackage gui
 * @version 3.5.0
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
require_once ('webcore/gui/entry_grid.php');

/**
 * Display {@link RECIPE}s from a {@link QUERY}.
 * @package recipes
 * @subpackage gui
 * @version 3.5.0
 * @since 1.3.0
 */
class RECIPE_GRID extends CONTENT_OBJECT_GRID
{
  /**
   * @var string
   */
  public $object_name = 'Recipe';

  /**
   * @var string
   */
  public $box_CSS_class = 'object-in-list';

  /**
   * @var boolean
   */
  public $show_separator = true;

  /**
   * @var boolean
   */
  public $show_user = true;

  /**
   * @var boolean
   */
  public $show_folder = false;

  /**
   * @param RECIPE $obj
   * @access private
   */
  protected function _draw_box ($obj)
  {
    $folder = $obj->parent_folder ();
    $creator = $obj->creator ();
  ?>
  <div class="grid-item">
    <div class="minimal-commands">
      <?php $this->_draw_menu_for ($obj, Menu_size_minimal); ?>
    </div>
    <div class="minimal-commands-content">
      <h3>
      <?php
        if ($this->show_folder)
        {
          echo $folder->title_as_link () . $this->app->display_options->object_separator;
        }
        echo $this->obj_link ($obj);
      ?>
      </h3>
      <div class="detail">
        <?php if ($this->show_user) { echo $creator->title_as_link (); ?> - <?php } ?>
        <?php echo $obj->time_created->format (); ?>
      </div>
      <div class="text-flow">
        <?php
          if ($obj->originator)
          {
        ?>
        <p>
          From the kitchen of <em><?php echo $obj->originator; ?></em>
        </p>
        <?php
          }

          if ($obj->description && ! $this->items_are_selectable)
          {
            echo $obj->description_as_html ();
          }
        ?>
      </div>
    </div>
  </div>
  <?php
  }
}

/**
 * Display {@link RECIPE}s from a {@link QUERY}.
 * @package recipes
 * @subpackage gui
 * @version 3.5.0
 * @since 1.3.0
 */
class RECIPE_SUMMARY_GRID extends DRAFTABLE_ENTRY_SUMMARY_GRID
{
  /**
   * @var string
   */
  public $object_name = 'Recipe';

  /**
   * Return the block of text to summarize.
   * @param OBJECT_IN_FOLDER $obj
   * @access private
   */
  protected function _text_to_summarize ($obj)
  {
    return $obj->description . ' ' . $obj->ingredients . ' ' . $obj->instructions;
  }  
}

?>