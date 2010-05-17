<?php

/**
 * @copyright Copyright (c) 2002-2010 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package projects
 * @subpackage gui
 * @version 3.3.0
 * @since 3.1.0
 */

/****************************************************************************

Copyright (c) 2002-2010 Marco Von Ballmoos

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
 * Renders a {@link PROJECT_ENTRY} for display in an email (plain text or HTML).
 * @package projects
 * @subpackage gui
 * @version 3.3.0
 * @since 3.1.0
 * @abstract
 */
class PROJECT_ENTRY_NAVIGATOR extends MULTI_TYPE_ENTRY_NAVIGATOR
{
  /**
   * Modify the query to navigate.
   * @param QUERY $query
   * @access private
   */
  protected function _adjust_query ($query)
  {
    parent::_adjust_query ($query);
    
    $branch_info = $this->_entry->main_branch_info ();
    $branch = $branch_info->branch ();
    $release = $branch_info->release ();

    $query->restrict_by_op('entry.main_branch_id', $branch->id);
    
    if (isset ($release))
    {
      $query->restrict_by_op('entry.release_id', $release->id);
    }
  }
}

?>