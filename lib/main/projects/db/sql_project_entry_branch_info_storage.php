<?php

/**
 * @copyright Copyright (c) 2002-2009 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package projects
 * @subpackage db
 * @version 3.2.0
 * @since 1.4.1
 * @access private
 */

/****************************************************************************

Copyright (c) 2002-2009 Marco Von Ballmoos

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
require_once ('webcore/db/sql_unique_storage.php');

/**
 * Specialized storage for {@link JOB_BRANCH_INFO} objects.
 * @package projects
 * @subpackage db
 * @version 3.2.0
 * @since 1.4.1
 * @access private
 */
class SQL_PROJECT_ENTRY_BRANCH_INFO_STORAGE extends SQL_UNIQUE_STORAGE
{
  /**
   * @param SQL_TABLE $table
   * @param integer $action
   * @param STORABLE $obj
   */
  protected function _commit_table ($table, $action, $obj)
  {
    if (($table->name == $obj->secondary_table_name ()) && ($action == Storage_action_create))
    {
      $table->update ('entry_to_branch_id', $obj->entry_to_branch_id);
    }
    
    parent::_commit_table ($table, $action, $obj);

    if (($table->name == $obj->table_name ()) && ($action == Storage_action_create))
    {
      $obj->entry_to_branch_id = $obj->id;
    }
  }
}

?>