<?php

/**
 * @copyright Copyright (c) 2002-2013 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage grid
 * @version 3.4.0
 * @since 2.2.1
 */

/****************************************************************************

Copyright (c) 2002-2013 Marco Von Ballmoos

This file is part of earthli WebCore.

earthli WebCore is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

earthli WebCore is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with earthli WebCore; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

For more information about the earthli WebCore, visit:

http://www.earthli.com/software/webcore

****************************************************************************/

/** */
require_once ('webcore/obj/webcore_object.php');

/**
 * Renders grids of objects from a {@link QUERY}.
 * Takes objects from a query and displays them in the requested rows and columns. If the
 * query has more objects than will fit, it automatically handles showing pages of grids.
 * @package webcore
 * @subpackage grid
 * @version 3.4.0
 * @since 2.2.1
 * @abstract
 */
abstract class GRID extends WEBCORE_OBJECT
{
  /**
   * Space to leave betweena grid cell border and its content.
   * @var integer
   */
  public $padding = 2;

  /**
   * Space to leave between grid cells.
   * @var integer
   */
  public $spacing = 0;

  /**
   * A CSS-style specifying the width of the grid.
   * @var string
   */
  public $width = '100%';

  /**
   * Center the grid in its parent?
   * @var boolean
   */
  public $centered = false;

  /**
   * Displays page navigation for the grid, if necessary.
   * @var PAGE_NAVIGATOR
   */
  public $paginator;

  /**
   * Force all columns to even width, regardless of content.
   * @var boolean
   */
  public $even_columns = true;

  /**
   * Style to use for each box containing an object.
   * Should be a defined CSS class.
   * @var string
   */
  public $box_style = '';

  /**
   * Put a separator between rows in the grid?
   * @var boolean
   */
  public $show_separator = true;

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
   * Border width to show for the entire grid.
   * Used primarily for debugging.
   * @var integer
   */
  public $border_size = 0;

  /**
   * Name of the type of objects shown.
   * @var string
   */
  public $object_name = 'object';

  /**
   * Override maximum title size to be this length.
   * If this is 0, then the size is not overridden.
   * @var integer
   */
  public $overridden_max_title_size = 75;

  /**
   * Should the paginator be drawn?
   * Set this to False to prevent page numbers from displaying even if there are
   * more pages than can be displayed.
   * @var boolean
   */
  public $show_paginator = true;

  /**
   * Should menus be drawn for objects?
   * @var boolean
   */
  public $show_menus = true;

  /**
   * @param APPLICATION $app Main application.
   */
  public function __construct ($app)
  {
    parent::__construct ($app);
    include_once ('webcore/gui/page_navigator.php');
    $this->paginator = new PAGE_NAVIGATOR ($app);
  }

  /**
   * Specify the number of desired columns and rows.
   * The grid will use a paginator to allow access to the objects not representable in a 'num_rows' x 'num_cols' grid.
   * @see GRID::display()
   * @param integer $num_rows Number of rows to display.
   * @param integer $num_cols Number of columns to display (some grids are not designed to use more than one column).
   */
  public function set_ranges ($num_rows, $num_cols)
  {
    $this->_num_rows = $num_rows;
    $this->_num_columns = $num_cols;
  }

  /**
   * Attach the query to be displayed.
   * Call this before calling {@link display()}.
   * @param QUERY $query
   */
  public function set_query ($query)
  {
    $this->_query = $query;
    $this->_num_objects = null;
  }

