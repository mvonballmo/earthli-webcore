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

Copyright (c) 2002-2014 Marco Von Ballmoos

This file is part of earthli Webcore.

earthli Webcore is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

earthli Webcore is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with earthli Webcore; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

For more information about the earthli Webcore, visit:

http://www.earthli.com/software/webcore

****************************************************************************/

/** */
require_once ('webcore/gui/grid.php');

/**
 * Shows columns of information for objects in a grid.
 * Each cell contains a list of columns; add columns with {@link append_column()}
 * and {@link prepend_column()}. Grids of this type should generally only have one
 * column when calling {@link set_ranges()} (behavior is undefined).
 * 
 * Descendents should override {@link _draw_column_contents()} to specify how each
 * column per object is drawn.
 * 
 * @package webcore
 * @subpackage grid
 * @version 3.6.0
 * @since 2.2.1
 * @abstract
 */
abstract class LIST_GRID extends HTML_TABLE_GRID
{
  /**
   * Add a column to the end of the list.
   * @param string $name
   * @param string $css_class
   * @param string $type
   * @access private
   */
  public function append_column ($name, $css_class = '', $type = 'td')
  {
    $col = new LIST_GRID_COLUMN();
    $col->name = $name;
    $col->css_class = $css_class;
    $col->type = $type;
    $this->_columns [] = $col;
  }

  /**
   * Add a column to the beginning of the list.
   * @param string $name
   * @param string $css_class
   * @param string $type
   * @access private
   */
  public function prepend_column ($name, $css_class = '', $type = 'td')
  {
    $col = new LIST_GRID_COLUMN();
    $col->name = $name;
    $col->css_class = $css_class;
    $col->type = $type;
    array_unshift ($this->_columns, $col);
  }

  protected function _start_row($obj)
  {
    parent::_start_row($obj);
    $this->_row_index += 1;
  }

  protected function _start_box($obj)
  {
    $current_column = $this->_columns[0];
    if ($current_column->type == 'th')
    {
      $this->_internal_start_header_cell($this->_CSS_for_box());
    }
    else
    {
      parent::_start_box($obj);
    }
  }

  /**
   * @param object $obj
   * @access private
   */
  protected function _draw_box ($obj)
  {
    $index = 0;
    $count = sizeof ($this->_columns);
    while ($index < $count)
    {
      if ($index > 0)
      {
        $current_column = $this->_columns[$index];
        if ($current_column->type == 'th')
        {
          $this->_internal_finish_header_cell();
        }
        else
        {
          $this->_internal_finish_cell();
        }

        $attributes = '';
        if ($current_column->css_class)
        {
          $attributes = 'class="'. $current_column->css_class . '"';
        }
        if ($current_column->type == 'th')
        {
          $this->_internal_start_header_cell($attributes);
        }
        else
        {
          $this->_internal_start_cell($attributes);
        }
      }

      $this->_draw_column_contents ($obj, $index, $this->_row_index);

      $index += 1;
    }
  }

  /**
   * @access private
   */
  protected function _draw_header ()
  {
    $index = 0;
    $count = sizeof ($this->_columns);
    if ($count > 0)
    {
      $this->_internal_start_row();
      while ($index < $count)
      {
        $attributes = '';
        if ($this->_columns[$index]->css_class)
        {
          $attributes = 'class="'. $this->_columns[$index]->css_class . '"';
        }
        $this->_internal_start_header_cell($attributes);
        echo $this->_columns [$index]->name;
        $this->_internal_finish_header_cell();

        $index += 1;
      }
      $this->_internal_finish_row();
    }

    $this->_row_index = 0;
  }

  /**
   * Draw the given column's data using the given object.
   * @param object $obj
   * @param $col_index
   * @param $row_index
   * @access private
   * @abstract
   */
  protected abstract function _draw_column_contents ($obj, $col_index, $row_index);

  /**
   * @var LIST_GRID_COLUMN[]
   * @see LIST_GRID_COLUMN
   * @access private
   */
  protected $_columns = array ();

  /**
   * @var int */
  private $_row_index = 0;
}

/**
 * Properties of a column in a {@link LIST_GRID}.
 * @package webcore
 * @subpackage grid
 * @version 3.6.0
 * @since 2.7.0
 * @access private
 */
class LIST_GRID_COLUMN
{
  /**
   * Name to be displayed in the header. 
   * @var string
   */
  public $name;

  public $css_class = '';

  public $type;
}