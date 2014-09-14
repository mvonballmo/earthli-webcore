<?php

/**
 * @copyright Copyright (c) 2002-2014 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage db
 * @version 3.6.0
 * @since 2.2.1
 */

/****************************************************************************

Copyright (c) 2002-2014 Marco Von Ballmoos

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
require_once ('webcore/db/sql_unique_storage.php');

/**
 * Specialized storage for {@link MULTI_TYPE_ENTRY}s.
 * @package webcore
 * @subpackage db
 * @version 3.6.0
 * @since 2.2.1
 * @access private
 */
class SQL_MULTI_TYPE_ENTRY_STORAGE extends SQL_UNIQUE_STORAGE
{
  /**
   * @param SQL_TABLE $table
   * @param integer $action
   * @param STORABLE $obj
   * @access private
   */
  protected function _commit_table ($table, $action, $obj)
  {
    if (($table->name == $obj->secondary_table_name ()) && ($action == Storage_action_create))
    {
      $table->update ('entry_id', $obj->entry_id);
    }
    
    parent::_commit_table ($table, $action, $obj);

    if (($table->name == $obj->table_name ()) && ($action == Storage_action_create))
    {
      $obj->entry_id = $obj->id;
    }
  }
}

?>