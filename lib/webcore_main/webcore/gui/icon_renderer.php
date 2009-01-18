<?php

/**
 * @copyright Copyright (c) 2002-2008 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage renderer
 * @version 3.0.0
 * @since 2.7.0
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
require_once ('webcore/gui/object_renderer.php');

/**
 * Renders an {@link ICON} as HTML.
 * @package webcore
 * @subpackage renderer
 * @version 3.0.0
 * @since 2.7.0
 */
class ICON_RENDERER extends OBJECT_RENDERER
{
  /**
   * Outputs the object as HTML.
   * @param ICON &$obj
   * @access private
   */
  function _display_as_html (&$obj)
  {
?>
<div style="text-align: center">
  <div style="height: 100px">
    <?php echo $obj->icon_as_html (); ?>
  </div>
  <div style="margin-top: 1em">
    <?php
      echo $obj->title_as_html ();
      if ($obj->category)
        echo " ($obj->category)";
    ?>
  </div>
</div>
<?php
  }
}

?>