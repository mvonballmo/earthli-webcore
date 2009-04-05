<?php

/**
 * @copyright Copyright (c) 2002-2009 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package albums
 * @subpackage gui
 * @version 3.1.0
 * @since 2.7.0
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
require_once ('webcore/gui/entry_grid.php');

/**
 * Display {@link ALBUM_ENTRY}s from a {@link QUERY}.
 * @package albums
 * @subpackage gui
 * @version 3.1.0
 * @since 2.7.0
 */
abstract class ALBUM_ENTRY_GRID extends CONTENT_OBJECT_GRID
{
  /**
   * Render the grid itself.
   * @param array[object] $objs
   * @access private
   */
  protected function _draw ($objs)
  {
    if (sizeof ($objs))
    {
      $this->_url = new URL ($objs [0]->home_page ());
      $url = new URL ($this->env->url (Url_part_all));
      $this->_url->replace_arguments ($url->query_string ());
    }

    parent::_draw ($objs);
  }

  /**
   * Used to draw the entry's title in each cell.
   * @param PICTURE $obj
   * @return TITLE_FORMATTER
   * @access private
   */
  public function title_formatter ($obj)
  {
    $Result = parent::title_formatter ($obj);

    $this->_url->replace_argument ('id', $obj->id);
    $Result->location = $this->_url->as_text ();

    return $Result;
  }

  /**
   * Used to generate URLs for the pictures.
   * The current URL is used, replacing the page with the picture home page.
   * @var URL
   * @access private
   */
  protected $_url;
}

?>