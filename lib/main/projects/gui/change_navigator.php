<?php

/**
 * @copyright Copyright (c) 2002-2014 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package projects
 * @subpackage gui
 * @version 3.5.0
 * @since 1.4.1
 */

/****************************************************************************

Copyright (c) 2002-2014 Marco Von Ballmoos

This file is part of earthli Projects.

earthli Projects is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

earthli Projects is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with earthli Projects; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

For more information about the earthli Projects, visit:

http://www.earthli.com/software/webcore/projects

****************************************************************************/

/** */
require_once ('webcore/gui/entry_navigator.php');

/**
 * Display a list of {@link CHANGE}s 'around' the current one.
 * @package projects
 * @subpackage gui
 * @version 3.5.0
 * @since 1.4.1
 */
class CHANGE_NAVIGATOR extends ENTRY_NAVIGATOR
{
  /**
   * Modify the query to navigate.
   * @param QUERY $query
   * @access private
   */
  protected function _adjust_query ($query)
  {
    parent::_adjust_query ($query);
    $query->add_select ('chng.number, entry.kind');
    $query->set_type ('change');
  }

  /**
   * @param CHANGE $obj Retrieve the title from this change.
   * @return string
   * @access private
   */
  protected function _text_for_list ($obj)
  {
    $props = $obj->kind_properties ();

    return $this->context->get_icon_with_text($props->icon, Sixteen_px, parent::_text_for_list($obj));
  }
}