  /**
   * Show this page of objects from the query.
   * The query must be set with {@link set_query()} before calling this
   * function. If the number of objects specified in 'set_ranges' exceeds (rows
   * x columns), then the page number specifies which page of objects to show in
   * the grid. If there are no objects, then don't render a grid, but display a
   * 'no objects' message.
   */
  public function display ()
  {
    if (! isset ($this->_num_objects))
    {
      $this->_num_objects = $this->_get_size ();
      $this->paginator->set_ranges ($this->_num_objects, $this->_num_rows * $this->_num_columns);
    }

    if ($this->_num_objects > 0)
    {
      if (isset ($this->env->profiler)) $this->env->profiler->start ('ui');
      $this->_draw ($this->_get_objects ());
      if (isset ($this->env->profiler)) $this->env->profiler->stop ('ui');
    }
    else
    {
      $this->_draw_empty_grid ();
    }
  }

  /**
   * The total number of object the grid represents.
   * This is not the same as the number of objects the grid can show. If the grid is
   * a 3 x 3 grid, but it has been given data for 120 items, this routine returns 120.
   * @return integer
   * @access private
   */
  protected function _get_size ()
  {
    $this->assert (isset ($this->_query), 'query is not set', '_get_size', 'GRID');
    return $this->_query->size ();
  }

  /**
   * Get the list of objects for the requested page.
   * @return array[object]
   * @access private
   */
  protected function _get_objects ()
  {
    $this->assert (isset ($this->_query), 'query is not set', '_get_objects', 'GRID');
    $num_objects_per_page = $this->_num_rows * $this->_num_columns;
    $this->_query->set_limits (($this->paginator->page_number - 1) * $num_objects_per_page, $num_objects_per_page);
    return $this->_query->objects ();
  }

  /**
   * Render the grid itself.
   * Draws a paginator if {@link $show_paginator} is True, then calls {@link _start_grid()}
   * and {@link _draw_header()}. After calling {@link _draw_cells()} to draw the content, it
   * finishes with {@link _draw_footer()} and {@link _finish_grid()} before drawing the
   * paginator again with {@link _draw_paginator()}.
   * @param array[object] $objs
   * @access private
   */
  protected function _draw ($objs)
  {
    if ($this->show_paginator)
    {
      $this->_draw_paginator (true);
    }

    $this->_start_grid ();
    $this->_draw_header ();

    if ($this->overridden_max_title_size)
    {
      $old_size = $this->context->display_options->overridden_max_title_size;
      $this->context->display_options->overridden_max_title_size = $this->overridden_max_title_size;
      $this->_draw_cells ($objs);
      $this->context->display_options->overridden_max_title_size = $old_size;
    }
    else
    {
      $this->_draw_cells ($objs);
    }

    $this->_draw_footer ();
    $this->_finish_grid ();

    if ($this->show_paginator)
    {
      $this->_draw_paginator (false);
    }

    ob_start ();
      $this->_draw_scripts ();
      $scripts = ob_get_contents ();
    ob_end_clean ();
    if (! empty ($scripts))
    {
?>
<script type="text/javascript">
<?php echo $scripts; ?>
</script>
<?php
    }
  }

  /**
   * Draw JavaScripts used by this grid.
   * @access private
   */
  protected function _draw_scripts ()
  {
  }

  /**
   * Called from {@link _draw()}.
   * Each cell is rendered using {@link _start_box()},
   * {@link _draw_box()} and {@link _finish_box()}. Calls {@link _draw_page_break()} if
   * {@link $show_page_breaks} is True, and calls {@link _start_row()} for every
   * {@link $_num_columns} cells rendered, calling {@link _finish_row()} if a row has
   * already been started and {@link _draw_separator()} if {@link $show_separator} is
   * True.
   *
   * Most descendents will only override the {@link _draw_box()} method to draw the
   * object details.
   * @param array[object] $objs
   * @access private
   */
  protected function _draw_cells ($objs)
  {
    $index = 0;
    $remainder = 0;
    $count = sizeof ($objs);
    
    foreach ($objs as &$obj)
    {
      $remainder = $index % $this->_num_columns;

      if ($remainder == 0)
      {
        if ($this->show_page_breaks && ($index % $this->rows_per_printed_page == 0))
        {
          $this->_draw_page_break ();
        }

        $this->_start_row ($obj);
      }

      $this->_start_box ($obj);
      $this->_draw_box ($obj);
      $this->_finish_box ($obj);

      if ($remainder == $this->_num_columns - 1)
      {
        $this->_finish_row ($obj);
        if (($index < $count - 1) && $this->show_separator)
        {
          $this->_draw_separator ();
        }
      }
      
      $index += 1;
    }
    
    // if there were fewer than 'num_columns' cells in the last row

    if ($remainder < $this->_num_columns - 1)
    {
      $empty = null;  // avoid a compile warning
      $this->_finish_row ($empty);
    }
  }

