<?php

/**
 * @copyright Copyright (c) 2002-2014 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage grid
 * @version 3.6.0
 * @since 2.2.1
 */

/****************************************************************************
 *
 * Copyright (c) 2002-2014 Marco Von Ballmoos
 *
 * This file is part of earthli WebCore.
 *
 * earthli WebCore is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * earthli WebCore is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with earthli WebCore; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 * For more information about the earthli WebCore, visit:
 *
 * http://www.earthli.com/software/webcore
 ****************************************************************************/

/** */
require_once('webcore/obj/webcore_object.php');

/**
 * Renders grids of objects from a {@link QUERY}.
 * Takes objects from a query and displays them in the requested rows and columns. If the
 * query has more objects than will fit, it automatically handles showing pages of grids.
 * @package webcore
 * @subpackage grid
 * @version 3.6.0
 * @since 2.2.1
 * @abstract
 */
abstract class GRID extends WEBCORE_OBJECT
{
  /**
   * Displays page navigation for the grid, if necessary.
   * @var PAGE_NAVIGATOR
   */
  public $pager;

  /**
   * Style to use for each the grid itself.
   * Should be a defined CSS class.
   * @var string
   */
  public $css_class = '';

  /**
   * Style to use for each box containing an object.
   * Should be a defined CSS class.
   * @var string
   */
  public $box_css_class = '';

  /**
   * Use CSS-style page-breaks for printing?
   * @var boolean
   */
  public $show_page_breaks = false;

  /**
   * How many grid rows to show per page when printing?
   * @var integer
   */
  public $rows_per_printed_page = 2;

  /**
   * Override maximum title size to be this length.
   * If this is 0, then the size is not overridden.
   * @var integer
   */
  public $overridden_max_title_size = 75;

  /**
   * Should the pager be drawn?
   * Set this to False to prevent page numbers from displaying even if there are
   * more pages than can be displayed.
   * @var boolean
   */
  public $show_pager = true;

  /**
   * Should menus be drawn for objects?
   * @var boolean
   */
  public $show_menus = true;

  /**
   * @param CONTEXT $context Context to which this grid belongs.
   */
  public function __construct($context)
  {
    parent::__construct($context);
    include_once('webcore/gui/page_navigator.php');
    $this->pager = new PAGE_NAVIGATOR ($context);
  }

  public function set_page_size($size)
  {
    $this->_num_rows = $size;
    $this->_num_columns = 1;
  }

  /**
   * Attach the query to be displayed.
   * Call this before calling {@link display()}.
   * @param QUERY $query
   */
  public function set_query($query)
  {
    $this->_query = $query;
    $this->_num_objects = null;
  }

  public function get_pager()
  {
    if (!isset ($this->_num_objects))
    {
      $this->_num_objects = $this->_get_size();
      $this->pager->set_ranges($this->_num_objects, $this->_num_rows * $this->_num_columns);
    }

    return $this->pager;
  }

  /**
   * Show this page of objects from the query.
   * The query must be set with {@link set_query()} before calling this
   * function. If the number of objects specified in 'set_ranges' exceeds (rows
   * x columns), then the page number specifies which page of objects to show in
   * the grid. If there are no objects, then don't render a grid, but display a
   * 'no objects' message.
   */
  public function display()
  {
    if (!isset ($this->_num_objects))
    {
      $this->_num_objects = $this->_get_size();
      $this->pager->set_ranges($this->_num_objects, $this->_num_rows * $this->_num_columns);
    }

    if ($this->_num_objects > 0)
    {
      $objects = $this->_get_objects();
      if (isset ($this->env->profiler))
      {
        $this->env->profiler->start('ui');
      }
      $this->_draw($objects);
      if (isset ($this->env->profiler))
      {
        $this->env->profiler->stop('ui');
      }
    }
    else
    {
      $this->_draw_empty_grid();
    }
  }

