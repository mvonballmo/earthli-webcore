<?php

/**
 * @copyright Copyright (c) 2002-2014 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage grid
 * @version 3.4.0
 * @since 2.2.1
 */

/****************************************************************************

Copyright (c) 2002-2014 Marco Von Ballmoos

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
 * Shows {@link FOLDER}s from a {@link QUERY}.
 * @abstract
 * @package webcore
 * @subpackage grid
 * @version 3.4.0
 * @since 2.2.1
 */
abstract class FOLDER_GRID extends STANDARD_GRID
{
  /**
   * Assign the tree of {@link FOLDER}s to display.
   * @param array[FOLDER]
   */
  public function set_folders ($folders)
  {
    $this->_folders = $folders;
    $this->_num_objects = null;
  }

  /**
   * The total number of object the grid represents.
   * @return integer
   * @access private
   */
  protected function _get_size ()
  {
    $this->assert (isset ($this->_folders), 'folders are not set', '_get_size', 'FOLDER_GRID');
    return sizeof ($this->_folders);
  }

  /**
   * Get the list of objects for the requested page.
   * @return array[FOLDER]
   * @access private
   */
  protected function _get_objects ()
  {
    $this->assert (isset ($this->_folders), 'folders are not set', '_get_objects', 'FOLDER_GRID');
    $num_objects_per_page = $this->_num_rows * $this->_num_columns;
    return array_slice ($this->_folders, ($this->pager->page_number - 1) * $num_objects_per_page, $num_objects_per_page);
  }

  /**
   * @var array[FOLDER]
   * @see FOLDER
   * @access private
   */
  protected $_folders;
}