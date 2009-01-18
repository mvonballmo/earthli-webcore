<?php

/**
 * @copyright Copyright (c) 2002-2008 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage grid
 * @version 3.0.0
 * @since 2.2.1
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
require_once ('webcore/gui/object_in_folder_grid.php');

/**
 * Display {@link ENTRY} objects as the result of a {@link SEARCH}.
 * @package webcore
 * @subpackage gui
 * @version 3.0.0
 * @since 2.7.0
 */
class ENTRY_SUMMARY_GRID extends OBJECT_IN_FOLDER_SUMMARY_GRID
{
}

/**
 * Display {@link DRAFTABLE_ENTRY} objects as the result of a {@link SEARCH}.
 * @package webcore
 * @subpackage gui
 * @version 3.0.0
 * @since 2.7.0
 */
class DRAFTABLE_ENTRY_SUMMARY_GRID extends ENTRY_SUMMARY_GRID
{
  /**
   * Show search details for an object.
   * @param OBJECT_IN_FOLDER &$obj
   * @access private
   */
  function _echo_details (&$obj)
  {
    if ($obj->unpublished ())
      parent::_echo_details ($obj);
    else
      $this->_echo_user_information ('Published by', $obj->publisher (), $obj->time_published );
  }  
}

?>