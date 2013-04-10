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

/**
 * Retrieves {@link RELEASE}s visible to a {@link PROJECT_USER}.
 * @package projects
 * @subpackage db
 * @version 3.3.0
 * @since 1.4.1
 */
class PROJECT_USER_RELEASE_QUERY extends USER_ENTRY_QUERY
{
  /**
   * SQL alias for the "main" table.
   * @var string
   */
  public $alias = 'rel';

  /**
   * Apply default restrictions and tables.
   */
  public function apply_defaults ()
  {
    $this->set_select ('rel.*, bra.folder_id as branch_folder_id, bra.title as branch_title, bra.id as branch_id, bra.state as branch_state');
    $this->set_table ($this->app->table_names->releases . ' rel');
    $this->add_table ($this->app->table_names->branches . ' bra', 'rel.branch_id = bra.id');
    $this->add_table ($this->app->table_names->folders . ' fldr', 'bra.folder_id = fldr.id');
    $this->set_order ('bra.folder_id, rel.branch_id, time_created DESC');
  }

  /**
   * @return RELEASE
   * @access private
   */
  protected function _make_object ()
  {
    $class_name = $this->app->final_class_name ('RELEASE', 'projects/obj/release.php');
    return new $class_name ($this->app);
  }

  /**
   * Perform any setup needed on each returned object.
   * @param RELEASE
   * @access private
   */
  protected function _prepare_object ($obj)
  {
    // build the branch first.

    $class_name = $this->app->final_class_name ('BRANCH', 'projects/obj/branch.php');    
    $obj->_branch = new $class_name ($this->app);
    $obj->_branch->id = $this->db->f ('branch_id');
    $obj->_branch->state = $this->db->f ('branch_state');
    $obj->_branch->title = $this->db->f ('branch_title');

    // set a 'fake' folder id (so that the release appears as an ENTRY to the parent query)
    $fldr_id = $this->db->f ('branch_folder_id');
    $obj->set_parent_folder ($this->_user->folder_at_id ($fldr_id));
    $obj->_branch->set_parent_folder ($obj->parent_folder ());
  }
}

?>