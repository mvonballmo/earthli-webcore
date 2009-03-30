<?php

/**
 * @copyright Copyright (c) 2002-2008 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage gui
 * @version 3.0.0
 * @since 2.4.0
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
require_once ('webcore/gui/object_navigator.php');

/**
 * Navigates lists of {@link ENTRY}s.
 * @package webcore
 * @subpackage gui
 * @version 3.0.0
 * @since 2.4.0
 */
class ENTRY_NAVIGATOR extends OBJECT_NAVIGATOR
{
  /**
   * @param ENTRY $entry
   */
  public function ENTRY_NAVIGATOR ($entry)
  {
    OBJECT_NAVIGATOR::OBJECT_NAVIGATOR ($entry->context);
    $this->_entry = $entry;
  }

  /**
   * Modify the query to navigate.
   * Restrict the query selection to required fields.
   * The navigator needs only a few fields from an entry; use {@link QUERY::
   * set_select()} to reset the selection clause.
   * @param QUERY $query
   * @access private
   */
  protected function _adjust_query ($query)
  {
    $query->set_select ('entry.id, entry.title, entry.state');
  }
}

/**
 * Navigates lists of {@link MULTI_TYPE_ENTRY}s.
 * @package webcore
 * @subpackage gui
 * @version 3.0.0
 * @since 2.7.0
 */
class MULTI_TYPE_ENTRY_NAVIGATOR extends ENTRY_NAVIGATOR
{
  /**
   * Modify the query to navigate.
   * @param QUERY $query
   * @access private
   */
  protected function _adjust_query ($query)
  {
    parent::_adjust_query ($query);
    $query->add_select ('entry.type as entry_type');
  }
}

?>