  /**
   * Start rendering the grid.
   * Use the results of {@link _style_for_grid()} to render the style for the starting container.
   * Called from {@link _draw()}.
   * @access private
   */
  protected function _start_grid () {}

  /**
   * Render the header for the grid.
   * Called immediately after {@link _start_grid()}. (empty by default)
   * Called from {@link _draw()}.
   * @access private
   */
  protected function _draw_header () {}

  /**
   * Start rendering a row.
   * Called from {@link _draw_cells()}.
   * @param object $obj
   * @access private
   */
  protected function _start_row ($obj) {}

  /**
   * Finish rendering a row.
   * Called from {@link _draw_cells()}.
   * @param object $obj
   * @access private
   */
  protected function _finish_row ($obj) {}

  /**
   * Render the footer for the grid.
   * Called immediately before {@link _finish_grid()}. (empty by default)
   * Called from {@link _draw()}.
   * @access private
   */
  protected function _draw_footer () {}

  /**
   * Finish rendering the grid.
   * Called from {@link _draw()}.
   * @access private
   */
  protected function _finish_grid () {}

  /**
   * Render the start of a single cell.
   * Called from {@link _draw_cells()}.
   * @param object $obj
   * @access private
   */
  protected function _start_box ($obj) {}

  /**
   * Close the open cell.
   * Called from {@link _draw_cells()}.
   * @param object $obj
   * @access private
   */
  protected function _finish_box ($obj) {}

  /**
   * Render the actual cell contents.
   * Override in descendants to show details for the specific object being displayed in the grid.
   * @param object $obj
   * @access private
   * @abstract
   */
  protected abstract function _draw_box ($obj);

  /**
   * Render the page navigator, if necessary.
   * Only called if {@link $show_paginotor} is True. Called from {@link _draw()}.
   * @param boolean $include_anchor_id If true, renders the id for the
   * paginator.
   * @access private
   */
  protected function _draw_paginator ($include_anchor_id)
  {
    $output = $this->paginator->as_html ();
    if ($output)
    {
      if ($this->centered)
      {
        if ($include_anchor_id && $this->paginator->page_anchor)
        {
          echo '<p class="paginator" style="text-align: center" id="' . $this->paginator->page_anchor . '">' . $output . '</p>';
        }
        else
        {
          echo '<p class="paginator" style="text-align: center">' . $output . '</p>';
        }
      }
      else
      {
        if ($include_anchor_id && $this->paginator->page_anchor)
        {
          echo '<p class="paginator" id="' . $this->paginator->page_anchor . '">' . $output . '</p>';
        }
        else
        {
          echo '<p class="paginator">' . $output . '</p>';
        }
      }
    }
  }

  /**
   * Render a separator between rows.
   * Only used if {@link $show_separator} is True.
   * @access private
   */
  protected function _draw_separator () {}

  /**
   * Called if there are no objects in the query.
   * @access private
   */
  protected function _draw_empty_grid ()
  {
?>
  <p class="error">There are no <?php echo $this->object_name; ?>s to display.</p>
<?php
  }

  /**
   * Renders a page break if the grid is printable.
   * Called only if {@link $show_page_breaks} is true. Called whenever
   * {@link $rows_per_printed_page} have been rendered.
   * @access private
   */
  protected function _draw_page_break ()
  {
?>
  <div style="page-break-before: always"></div>
<?php
  }