  /**
   * The total number of object the grid represents.
   * This is not the same as the number of objects the grid can show. If the grid is
   * a 3 x 3 grid, but it has been given data for 120 items, this routine returns 120.
   * @return integer
   * @access private
   */
  protected function _get_size()
  {
    $this->assert(isset ($this->_query), 'query is not set', '_get_size', 'GRID');

    return $this->_query->size();
  }

  /**
   * Get the list of objects for the requested page.
   * @return stdClass[]
   * @access private
   */
  protected function _get_objects()
  {
    $this->assert(isset ($this->_query), 'query is not set', '_get_objects', 'GRID');
    $num_objects_per_page = $this->_num_rows * $this->_num_columns;
    $this->_query->set_limits(($this->pager->page_number - 1) * $num_objects_per_page, $num_objects_per_page);

    return $this->_query->objects();
  }

  /**
   * Render the grid itself.
   * Draws a pager if {@link $show_pager} is True, then calls {@link _start_grid()}
   * and {@link _draw_header()}. After calling {@link _draw_cells()} to draw the content, it
   * finishes with {@link _draw_footer()} and {@link _finish_grid()} before drawing the
   * pager again with {@link _draw_pager()}.
   * @param stdClass[] $objects
   * @access private
   */
  protected function _draw($objects)
  {
    if ($this->show_pager)
    {
      $this->_draw_pager(true);
    }

    $this->_start_grid();
    $this->_draw_header();

    if ($this->overridden_max_title_size)
    {
      $old_size = $this->context->display_options->overridden_max_title_size;
      $this->context->display_options->overridden_max_title_size = $this->overridden_max_title_size;
      $this->_draw_cells($objects);
      $this->context->display_options->overridden_max_title_size = $old_size;
    }
    else
    {
      $this->_draw_cells($objects);
    }

    $this->_draw_footer();
    $this->_finish_grid();

    if ($this->show_pager)
    {
      $this->_draw_pager(false);
    }

    ob_start();
    $this->_draw_scripts();
    $scripts = ob_get_contents();
    ob_end_clean();
    if (!empty ($scripts))
    {
      ?>
      <script>
        <?php echo $scripts; ?>
      </script>
    <?php
    }
  }

  /**
   * Draw JavaScripts used by this grid.
   * @access private
   */
  protected function _draw_scripts()
  {
  }

  /**
   * Called from {@link _draw()}.
   * Each cell is rendered using {@link _start_box()},
   * {@link _draw_box()} and {@link _finish_box()}. Calls {@link _draw_page_break()} if
   * {@link $show_page_breaks} is True, and calls {@link _start_row()} for every
   * {@link $_num_columns} cells rendered, calling {@link _finish_row()} if a row has
   * already been started.
   *
   * Most descendants will only override the {@link _draw_box()} method to draw the
   * object details.
   * @param stdClass[] $objects
   * @access private
   */
  protected function _draw_cells($objects)
  {
    $index = 0;
    $remainder = 0;

    foreach ($objects as &$obj)
    {
      $remainder = $index % $this->_num_columns;

      if ($remainder == 0)
      {
        if ($this->show_page_breaks && ($index % $this->rows_per_printed_page == 0))
        {
          $this->_draw_page_break();
        }

        $this->_start_row($obj);
      }

      $this->_start_box($obj);
      $this->_draw_box($obj);
      $this->_finish_box($obj);

      if ($remainder == $this->_num_columns - 1)
      {
        $this->_finish_row($obj);
      }

      $index += 1;
    }

    // if there were fewer than 'num_columns' cells in the last row

    if ($remainder < $this->_num_columns - 1)
    {
      $empty = null; // avoid a compile warning
      $this->_finish_row($empty);
    }
  }

  /**
   * Start rendering the grid.
   * Use the results of {@link _style_for_grid()} to render the style for the starting container.
   * Called from {@link _draw()}.
   * @access private
   */
  protected function _start_grid()
  {
  }

  /**
   * Render the header for the grid.
   * Called immediately after {@link _start_grid()}. (empty by default)
   * Called from {@link _draw()}.
   * @access private
   */
  protected function _draw_header()
  {
  }

