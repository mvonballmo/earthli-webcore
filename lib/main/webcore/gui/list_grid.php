<?php

/**
 * @copyright Copyright (c) 2002-2009 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage grid
 * @version 3.2.0
 * @since 2.2.1
 */

/****************************************************************************

Copyright (c) 2002-2009 Marco Von Ballmoos

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
 * @version 3.2.0
 * @since 2.2.1
 * @abstract
 */
abstract class LIST_GRID extends STANDARD_GRID
{
  /**
   * Add space between columns?
   * @var boolean*/
  public $use_spacers = true;

  /**
   * @var boolean
   */
  public $show_separator = false;

  /**
   * @var boolean
   */
  public $even_columns = false;

  /**
   * Add a column to the end of the list.
   * @param string $name
   * @param string $alignment Can be 'left', 'center' or 'right'
   * @access private
   */
  public function append_column ($name, $alignment = 'left')
  {
    $col = null;  // Compiler warning
    $col->name = $name;
    $col->alignment = $alignment;
    $this->_columns [] = $col;
  }

  /**
   * Add a column to the beginning of the list.
   * @param string $name
   * @param string $alignment Can be 'left', 'center' or 'right'
   * @access private
   */
  public function prepend_column ($name, $alignment = 'left')
  {
    $col = null; // Compiler warning
    $col->name = $name;
    $col->alignment = $alignment;
    array_unshift ($this->_columns, $col);
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
        echo '<td style="vertical-align: top; text-align: ' . $this->_columns [$index]->alignment . '">';
      }
      $this->_draw_column_contents ($obj, $index);
      if (($index < $count - 1) && ($this->use_spacers))
      {
        echo '</td>';
        $this->_draw_spacer ('td');
      }

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
      echo "<tr>\n";
      while ($index < $count)
      {
        echo '<th style="text-align: ' . $this->_columns [$index]->alignment . '">' . $this->_columns [$index]->name . "</th>\n";
        if (($index < $count - 1) && ($this->use_spacers))
        {
          $this->_draw_spacer ('th');
        }
  
        $index += 1;
      }
      echo "</tr>\n";
    }
  }

  /**
   * Draw the given column's data using the given object.
   * @param object $obj
   * @param integer $index
   * @access private
   * @abstract
   */
  protected abstract function _draw_column_contents ($obj, $index);

  /**
   * @param string $cell_type Can be 'td' or 'th'.
   * @access private
   */
  protected function _draw_spacer ($cell_type)
  {
    echo "<$cell_type class=\"spacer\">&nbsp;</$cell_type>\n";
  }
  
  /**
   * @var array[LIST_GRID_COLUMN]
   * @see LIST_GRID_COLUMN
   * @access private
   */
  protected $_column_names = array ();
}

/**
 * Properties of a column in a {@link LIST_GRID}.
 * @package webcore
 * @subpackage grid
 * @version 3.2.0
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

  /**
   * Horizontal alignment for header and data.
   * Can be 'left', 'right', 'center'.
   * @var string
   */
  public $alignment = 'left';
}

?>