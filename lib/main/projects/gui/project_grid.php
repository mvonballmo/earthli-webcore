<?php

/**
 * @copyright Copyright (c) 2002-2014 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package projects
 * @subpackage gui
 * @version 3.6.0
 * @since 1.4.1
 */

/****************************************************************************
 *
 * Copyright (c) 2002-2014 Marco Von Ballmoos
 *
 * This file is part of earthli Projects.
 *
 * earthli Projects is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * earthli Projects is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with earthli Projects; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 * For more information about the earthli Projects, visit:
 *
 * http://www.earthli.com/software/webcore/projects
 ****************************************************************************/

/** */
require_once('webcore/gui/folder_grid.php');

/**
 * Display a list of {@link PROJECT}s from a {@link QUERY}.
 * @package projects
 * @subpackage gui
 * @version 3.6.0
 * @since 1.4.1
 */
class PROJECT_GRID extends FOLDER_GRID
{
  /**
   * @param PROJECT $obj
   * @access private
   */
  protected function _draw_box($obj)
  {
    $this->_display_start_minimal_commands_block($obj);
    if ($obj->icon_url)
    {
      $this->context->start_icon_container($obj->icon_url, Thirty_two_px);
    }
    ?>
    <h3>
      <?php
      // drill down to the folder view only if there are subfolders for that project

      $t = $obj->title_formatter();
      if (sizeof($obj->sub_folders()))
      {
        $t->add_argument('panel', 'projects');
      }
      echo $obj->title_as_link($t);
      ?>
    </h3>
    <?php
    if ($obj->icon_url)
    {
      $this->context->finish_icon_container();
    }
    ?>
    <p>
      <?php
      $menu = $this->context->make_menu();
      $menu->append("Changes", "view_folder.php?id=$obj->id&panel=changes");
      $menu->append("Jobs", "view_folder.php?id=$obj->id&panel=jobs");
      $menu->display();
      ?>
    </p>
    <div class="text-flow">
      <?php echo $obj->summary_as_html(); ?>
    </div>
    <?php
    $this->_display_finish_minimal_commands_block();
  }
}

?>