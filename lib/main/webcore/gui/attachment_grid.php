<?php

/**
 * @copyright Copyright (c) 2002-2014 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage grid
 * @version 3.5.0
 * @since 2.5.0
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
 * Displays {@link ATTACHMENT}s from a {@link QUERY}.
 * @package webcore
 * @subpackage grid
 * @version 3.5.0
 * @since 2.5.0
 */
class ATTACHMENT_GRID extends STANDARD_GRID
{
  /**
   * @param CONTEXT $context Context to which this grid belongs.
   */
  public function __construct ($context)
  {
    parent::__construct ($context);

    $this->width = '';
    $this->even_columns = true;
  }

  /**
   * @param ATTACHMENT $obj
   * @access private
   */
  protected function _draw_box ($obj)
  {
?>
<div style="position: relative">
  <div class="top-left-overlay">
    <?php
    $this->_draw_menu_for ($obj, Menu_size_minimal);
    ?>
  </div>
  <a href="<?php echo $obj->home_page_as_html (); ?>"><?php
    if ($obj->is_image)
    {
      $thumb = $obj->thumbnail_as_html ();
      if ($thumb)
      {
        echo $thumb;
      }
      else
      {
        echo $obj->icon_as_html (One_hundred_px);
      }
    }
    else
    {
      echo $obj->icon_as_html (One_hundred_px);
    }
  ?></a>
  <h3>
    <?php
    echo $obj->title_as_html ();
    ?>
  </h3>
  <p class="detail">
    <?php
      echo $obj->mime_type . ' (' . file_size_as_text ($obj->size) . ')';
    ?>
  </p>
  <div class="text-flow">
  <?php echo $obj->description_as_html (); ?>
  </div>
</div>
<?php
   }
}
?>