  /**
   * Renders using the object's menu and commands.
   * Uses the {@link Handler_menu} and {@link Handler_commands} from the given
   * object. Draws nothing if {@link $show_menus} is <code>False</code>.
   * @param RENDERABLE $obj
   * @param string $size Sizing constant; see {@link Menu_size_standard}.
   */
  protected function _draw_menu_for ($obj, $size = Menu_size_standard)
  {
    if ($this->show_menus)
    {
      $renderer = $obj->handler_for (Handler_menu);
      $renderer->set_size ($size);
      $renderer->display ($obj->handler_for (Handler_commands));
    }
  }

  /**
   * CSS styles to apply to the main grid container.
   * Takes {@link $centered}, {@link $width} and {@link $show_page_breaks} into account.
   * @return string
   * @access private
   */
  protected function _style_for_grid ()
  {
    if ($this->width)
    {
      $styles [] = "width: $this->width";
    }
    if ($this->centered)
    {
      $styles [] = 'margin: auto';
    }
    if ($this->show_page_breaks)
    {
      $styles [] = 'page-break-before: always';
    }

    if (isset ($styles) && sizeof ($styles))
    {
      return join ('; ', $styles);
    }

    return '';
  }

  /**
   * CSS class and styles to apply to the main grid container.
   * Takes {@link $box_style} and {@link $even_columns} into account.
   * @return string
   * @access private
   */
  protected function _CSS_for_box ()
  {
    $style = $this->_style_for_box ();
    $attrs = array();
    if (!empty($style))
    {
      $attrs [] = "style=\"$style\"";
    }
    if (!empty($this->box_style))
    {
      $attrs [] = "class=\"$this->box_style\"";
    }
    if (sizeof ($attrs))
    {
      return join (' ', $attrs);
    }

    return '';
  }

  /**
   * CSS styles to apply to each cell (box).
   * Calculates the desired width if {@link $even_columns} is True.
   * @return string
   * @access private
   */
  protected function _style_for_box ()
  {
    if ($this->even_columns && ($this->_num_columns > 1))
    {
      $width = round (100 / $this->_num_columns, 0);
      return "vertical-align: top; width: $width%";
    }

    return "";
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
   * @var QUERY
   * @access private
   */
  protected $_query;
}

/**
 * Renders a grid using an HTML table.
 * The width is always fixed to the number of columns specified; it can never be less.
 * Use the {@link CSS_FLOW_GRID} to allow the grid to resize smaller, if needed.
 * @package webcore
 * @subpackage grid
 * @version 3.4.0
 * @since 2.6.0
 * @abstract
 */
abstract class HTML_TABLE_GRID extends GRID
{
  /**
   * Start rendering the grid.
   * @access private
   */
  protected function _start_grid ()
  {
    $style = $this->_style_for_grid ();
?>
<table class="basic" <?php if ($style) echo " style=\"$style\""; if ($this->border_size) echo " border=\"$this->border_size\""; ?>>
<?php
  }

  /**
   * Start rendering a row.
   * @param object $obj
   * @access private
   */
  protected function _start_row ($obj)
  {
?>
  <tr>
<?php
  }

  /**
   * Render the start of a single cell.
   * @param object $obj
   * @access private
   */
  protected function _start_box ($obj)
  {
    $attrs = $this->_CSS_for_box ();
?>
<td<?php if ($attrs) echo " $attrs"; ?>>
<?php
  }

  /**
   * Close the open cell.
   * @param object $obj
   * @access private
   */
  protected function _finish_box ($obj)
  {
?>
</td>
<?php
  }

  /**
   * Finish rendering a row.
   * @param object $obj
   * @access private
   */
  protected function _finish_row ($obj)
  {
?>
  </tr>
<?php
  }

  /**
   * Render a separator between rows.
   * @access private
   */
  protected function _draw_separator ()
  {
?>
  <tr><td>&nbsp;</td></tr>
<?php
  }

