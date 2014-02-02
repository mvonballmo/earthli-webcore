<?php

/**
 * @copyright Copyright (c) 2002-2014 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package projects
 * @subpackage db
 * @version 3.4.0
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
require_once ('webcore/db/folder_entry_query.php');
require_once ('projects/db/project_query_toolkit.php');

/**
 * Retrieves {@link RELEASE} related to a particular {@link PROJECT}.
 * @package projects
 * @subpackage db
 * @version 3.4.0
 * @since 1.4.1
 */
class PROJECT_RELEASE_QUERY extends FOLDER_ENTRY_QUERY
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
    release_query_order($this);
  }

  /**
   * Configure to show pending releases in next release order.
   * Releases without a date sort after those with a date and
   * shipped releases sort last.
   * @param integer $filter Use {@link Release_not_locked} or {@link
   * Release_is_pending}.
   */
  public function set_up_pending ($filter = Release_is_pending)
  {
    $this->set_filter ($filter);
    release_query_order($this);
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
    parent::_prepare_object ($obj);

    $class_name = $this->app->final_class_name ('BRANCH', 'projects/obj/branch.php');    
    $obj->_branch = new $class_name ($this->app);
    $obj->_branch->id = $this->db->f ('branch_id');
    $obj->_branch->state = $this->db->f ('branch_state');
    $obj->_branch->title = $this->db->f ('branch_title');
    $obj->_branch->set_parent_folder ($this->_folder);
  }

  /**
   * Return the SQL that retrieves the folder id from the query.
   * Since the release is now a member of the branch, it does not contain a 'folder_id'
   * in it's main table, but joins the branch table to retrieve it. The default behavior
   * for {@link FOLDER_ENTRY_QUERY} returns the folder id from the main table, but here
   * we return it from the branch table instead.
   * @return string
   */
  protected function _sql_folder_id ()
  {
    return 'bra.folder_id';
  }

  /**
   * Name of the default permission set to use.
   * @var string
   * @access private
   */
  protected $_privilege_set = Privilege_set_folder;
}

?>