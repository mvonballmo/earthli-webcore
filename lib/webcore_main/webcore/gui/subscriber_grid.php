<?php

/**
 * @copyright Copyright (c) 2002-2008 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage grid
 * @version 3.0.0
 * @since 2.2.1
 */

/****************************************************************************

Copyright (c) 2002-2008 Marco Von Ballmoos

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
 * Displays {@link SUBSCRIBER}s from a {@link QUERY}.
 * Displays with a checkbox, so the grid can be used in a form to select/deselect subscribers.
 * @package webcore
 * @subpackage grid
 * @version 3.0.0
 * @since 2.2.1
 */
class SUBSCRIBER_GRID extends STANDARD_GRID
{
  /**
   * @var string
   */
  var $object_name = 'Subscriber';
  /**
   * @var integer
   */
  var $spacing = 2;
  /**
   * @var string
   */
  var $width = '';
  /**
   * @var boolean
   */
  var $show_separator = FALSE;

  /**
   * @param SUBSCRIBER &$obj
    * @access private
    */
  function _draw_box (&$obj)
  {
?>
    <input type="checkbox" value="<?php echo $obj->email; ?>" name="subscriber_ids[]" checked>
<?php
    echo $obj->title_as_link ();
  }

}
?>