  /**
   * Renders a page break if the grid is printable.
   * Called only if {@link $show_page_breaks} is true. Called whenever
   * {@link $rows_per_printed_page} have been rendered.
   * @access private
   */
  protected function _draw_page_break ()
  {
?>
<tr style="page-break-before: always"><td colspan="<?php echo $this->_num_columns; ?>"></td></tr>
<?php
  }

  /**
   * Finish rendering the grid.
   * @access private
   */
  protected function _finish_grid ()
  {
?>
</table>
<?php
  }
}

/**
 * Renders a grid using CSS floats.
 * Each row will have at most {@link $_num_columns}; if there is not enough space,
 * a row may have fewer cells as content is automatically reflowed. See {@link HTML_TABLE_GRID}
 * for a container that enforces number of columns.
 * @package webcore
 * @subpackage grid
 * @version 3.4.0
 * @since 2.6.0
 * @abstract
 */
abstract class CSS_FLOW_GRID extends GRID
{
  /**
   * Make each column the same width.
   * Since cells are allowed to flow, forcing them to a certain percentage width of the parent
   * container results in overlap rather than reflow. Defaults to off.
   * If you re-enable this property, make sure to set a {@link $min_box_width} so that boxes
   * still flow more or less normally.
   * @var boolean
   */
  public $even_columns = false;

  /**
   * Minimum width of a single box.
   * Use CSS units to specify the width.
   * @var string
   */
  public $min_box_width = '';

  /**
   * Start rendering the grid.
   * @access private
   */
  protected function _start_grid ()
  {
    $style = $this->_style_for_grid ();
    $style .= '; display: table';
?>
<div style="<?php echo $style; ?>">
<?php
  }

  /**
   * Start rendering a row.
   * @param object $obj
   * @access private
   */
  protected function _start_row ($obj)
  {
?>
<div style="display: table">
<?php
  }

  /**
   * Render the start of a single cell.
   * @param object $obj
   * @access private
   */
  protected function _start_box ($obj)
  {
    $attrs = $this->_CSS_for_box ();
?>
<div<?php if ($attrs) echo " $attrs"; ?>>
<?php
  }

  /**
   * Close the open cell.
   * @param object $obj
   * @access private
   */
  protected function _finish_box ($obj)
  {
?>
</div>
<?php
  }

  /**
   * Finish rendering a row.
   * @param object $obj
   * @access private
   */
  protected function _finish_row ($obj)
  {
?>
</div>
<?php
  }

  /**
   * Finish rendering the grid.
   * @access private
   */
  protected function _finish_grid ()
  {
?>
</div>
<?php
  }

  /**
   * CSS styles to apply to each cell (box).
   * Applies the "float: left" style to each cell in the grid.
   * @return string
   * @access private
   */
  protected function _style_for_box ()
  {
    $style = parent::_style_for_box ();
    if ($style)
    {
      $Result = $style . '; float: left';
    }
    else
    {
      $Result = 'float: left';
    }

    if ($this->spacing)
    {
      $Result .= '; margin: ' . $this->spacing . 'px';
    }
    if ($this->padding)
    {
      $Result .= '; padding: ' . $this->padding . 'px';
    }
    if ($this->min_box_width)
    {
      $Result .= '; min-width: ' . $this->min_box_width;
    }

    return $Result;
  }
}

/**
 * Placeholder class for extending classes.
 * Descendents that inherit from this class will automatically be updated to a new
 * implementation if the ancestor here is changed. Prevents all descendent classes
 * from specifying that they explicitly use an HTML table for rendering.
 * @see CSS_FLOW_GRID
 * @see HTML_TABLE_GRID
 * @package webcore
 * @subpackage grid
 * @version 3.4.0
 * @since 2.6.0
 * @abstract
 */
abstract class STANDARD_GRID extends HTML_TABLE_GRID {}

?>