<?php

/**
 * @copyright Copyright (c) 2002-2013 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package projects
 * @subpackage db
 * @version 3.3.0
 * @since 1.4.1
 */

/****************************************************************************

Copyright (c) 2002-2013 Marco Von Ballmoos

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
require_once ('webcore/db/user_entry_query.php');
require_once ('projects/db/project_query_toolkit.php');

/**
 * Retrieves {@link CHANGE}s or {@link JOB}s visible to a {@link PROJECT_USER}.
 * @package projects
 * @subpackage db
 * @version 3.3.0
 * @since 1.4.1
 */
class PROJECT_USER_ENTRY_QUERY extends USER_MULTI_ENTRY_QUERY
{
  /**
   * Specify which type of entries to retrieve.
   * If no type is specified, then assume all entries.
   * @param string $type 'job' and 'change' are valid here.
   */
  public function set_type ($type)
  {
    $this->_type = $type;
    $this->apply_defaults ();
    project_query_set_type ($this, $type);
  }

  /**
   * Reset the ordering to show recent items first.
   * @access private
   */
  protected function _order_by_recent ()
  {
    project_query_order_by_recent ($this, $this->_type);
  }

  /**
   * Perform any setup needed on each returned object.
   * @param PROJECT_ENTRY $obj
   * @access private
   */
  protected function _prepare_object ($obj)
  {
    parent::_prepare_object ($obj);

    $branch_info = $obj->main_branch_info ();
    $branch_info->release_id = $this->db->f ('release_id');
    
    switch ($obj->type)
    {
    case 'job':
      $branch_info->status = $this->db->f ('status');
      $branch_info->priority = $this->db->f ('priority');
      $branch_info->time_closed->set_from_iso ($this->db->f ('time_closed'));
      $branch_info->closer_id = $this->db->f ('closer_id');
      $branch_info->time_status_changed->set_from_iso ($this->db->f ('time_status_changed'));
      break;
    case 'change':
      $branch_info->applier_id = $this->db->f ('applier_id');
      $branch_info->time_applied->set_from_iso ($this->db->f ('time_applied'));
      break;
    }
  }
  
  /**
   * Last type applied by {@link set_type()}.
   * @var string
   * @access private
   */
  protected $_type;
}

?>