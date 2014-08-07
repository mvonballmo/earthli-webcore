<?php

  /**
   * @copyright Copyright (c) 2002-2014 Marco Von Ballmoos
   * @author Marco Von Ballmoos
   * @filesource
   * @package projects
   * @subpackage gui
   * @version 3.5.0
   * @since 1.9.0
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
  require_once('webcore/gui/entry_grid.php');

  /**
   * Display {@link PROJECT_ENTRY}s from a {@link QUERY}.
   * Used as a base class only.
   * @package projects
   * @subpackage gui
   * @version 3.5.0
   * @since 1.9.0
   */
  class PROJECT_ENTRY_GRID extends CONTENT_OBJECT_GRID
  {
    /**
     * @var boolean
     */
    public $show_user = true;

    /**
     * @var boolean
     */
    public $show_folder = false;

    /**
     * @var boolean
     */
    public $show_branch = true;

    /**
     * @var boolean
     */
    public $show_release = true;

    /**
     * @var boolean
     */
    public $show_component = true;

    /**
     * @param CONTEXT $context Context to which this grid belongs.
     */
    public function __construct($context)
    {
      parent::__construct($context);

      $this->even_columns = true;
    }

    /**
     * @param PROJECT_ENTRY $obj
     * @access private
     */
    protected function _draw_box($obj)
    {
      $branch_info = $obj->main_branch_info();
      $this->_display_start_minimal_commands_block($obj);
      $props = $obj->kind_properties();
      $this->context->start_icon_container($props->icon, Twenty_px);
      ?>
      <h3>
        <?php echo $this->obj_link($obj); ?>
      </h3>
      <?php
      $this->_draw_context_in_project_for($obj, $branch_info);
      ?>
      <div class="text-flow">
        <?php
          if ($this->show_release)
          {
            $this->_draw_release_details($obj, $branch_info);
          }
          $this->_draw_user_details($obj, $branch_info);
          echo $obj->description_as_html();
          if ($obj->extra_description)
          {
            echo "<p><span class=\"field\">" . strlen($obj->extra_description) . "</span> bytes of extra information.</p>";
          }
          $this->_draw_description($obj);
        ?>
      </div>
      <?php
      $this->context->finish_icon_container();
      $this->_display_finish_minimal_commands_block();
    }

    /**
     * Draw the "path" to the entry.
     * Takes the visibility properties into account to determine whether to show
     * the component/project/release/branch.
     * @param PROJECT_ENTRY $obj
     * @param PROJECT_ENTRY_BRANCH_INFO $branch_info
     */
    protected function _draw_context_in_project_for($obj, $branch_info)
    {
      if ($this->show_folder || $this->show_branch || $this->show_release || $this->show_component)
      {
        $menu = $this->context->make_menu();
        $menu->renderer->separator_class = $this->app->display_options->object_class;

        if ($this->show_folder)
        {
          $folder = $obj->parent_folder();
          $menu->append($folder->title_as_link());
        }

        if ($this->show_component && $obj->component_id)
        {
          $comp = $obj->component();
          $menu->append($comp->title_as_link());
        }

        if ($this->show_branch)
        {
          $branch = $obj->main_branch();
          $menu->append($branch->title_as_link());
        }

        if ($this->show_release)
        {
          $rel = $branch_info->release();
          if (isset ($rel))
          {
            $menu->append($rel->title_as_link());
          }
        }

        $menu->display();
      }
    }

    /**
     * Draw entry-specific information for the given release.
     * @param PROJECT_ENTRY $obj
     * @param PROJECT_ENTRY_BRANCH_INFO $branch_info
     */
    protected function _draw_release_details($obj, $branch_info)
    {
    }

    /**
     * Draw user-specific information for the given release.
     * @param PROJECT_ENTRY $obj
     * @param PROJECT_ENTRY_BRANCH_INFO $branch_info
     */
    protected function _draw_user_details($obj, $branch_info)
    {
    }

    /**
     * Draw extra description information for the entry.
     * @param PROJECT_ENTRY $obj
     */
    protected function _draw_description($obj)
    {
    }
  }

  /**
   * Display {@link PROJECT_ENTRY}s from a {@link QUERY}.
   * @package projects
   * @subpackage gui
   * @version 3.5.0
   * @since 1.4.1
   */
  class PROJECT_ENTRY_SUMMARY_GRID extends ENTRY_SUMMARY_GRID
  {
    /**
     * Show search details for an object.
     * @param PROJECT_ENTRY $obj
     * @access private
     */
    protected function _echo_details($obj)
    {
      ?>
      <tr>
        <th>Kind</th>
        <td>
          <?php
            $props = $obj->kind_properties();

            echo $this->context->get_icon_with_text($props->icon, Sixteen_px, $obj->kind_as_text());
          ?>
        </td>
      </tr>
      <?php
      $comp = $obj->component();
      if (isset ($comp))
      {
        ?>
        <tr>
          <th>Component</th>
          <td>
            <?php
              echo $this->app->get_icon_with_text($comp->icon_url, Sixteen_px, $comp->title_as_link());
            ?>
          </td>
        </tr>
      <?php
      }
      parent::_echo_details($obj);
    }

    /**
     * Return the block of text to summarize.
     * @param PROJECT_ENTRY $obj
     * @return string
     * @access private
     */
    protected function _text_to_summarize($obj)
    {
      return $obj->description . ' ' . $obj->extra_description;
    }
  }

?>