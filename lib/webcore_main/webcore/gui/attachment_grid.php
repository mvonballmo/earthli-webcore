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
 * Displays {@link ATTACHMENT}s from a {@link QUERY}.
 * @package webcore
 * @subpackage grid
 * @version 3.0.0
 * @since 2.5.0
 */
class ATTACHMENT_GRID extends STANDARD_GRID
{
  /**
   * @var string
   */
  var $box_style = 'chart';
  /**
   * @var integer
   */
  var $spacing = '5';
  /**
   * @var boolean
   */
  var $show_separator = FALSE;
  /**
   * @var string
   */
  var $object_name = 'Attachment';
  /**
   * @var string
   */
  var $width = '';
  /**
   * @var boolean
   */
  var $centered = TRUE;
  /**
   * @var boolean
   */
  var $even_columns = TRUE;
  /**
   * @var string
   */
  var $last_page;

  /**
   * @param USER &$obj
    * @access private
    */
  function _draw_box (&$obj)
  {
?>
<div style="text-align: center">
  <div class="chart-title">
    <?php
      echo $obj->title_as_html ();
    ?>
  </div>
  <div class="chart-body">
    <?php
      $this->_draw_menu_for ($obj, Menu_size_minimal, Menu_align_left);
    ?>
    <div style="clear: both">
    <a href="<?php echo $obj->home_page_as_html (); ?>"><?php
      if ($obj->is_image)
      {
        $thumb = $obj->thumbnail_as_html ();
        if ($thumb)
          echo $thumb;
        else
          echo $obj->icon_as_html ('100px');
      }
      else
        echo $obj->icon_as_html ('100px');
    ?></a>
    </div>
  </div>
  <div class="detail" style="text-align: center; margin-bottom: 1em">
    <?php
      echo $obj->mime_type . ' (' . file_size_as_text ($obj->size) . ')';
    ?>
  </div>
  <div style="text-align: left">
  <?php echo $obj->description_as_html (); ?>
  </div>
</div>
<?php
   }
}
?>