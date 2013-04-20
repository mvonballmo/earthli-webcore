<?php

/**
 * @copyright Copyright (c) 2002-2013 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package recipes
 * @subpackage gui
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
require_once ('webcore/gui/folder_grid.php');

/**
 * Display {@link RECIPE_BOOK}s from a query.
 * @package recipes
 * @subpackage gui
 * @version 3.4.0
 * @since 1.3.0
 */
class RECIPE_BOOK_GRID extends FOLDER_GRID
{
  /**
   * @var string
   */
  public $object_name = 'Recipe Book';

  /**
   * @var string
   */
  public $box_style = '';

  /**
   * @var boolean
   */
  public $show_separator = false;

  /**
   * @param RECIPE_BOOK $obj
   * @access private
   */
  protected function _draw_box ($obj)
  {
?>
<div class="grid-item">
  <div class="minimal-commands">
    <?php $this->_draw_menu_for ($obj, Menu_size_minimal); ?>
  </div>
  <div class="minimal-commands-content">
    <?php
    if ($obj->icon_url)
    {
      ?>
      <div style="float: left; padding-right: 5px">
        <?php echo $obj->icon_as_html (); ?>
      </div>
    <?php
    }
    ?>
    <h3>
      <?php
      // drill down to the folder view only if there are subfolders for that project

      $t = $obj->title_formatter ();
      if (sizeof ($obj->sub_folders ()))
      {
        $t->add_argument ('panel', 'projects');
      }
      echo $obj->title_as_link ($t);
      ?>
    </h3>
    <p class="detail">
      <?php
      $entry_query = $obj->entry_query ();
      $size = $entry_query->size ();
      echo "(<span class=\"field\">$size</span> Recipes)";
      ?>
    </p>
    <div class="text-flow">
      <?php echo $obj->summary_as_html (); ?>
    </div>
  </div>
</div>
<?php
  }
}
?>