<?php

/**
 * @copyright Copyright (c) 2002-2010 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package projects
 * @subpackage gui
 * @version 3.3.0
 * @since 1.4.1
 */

/****************************************************************************

Copyright (c) 2002-2010 Marco Von Ballmoos

This file is part of earthli Projects.

earthli Projects is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

earthli Projects is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with earthli Projects; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

For more information about the earthli Projects, visit:

http://www.earthli.com/software/webcore/projects

****************************************************************************/

/** */
require_once ('webcore/gui/folder_grid.php');

/**
 * Display a list of {@link PROJECT}s from a {@link QUERY}.
 * @package projects
 * @subpackage gui
 * @version 3.3.0
 * @since 1.4.1
 */
class PROJECT_GRID extends FOLDER_GRID
{
  /**
   * @var boolean
   */
  public $show_separator = false;

  /**
   * @var string
   */
  public $object_name = 'Project';

  /**
   * @var string
   */
  public $box_style = '';

  /**
   * @var integer
   */
  public $spacing = 8;

  /**
   * @param PROJECT $obj
   * @access private
   */
  protected function _draw_box ($obj)
  {
?>
  <div class="grid-item">
  <div class="minimal-commands">
    <?php $this->_draw_menu_for ($obj, Menu_size_minimal, Menu_align_inline); ?>
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
    <h3 class="grid-title">
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
    <p>
    <?php
      $menu = $this->context->make_menu ();
      $menu->append ("Changes", "view_folder.php?id=$obj->id&panel=changes");
      $menu->append ("Jobs", "view_folder.php?id=$obj->id&panel=jobs");
      $menu->display ();
    ?>
    </p>
    <div class="text-flow">
      <?php echo $obj->summary_as_html (); ?>
    </div>
  </div>
  <?php
  }
}
?>