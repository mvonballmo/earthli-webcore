<?php

/**
 * @copyright Copyright (c) 2002-2009 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package albums
 * @subpackage gui
 * @version 3.1.0
 * @since 2.9.0
 * @access private
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
require_once ('webcore/gui/entry_navigator.php');

/**
 * Display a list of {@link PICTURE}s 'around' the current one.
 * @package albums
 * @subpackage gui
 * @version 3.1.0
 * @since 2.9.0
 */
class PICTURE_NAVIGATOR extends MULTI_TYPE_ENTRY_NAVIGATOR
{
  /**
   * Modify the query to navigate.
   * @param QUERY $query
   * @access private
   */
  protected function _adjust_query ($query)
  {
    parent::_adjust_query ($query);

    $journal = read_var ('journal');
    $first_day = read_var ('first_day');
    $last_day = read_var ('last_day');

    if ($journal)
    {
      $folder = $this->_entry->parent_folder ();
      $jrnl_query = $folder->entry_query ();
      $jrnl = $jrnl_query->object_at_id ($journal);
      if (isset ($jrnl))
      {
        $jrnl->adjust_query ($query);
      }
    }

    if (! $journal && $first_day)
    {
      $query->set_days ($first_day, $last_day);
    }

    $query->order_by_day('ASC');    
  }
}

?>