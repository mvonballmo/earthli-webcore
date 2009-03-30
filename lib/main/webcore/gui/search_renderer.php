<?php

/**
 * @copyright Copyright (c) 2002-2008 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage renderer
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
require_once ('webcore/gui/content_object_renderer.php');

/**
 * Render details for {@link SEARCH} objects.
 * @package webcore
 * @subpackage renderer
 * @version 3.0.0
 * @since 2.5.0
 */
class SEARCH_RENDERER extends CONTENT_OBJECT_RENDERER
{
  /**
   * Outputs the object as HTML.
   * @param SEARCH $obj
   * @access private
   */
  protected function _display_as_html ($obj)
  {
    $this->_echo_html_description ($obj);
    echo $obj->system_description_as_html ();
  }

  /**
   * Outputs the object as plain text.
   * @param SEARCH $obj
   * @access private
   */
  protected function _display_as_plain_text ($obj)
  {
    $this->_echo_plain_text_description ($obj);
    echo $obj->system_description_as_plain_text ();
  }
}

?>