<?php

/**
 * @copyright Copyright (c) 2002-2009 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage gui
 * @version 3.1.0
 * @since 2.7.1
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
require_once ('webcore/obj/webcore_object.php');

/**
 * Renders a location for a {@link RENDERABLE} into a {@link PAGE}.
 * @package webcore
 * @subpackage gui
 * @version 3.1.0
 * @since 2.7.1
 */
class LOCATION_RENDERER extends WEBCORE_OBJECT
{
  /**
   * Render 'obj' to 'page' as a link.
   * Updates the {@link PAGE::$location} and {@link PAGE::$title}.
   * @param PAGE $page
   * @param RENDERABLE $obj
   */
  public function add_to_page_as_link ($page, $obj)
  {
    $this->_add_context ($page, $obj);
    $page->title->add_object ($obj);
    $page->location->add_object_link ($obj);
  }

  /**
   * Render 'obj' to 'page' as text.
   * Updates the {@link PAGE::$location} and {@link PAGE::$title}.
   * @param PAGE $page
   * @param RENDERABLE $obj
   */
  public function add_to_page_as_text ($page, $obj)
  {
    $this->_add_context ($page, $obj);
    $page->title->add_object ($obj);
    $page->location->add_object_text ($obj);
  }
  
  /**
   * Render any parent objects to the title and location.
   * @param PAGE $page
   * @param RENDERABLE $obj
   * @access private
   */
  protected function _add_context ($page, $obj)
  {
  }
}

/**
 * Renders a location for a {@link RENDERABLE} into a {@link PAGE}.
 * @package webcore
 * @subpackage gui
 * @version 3.1.0
 * @since 2.7.1
 */
class OBJECT_IN_FOLDER_LOCATION_RENDERER extends LOCATION_RENDERER
{
  /**
   * Render any parent objects to the title and location.
   * @param PAGE $page
   * @param RENDERABLE $obj
   * @access private
   */
  protected function _add_context ($page, $obj)
  {
    $f = $obj->parent_folder ();
    $page->location->add_folder_link ($f);
    $page->title->add_object ($f);
  }
}

?>