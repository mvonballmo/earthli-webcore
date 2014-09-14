<?php

/**
 * @copyright Copyright (c) 2002-2014 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package projects
 * @subpackage db
 * @version 3.6.0
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
require_once ('projects/db/project_entry_query.php');

/**
 * Retrieves {@link CHANGE}s or {@link JOB}s related to a particular {@link BRANCH}.
 * @package projects
 * @subpackage db
 * @version 3.6.0
 * @since 1.4.1
 */
class BRANCH_ENTRY_QUERY extends GENERIC_PROJECT_ENTRY_QUERY
{
  /**
   * @param BRANCH $branch Branch for which entries are retrieved.
   */
  public function __construct ($branch)
  {
    $folder = $branch->parent_folder ();
    parent::__construct ($folder);
    $this->_branch = $branch;
  }

  /**
   * Specify which type of entries to retrieve.
   * If no type is specified, then assume all entries.
   * @param string $type 'job' and 'change' are valid here.
   */
  public function set_type ($type)
  {
    parent::set_type ($type);

    $table_names = $this->app->table_names;

    $this->add_select ('etob.branch_release_id');
    $this->add_table ("{$table_names->entries_to_branches} etob", 'etob.entry_id = entry.id');

    switch ($type)
    {
    case 'change':
      $this->add_select ('ctob.*');
      $this->add_table ("{$table_names->changes_to_branches} ctob", 'ctob.entry_to_branch_id = etob.id');
      break;

    case 'job':
      $this->add_select ('jtob.*');
      $this->add_table ("{$table_names->jobs_to_branches} jtob", 'jtob.entry_to_branch_id = etob.id');
      $this->set_order ('jtob.branch_status ASC, jtob.branch_priority DESC, entry.time_created DESC');
      break;

    default:
      $this->add_select ('jtob.*, ctob.*');
      $this->add_table ("{$table_names->jobs_to_branches} jtob", 'jtob.entry_to_branch_id = etob.id', 'LEFT');
      $this->add_table ("{$table_names->changes_to_branches} ctob", 'ctob.entry_to_branch_id = etob.id', 'LEFT');
      $this->set_order ('entry.type, entry.time_created DESC');
      break;
    }
  }

  /**
   * Prepare security- and filter-based restrictions.
   * @access private
   */
  protected function _prepare_restrictions ()
  {
    parent::_prepare_restrictions ();
    $this->_calculated_restrictions [] = "etob.branch_id = {$this->_branch->id}";
  }

  /**
   * @var BRANCH
   * @access private
   */
  protected $_branch;

  /**
   * Name of the default permission set to use.
   * @var string
   * @access private
   */
  protected $_privilege_set = Privilege_set_entry;
}

?>