  /**
   * Start rendering a row.
   * Called from {@link _draw_cells()}.
   * @param object $obj
   * @access private
   */
  protected function _start_row($obj)
  {
  }

  /**
   * Finish rendering a row.
   * Called from {@link _draw_cells()}.
   * @param object $obj
   * @access private
   */
  protected function _finish_row($obj)
  {
  }

  /**
   * Render the footer for the grid.
   * Called immediately before {@link _finish_grid()}. (empty by default)
   * Called from {@link _draw()}.
   * @access private
   */
  protected function _draw_footer()
  {
  }

  /**
   * Finish rendering the grid.
   * Called from {@link _draw()}.
   * @access private
   */
  protected function _finish_grid()
  {
  }

  /**
   * Render the start of a single cell.
   * Called from {@link _draw_cells()}.
   * @param object $obj
   * @access private
   */
  protected function _start_box($obj)
  {
  }

  /**
   * Close the open cell.
   * Called from {@link _draw_cells()}.
   * @param object $obj
   * @access private
   */
  protected function _finish_box($obj)
  {
  }

  /**
   * Render the actual cell contents.
   * Override in descendants to show details for the specific object being displayed in the grid.
   * @param object $obj
   * @access private
   * @abstract
   */
  protected abstract function _draw_box($obj);

  /**
   * Render the page navigator, if necessary.
   * Only called if {@link $show_paginotor} is True. Called from {@link _draw()}.
   * @param boolean $include_anchor_id If true, renders the id for the
   * pager.
   * @access private
   */
  protected function _draw_pager($include_anchor_id)
  {
    $output = $this->pager->as_html();
    if ($output)
    {
      if ($include_anchor_id && $this->pager->page_anchor)
      {
        echo '<div class="pager" id="' . $this->pager->page_anchor . '">' . $output . '</div>';
      }
      else
      {
        echo '<div class="pager">' . $output . '</div>';
      }
    }
  }

  /**
   * Called if there are no objects in the query.
   * @access private
   */
  protected function _draw_empty_grid()
  {
    $this->context->show_message('This list is empty.', 'info');
  }

  /**
   * Renders a page break if the grid is printable.
   * Called only if {@link $show_page_breaks} is true. Called whenever
   * {@link $rows_per_printed_page} have been rendered.
   * @access private
   */
  protected function _draw_page_break()
  {
    ?>
    <div class="page-break"></div>
  <?php
  }

  /**
   * Renders using the object's menu and commands.
   * Uses the {@link Handler_menu} and {@link Handler_commands} from the given
   * object. Draws nothing if {@link $show_menus} is <code>False</code>.
   * @param RENDERABLE $obj
   * @param string $size Sizing constant; see {@link Menu_size_standard}.
   */
  protected function _draw_menu_for($obj, $size = Menu_size_standard)
  {
    if ($this->show_menus)
    {
      /** @var MENU_RENDERER $renderer */
      $renderer = $obj->handler_for(Handler_menu);
      $renderer->set_size($size);
      /** @var COMMANDS $commands */
      $commands = $obj->handler_for(Handler_commands);
      $renderer->display($commands);
    }
  }

  protected function _display_start_overlay_container()
  {
    ?>
    <div class="overlay-container">
  <?php
  }

  protected function _display_finish_overlay_container()
  {
    ?>
    </div>
  <?php
  }

  protected function _display_start_overlay_commands($obj)
  {
    $this->_display_start_overlay_container();
    ?>
    <div class="top-left-overlay">
      <?php
      $this->_draw_menu_for($obj, Menu_size_minimal);
      ?>
    </div>
  <?php
  }

  protected function _display_finish_overlay_commands()
  {
    $this->_display_finish_overlay_container();
  }

  protected function _display_start_bottom_right_overlay()
  {
    ?>
    <div class="bottom-right-overlay">
  <?php
  }

  protected function _display_finish_bottom_right_overlay()
  {
    echo '</div>';
  }

