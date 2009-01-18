<?php

/**
 * @copyright Copyright (c) 2002-2008 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage grid
 * @version 3.0.0
 * @since 2.5.0
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
 * Displays {@link SEARCH} objects from a {@link QUERY}.
 * @package webcore
 * @subpackage grid
 * @version 3.0.0
 * @since 2.5.0
 */
class SEARCH_GRID extends STANDARD_GRID
{
  /**
   * @var string
   */
  var $object_name = 'search';
  /**
   * @var boolean
   */
  var $show_separator = FALSE;
  /**
   * @var string
   */
  var $width = '';

  /**
   * @param SEARCH &$obj
    * @access private
    */
  function _draw_box (&$obj)
  {
?>
  <div class="grid-title">
    <?php echo $obj->title_as_link (); ?>
  </div>
  <div>
  <?php
    $this->_draw_menu_for ($obj, Menu_size_compact);
  ?>
  Searches <span class="field"><?php echo $obj->type; ?>s</span>.
  </div>
  <div class="description">
  <?php
    $renderer = $obj->handler_for (Handler_html_renderer);
    $renderer->display ($obj);
  ?>
  </div>
<?php
  }
}
?>