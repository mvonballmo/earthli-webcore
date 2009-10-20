<?php

/**
 * @copyright Copyright (c) 2002-2009 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package albums
 * @subpackage gui
 * @version 3.2.0
 * @since 2.5.0
 */

/****************************************************************************

Copyright (c) 2002-2009 Marco Von Ballmoos

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
require_once ('webcore/gui/select_list.php');

/**
 * Handles shared drawing for {@link PICTURE} and {@link JOURNAL} lists.
 * @package albums
 * @subpackage gui
 * @version 3.2.0
 * @since 2.5.0
 */
class ALBUM_ENTRY_LIST extends SELECT_LIST
{
  /**
   * @param ALBUM_APPLICATION $app Main application.
   */
  public function __construct ($app)
  {
    parent::__construct ($app);
    $this->append_column ('Name');
    $this->append_column ('Date');
  }

  /**
   * @param ALBUM $obj
   * @param integer $index
   * @private
   */
  protected function _draw_column_contents ($obj, $index)
  {
    switch ($index)
    {
    case 0:
      $this->_draw_selector ($obj);
      break;
    case 1:
      $t = $obj->title_formatter ();
      $t->max_visible_output_chars = 0;
      echo $obj->title_as_link ($t);
      break;
    case 2:
      $folder = $obj->parent_folder ();
      echo $folder->format_date ($obj->date);
      break;
    }
  }
}

?>