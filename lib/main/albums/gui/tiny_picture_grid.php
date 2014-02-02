<?php

/**
 * @copyright Copyright (c) 2002-2014 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package albums
 * @subpackage gui
 * @version 3.4.0
 * @since 2.8.0
 */

/****************************************************************************

Copyright (c) 2002-2014 Marco Von Ballmoos

This file is part of earthli Albums.

earthli Albums is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

earthli Albums is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with earthli Albums; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

For more information about the earthli Albums, visit:

http://www.earthli.com/software/webcore/albums

****************************************************************************/

/** */
require_once ('webcore/gui/grid.php');

/**
 * Displays a reduced thumbnail of the {@link PICTURE}.
 * Does not use an HTML table to constrain columns, so is useful for flow layouts on
 * non-application pages. Use {@link $max_width} and {@link $max_height} to set the
 * size of the thumbnail.
 * @package albums
 * @subpackage gui
 * @version 3.4.0
 * @since 2.8.0
 */
class TINY_PICTURE_GRID extends CSS_FLOW_GRID
{
  public $object_name = 'Picture';
  public $box_style = '';
  public $width = '';
  public $max_width = 100;
  public $max_height = 75;

  /**
   * @param PICTURE $obj
   */
  protected function _draw_box ($obj)
  {
    $url = $obj->thumbnail_location (Force_root_on);
    $is_local = $url->has_local_domain ();
    $metrics = $obj->thumbnail_metrics ($is_local);
    if ($is_local)
    {
      $metrics->resize_to_fit ($this->max_width, $this->max_height);
    }
    else
    {
      $metrics->resize ($this->max_width, $this->max_height);
    }
  ?>
    <a href="<?php echo $obj->home_page (); ?>"><?php echo $metrics->as_html_without_link ($obj->title_as_plain_text ()); ?></a>
  <?php
  }
}

?>