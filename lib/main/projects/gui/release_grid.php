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
require_once('webcore/gui/content_object_grid.php');

/**
 * Display a list of {@link RELEASE}s from a {@link QUERY}.
 * @package projects
 * @subpackage gui
 * @version 3.6.0
 * @since 1.4.1
 */
class RELEASE_GRID extends CONTENT_OBJECT_GRID
{
  /**
   * @var boolean Show creator/modifier with releases?
   */
  public $show_user = true;

  /**
   * @var boolean Show project for release?
   */
  public $show_folder = false;

  /**
   * @var boolean Show branch for release?
   */
  public $show_branch = false;

  /**
   * @param CONTEXT $context Context to which this grid belongs.
   */
  function __construct($context)
  {
    parent::__construct($context);
  }

  /**
   * @param RELEASE $obj
   * @access private
   */
  protected function _draw_box($obj)
  {
    $creator = $obj->creator();
    $this->_display_start_minimal_commands_block($obj);
    ?>
    <h3>
      <?php echo $this->obj_link($obj); ?>
    </h3>
    <p class="info-box-top">
      <?php
      echo 'Created ';
      if ($this->show_user)
      {
        echo 'by ' . $creator->title_as_link() . ' - ';
      }
      echo $obj->time_created->format();

      if (!$obj->time_created->equals($obj->time_modified))
      {
        $modifier = $obj->modifier();
        echo '<br>Updated ';
        if ($this->show_user)
        {
          echo 'by ' . $modifier->title_as_link() . ' - ';
        }
        echo $obj->time_modified->format();
      }
      ?>
    </p>
    <div class="text-flow">
      <p>
        <?php
        $menu = $this->context->make_menu();
        $menu->renderer->separator_class = $this->app->display_options->object_class;

        if ($this->show_folder)
        {
          $folder = $obj->parent_folder();
          $menu->append($folder->title_as_link());
        }
        $branch = $obj->branch();
        if ($this->show_branch && isset ($branch))
        {
          $menu->append($branch->title_as_link());
        }

        $menu->display();
        ?>
      </p>
      <?php
      $status = $obj->status();
      echo $status->as_html();
      ?>
      <?php
      echo $obj->description_as_html();
      ?>
    </div>
    <?php
    $this->_display_finish_minimal_commands_block();
  }
}

?>