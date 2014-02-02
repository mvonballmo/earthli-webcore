<?php

/**
 * @copyright Copyright (c) 2002-2014 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package albums
 * @subpackage gui
 * @version 3.4.0
 * @since 2.5.0
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
 * Displays only the thumbnail of the {@link PICTURE}.
 * Use only from the {@link ALBUM_FORM} to select a main picture.
 * @package albums
 * @subpackage gui
 * @version 3.4.0
 * @since 2.5.0
 */
class SIMPLE_PICTURE_GRID extends STANDARD_GRID
{
  /**
   * @var string
   */
  public $object_name = 'Picture';

  /**
   * @var boolean
   */
  public $show_separator = false;

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
   * @param PICTURE $obj
   * @access private
   */
  protected function _draw_box ($obj)
  {
    $f = $obj->date->formatter ();
    $f->clear_flags ();
    $album = $obj->parent_folder ();
    $title = $obj->title_as_plain_text () . " (" . $album->format_date ($obj->date, $f) . ")";
    $thumbnail = $obj->full_thumbnail_name ();
?>
  <a href="#" onclick="picker.select_value('<?php echo $obj->id ?>|<?php echo $thumbnail; ?>'); return false;"><img class="frame" src="<?php echo $thumbnail; ?>" alt="<?php echo $title; ?>"></a>
<?php
  }
}
?>