  /**
   * @param RENDERABLE $obj
   */
  protected function _display_start_minimal_commands_block($obj)
  {
    if ($this->show_menus)
    {
      ?>
      <div class="minimal-commands">
        <?php $this->_draw_menu_for($obj, Menu_size_minimal); ?>
      </div>
      <div class="minimal-commands-content">
    <?php
    }
  }

  protected function _display_finish_minimal_commands_block()
  {
    if ($this->show_menus)
    {
      ?>
      </div>
    <?php
    }
  }

  /**
   * CSS styles to apply to the main grid container.
   * Takes {@link $centered}, {@link $width} and {@link $show_page_breaks} into account.
   * @return string
   * @access private
   */
  protected function _style_for_grid()
  {
    if ($this->show_page_breaks)
    {
      $styles [] = 'page-break-before: always';
    }

    if (isset ($styles) && sizeof($styles))
    {
      return join('; ', $styles);
    }

    return '';
  }

  /**
   * CSS classes to apply to the main grid container.
   * @return string
   * @access private
   */
  protected function _css_class_for_grid()
  {
    $Result = $this->css_class;

    $col_class = $this->_get_column_class();
    if ($col_class)
    {
      $Result .= ' ' . $col_class;
    }

    return $Result;
  }

  /**
   * CSS class and styles to apply to the main grid container.
   * Takes {@link $box_style} and {@link $even_columns} into account.
   * @return string
   * @access private
   */
  protected function _CSS_for_box()
  {
    $attributes = array();
    $style = $this->_style_for_box();
    if (!empty($style))
    {
      $attributes [] = "style=\"$style\"";
    }
    $css_class = $this->_class_for_box();
    if (!empty($css_class))
    {
      $attributes [] = "class=\"$css_class\"";
    }
    if (sizeof($attributes))
    {
      return join(' ', $attributes);
    }

    return '';
  }

  /**
   * CSS styles to apply to each cell (box).
   * Calculates the desired width if {@link $even_columns} is True.
   * @return string
   * @access private
   */
  protected function _style_for_box()
  {
    return "";
  }

  /**
   * @return string
   */
  protected function _class_for_box()
  {
    return $this->box_css_class;
  }

  protected function _get_column_class()
  {
    return '';

    switch ($this->_num_columns)
    {
      case 1:
        return 'one-column';
      case 2:
        return 'two-column';
      case 3:
        return 'three-column';
      case 4:
        return 'four-column';
      case 5:
        return 'five-column';
      default:
        return '';
    }
  }

  /**
   * @var integer
   * @access private
   */
  protected $_num_columns = 3;

  /**
   * @var integer
   * @access private
   */
  protected $_num_rows = 5;

  /**
   * @var integer
   * @access private
   */
  protected $_num_objects;

  /**
   * @var HIERARCHICAL_QUERY
   * @access private
   */
  protected $_query;
}

abstract class HTML_GRID extends GRID
{
  /**
   * Start rendering the grid.
   * @access private
   */
  protected function _start_grid()
  {
    $class = $this->_css_class_for_grid();
    $style = $this->_style_for_grid();
    ?>
    <<?php echo $this->_table_tag_name; ?> class="<?php echo $class; ?>" <?php if ($style)
  {
    echo " style=\"$style\"";
  } ?>>
  <?php
  }

  /**
   * Start rendering a row.
   * @param object $obj
   * @access private
   */
  protected function _start_row($obj)
  {
    $this->_internal_start_row();
  }

  /**
   * Render the start of a single cell.
   * @param object $obj
   * @access private
   */
  protected function _start_box($obj)
  {
    $this->_internal_start_cell($this->_CSS_for_box());
  }

  /**
   * Close the open cell.
   * @param object $obj
   * @access private
   */
  protected function _finish_box($obj)
  {
    $this->_internal_finish_cell();
  }

  /**
   * Finish rendering a row.
   * @param object $obj
   * @access private
   */
  protected function _finish_row($obj)
  {
    $this->_internal_finish_row();
  }

