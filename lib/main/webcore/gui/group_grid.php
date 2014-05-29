<?php

/**
 * @copyright Copyright (c) 2002-2014 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage grid
 * @version 3.5.0
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
 * Displays {@link GROUP}s from a {@link QUERY}.
 * @package webcore
 * @subpackage grid
 * @version 3.5.0
 * @since 2.2.1
 */
class GROUP_GRID extends STANDARD_GRID
{
  /**
   * @param APPLICATION $context Main application.
   */
  function __construct($context)
  {
    parent::__construct($context);

    $this->width = '';
  }

  /**
   * @param GROUP $obj
   * @access private
   */
  protected function _draw_box ($obj)
  {
    $this->_draw_menu_for ($obj, Menu_size_minimal);
    $this->_new_column ();
    echo $obj->title_as_link ();
    $user_query = $obj->user_query ();
    $this->_new_column ('class="numeric"');
    echo $user_query->size ();
  }

  /**
   * @access private
   */
  protected function _draw_header ()
  {
?>
<tr>
  <td></td>
  <th>Name</th>
  <th>Users</th>
</tr>
<?php
  }
}

?>