<?php

/**
 * @copyright Copyright (c) 2002-2008 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package polls
 * @subpackage gui
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

require_once ('webcore/gui/grid.php');

class POLL_GRID extends STANDARD_GRID
{
  var $object_name = 'poll';
  var $show_separator = FALSE;

  function _draw_box (&$obj)
  {
    $t = $obj->title_formatter ();
    $t->add_argument ('page', read_var ('page]'));
?>
  <?php echo $obj->display_text_as_html ();  ?>
  <div style="margin-bottom: .5em; text-align: right">
  <?php echo $obj->title_as_link ($t); ?>
  </div>
<?php
  }
}

?>