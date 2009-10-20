<?php

/**
 * @copyright Copyright (c) 2002-2009 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage grid
 * @version 3.2.0
 * @since 2.5.0
 */

/****************************************************************************

Copyright (c) 2002-2009 Marco Von Ballmoos

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
 * Displays {@link ICON}s from a {@link QUERY}.
 * @package webcore
 * @subpackage grid
 * @version 3.2.0
 * @since 2.5.0
 */
class ICON_GRID extends STANDARD_GRID
{
  /**
   * @var string
   */
  public $box_style = '';

  /**
   * @var string
   */
  public $object_name = 'Icon';

  /**
   * @var string
   */
  public $width = '';

  /**
   * @var boolean
   */
  public $centered = true;

  /**
   * @var boolean
   */
  public $even_columns = true;

  /**
   * @var boolean
   */
  public $is_chooser = false;

  /**
   * Draw JavaScripts used by this grid.
   * @access private
   */
  protected function _draw_scripts ()
  {
    parent::_draw_scripts ();
?>
  picker = new PICKER ();
  picker.register (Query_string.item ('fieldid'));
<?php
  }

  /**
   * @param ICON $obj
   * @access private
   */
  protected function _draw_box ($obj)
  {
?>
<div style="text-align: center">
  <div style="height: 100px">
    <?php
      if ($this->is_chooser)
      {
        echo '<a href="#" onclick="picker.select_value (\'' . $obj->url . '\'); return false;">';
        echo $obj->icon_as_html ();
        echo '</a>';
      }
      else
      {
        echo $obj->icon_as_html ();
      }
    ?>
  </div>
  <div style="margin-top: 1em">
    <?php
      $this->_draw_menu_for ($obj, Menu_size_minimal, Menu_align_left);
      echo $obj->title_as_html ();
      if ($obj->category)
      {
        echo " ($obj->category)";
      }
    ?>
  </div>
</div>
<?php
   }
}
?>