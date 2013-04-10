<?php

/**
 * @copyright Copyright (c) 2002-2013 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage grid
 * @version 3.3.0
 * @since 2.7.0
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
require_once ('webcore/gui/grid.php');

/**
 * Attaches a checkbox to each item in the grid.
 * Used by {@link SEARCH} results to allow users to select one or more results.
 * @package webcore
 * @subpackage grid
 * @version 3.3.0
 * @since 2.7.0
 */
abstract class SELECTABLE_GRID extends STANDARD_GRID
{
  /**
   * Name of the selector control.
   * @var string
   */
  public $selector_name = 'ids[]';

  /**
   * Show check-box selectors next to items?
   * @var boolean
   */
  public $items_are_selectable = false;

  /**
   * Are the checkboxes enabled at first?
   * @var boolean
   */
  public $items_are_selected = false;

  /**
   * Render the start of a single cell.
   * @param object $obj
   * @access private
   */
  protected function _start_box ($obj)
  {
    parent::_start_box ($obj);
    if ($this->items_are_selectable)
    {
?>
  <div style="float: left">
    <?php $this->_echo_selector ($obj); ?>
  </div>
  <div style="margin-left: 2em">
<?php
    }
  }
  
  protected function _finish_box ($obj)
  {
    if ($this->items_are_selectable)
    {
?>
  </div>
<?php
    }
    parent::_finish_box ($obj);
  }
  
  /**
   * Display a checkbox to select this item.
   * @param object $obj
   * @access private
   */
  protected function _echo_selector ($obj)
  {
    if ($this->items_are_selectable)
    {
      if ($this->items_are_selected)
      {
        $state = ' checked';
      }
      else
      {
        $state = '';
      }
      echo '<input type="checkbox" name="' . $this->selector_name . '" value="' . $obj->id . '"' . $state . '>';
    }
  }
}

?>