  /**
   * Renders a page break if the grid is printable.
   * Called only if {@link $show_page_breaks} is true. Called whenever
   * {@link $rows_per_printed_page} have been rendered.
   * @access private
   */
  protected function _draw_page_break()
  {
    $this->_internal_start_row('class="page-break"');
    $this->_internal_finish_row();
  }

  /**
   * Finish rendering the grid.
   * @access private
   */
  protected function _finish_grid()
  {
    echo '</' . $this->_table_tag_name . '>';
  }

  protected function _new_column($attributes = null)
  {
    $this->_internal_finish_cell();
    $this->_internal_start_cell($attributes);
  }

  protected function _internal_start_row($attributes = null)
  {
    if ($this->_row_tag_name)
    {
      if ($attributes)
      {
        echo '<' . $this->_row_tag_name . ' ' . $attributes . '>';
      }
      else
      {
        echo '<' . $this->_row_tag_name . '>';
      }
    }
  }

  protected function _internal_start_cell($attributes = null)
  {
    if ($attributes)
    {
      echo '<' . $this->_cell_tag_name . ' ' . $attributes . '>';
    }
    else
    {
      echo '<' . $this->_cell_tag_name . '>';
    }
  }

  protected function _internal_finish_cell()
  {
    echo '</' . $this->_cell_tag_name . '>';
  }

  protected function _internal_start_header_cell($attributes = null)
  {
    if ($attributes)
    {
      echo '<' . $this->_header_cell_tag_name . ' ' . $attributes . '>';
    }
    else
    {
      echo '<' . $this->_header_cell_tag_name . '>';
    }
  }

  protected function _internal_finish_header_cell()
  {
    echo '</' . $this->_header_cell_tag_name . '>';
  }

  protected function _internal_finish_row()
  {
    if ($this->_row_tag_name)
    {
      echo '</' . $this->_row_tag_name . '>';
    }
  }

  protected $_table_tag_name;
  protected $_row_tag_name;
  protected $_cell_tag_name;
  protected $_header_cell_tag_name;
}

/**
 * Renders a grid using an HTML table.
 * The width is always fixed to the number of columns specified; it can never be less.
 * Use the {@link CSS_FLOW_GRID} to allow the grid to resize smaller, if needed.
 * @package webcore
 * @subpackage grid
 * @version 3.6.0
 * @since 2.6.0
 * @abstract
 */
abstract class HTML_TABLE_GRID extends HTML_GRID
{
  var $even_columns = true;

  /**
   * @param CONTEXT $context Context to which this grid belongs.
   */
  public function __construct($context)
  {
    parent::__construct($context);

    $this->css_class = 'grid top basic columns';
    $this->_table_tag_name = 'table';
    $this->_row_tag_name = 'tr';
    $this->_cell_tag_name = 'td';
    $this->_header_cell_tag_name = 'th';
  }

  /**
   * CSS classes to apply to the main grid container.
   * @return string
   * @access private
   */
  protected function _css_class_for_grid()
  {
    if (!$this->even_columns)
    {
      return $this->css_class;
    }

    return parent::_css_class_for_grid();
  }
}

abstract class HTML_DIV_GRID extends HTML_GRID
{
  /**
   * @param CONTEXT $context Context to which this grid belongs.
   */
  public function __construct($context)
  {
    parent::__construct($context);

    $this->css_class = 'grid';
    $this->_table_tag_name = 'div';
    $this->_cell_tag_name = 'div';
    $this->_header_cell_tag_name = 'div';
  }
}

/**
 * Placeholder class for extending classes.
 * Descendants that inherit from this class will automatically be updated to a new
 * implementation if the ancestor here is changed. Prevents all descendant classes
 * from specifying that they explicitly use an HTML table for rendering.
 * @package webcore
 * @subpackage grid
 * @version 3.6.0
 * @since 2.6.0
 * @abstract
 */
abstract class STANDARD_GRID extends HTML_DIV_GRID
{
}

?>