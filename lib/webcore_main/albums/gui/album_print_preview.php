<?php

/**
 * @copyright Copyright (c) 2002-2008 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package albums
 * @subpackage gui
 * @version 3.0.0
 * @since 2.5.0
 */

/****************************************************************************

Copyright (c) 2002-2008 Marco Von Ballmoos

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
require_once ('webcore/gui/print_preview.php');

/**
 * Render a {@link PICTURE} for printing.
 * @package albums
 * @subpackage gui
 * @version 3.0.0
 * @since 2.5.0
 */
class PICTURE_PRINT_RENDERER extends ENTRY_PRINT_RENDERER
{
  /**
   * @param PICTURE &$entry
    * @private
    */
  function _draw_title (&$entry)
  {
    // do nothing, title is drawn by renderer
  }
}

/**
 * Handle printing entries in an {@link ALBUM}.
 * @package albums
 * @subpackage gui
 * @version 3.0.0
 * @since 2.7.0
 * @access private
 */
class ALBUM_PRINT_RENDERER_OPTIONS extends PRINT_RENDERER_OPTIONS
{
  /**
   * Show pictures with {@link JOURNAL}s.
   * Pictures are displayed as thumbnails under the journal entry.
   * @var boolean
   */
  var $show_pictures = TRUE;
  /**
   * Resize pictures using album options?
   * If this is true, pictures are resized to the same size as they are on the picture's home page.
   * @var boolean
   */
  var $resize_pictures = TRUE;

  /**
   * Load values from the HTTP request.
   */
  function load_from_request ()
  {
    parent::load_from_request ();
    $this->show_pictures = read_var ('show_pictures');
    $this->resize_pictures = read_var ('resize_pictures');
  }
}

/**
 * Handle printing entries in an {@link ALBUM}.
 * @package albums
 * @subpackage gui
 * @version 3.0.0
 * @since 2.5.0
 */
class ALBUM_PRINT_PREVIEW extends PRINT_PREVIEW
{
  /**
   * @return ALBUM_PRINT_RENDERER_OPTIONS
   * @access private
   */
  function _make_print_options ()
  {
    return new ALBUM_PRINT_RENDERER_OPTIONS ($this->app);
  }

  /**
   * Make a new renderer for this class.
   * @param string $class_name
   * @return ENTRY_PRINT_RENDERER
   * @access private
   */
  function _make_renderer ($class_name)
  {
    if ($class_name == 'PICTURE')
      return new PICTURE_PRINT_RENDERER ($this);
    else
      return parent::_make_renderer ($class_name);
  }
}
?>