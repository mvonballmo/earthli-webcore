<?php

/**
 * @copyright Copyright (c) 2002-2008 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package projects
 * @subpackage db
 * @version 3.0.0
 * @since 1.4.1
 */

/****************************************************************************

Copyright (c) 2002-2008 Marco Von Ballmoos

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
require_once ('webcore/db/query.php');

/**
 * Retrieves {@link PROJECT_ENTRY_BRANCH_INFO}s for a particular {@link PROJECT_ENTRY}.
 * @package projects
 * @subpackage db
 * @version 3.0.0
 * @since 1.4.1
 */
class PROJECT_ENTRY_BRANCH_INFO_QUERY extends QUERY
{
  /**
   * SQL alias for the "main" table.
   * @var string
   */
  var $alias = 'bra';

  /**
   * @param ENTRY &$entry Entry for which branches are retrieved.
   */
  function PROJECT_ENTRY_BRANCH_INFO_QUERY (&$entry)
  {
    QUERY::QUERY ($entry->app);
    $this->_entry =& $entry;
  }

  /**
   * Apply default restrictions and tables.
   */
  function apply_defaults () 
  {
    $this->set_select ('bra.title, etob.id, etob.entry_id, etob.branch_id, etob.branch_release_id');
    $this->set_table ($this->app->table_names->branches . ' bra');
    $this->add_table ($this->app->table_names->entries_to_branches . ' etob', 'bra.id = etob.branch_id');
    $this->set_order ('title DESC');
  }

  /**
   * Prepare security- and filter-based restrictions.
   * @access private
   */
  function _prepare_restrictions ()
  {
    $this->_returns_no_data = ! isset ($this->_entry) || ! $this->_entry->exists ();
    if (! $this->_returns_no_data)
    {
      parent::_prepare_restrictions ();
      $this->_calculated_restrictions [] = "etob.entry_id = {$this->_entry->id}";
    }
  }

  /**
   * @return PROJECT_ENTRY_BRANCH_INFO
    * @access private
    */
  function _make_object ()
  {
    $class_name = $this->app->final_class_name ('PROJECT_ENTRY_BRANCH_INFO', 'projects/obj/project_entry.php');
    return new $class_name ($this->_entry);
  }

  /**
   * Result set with each object stored as [branch_id => PROJECT_ENTRY_INFO].
   * @return array[PROJECT_ENTRY_INFO]
   */
  function &indexed_objects_by_branch_id ()
  {
    $this->_index_by_branch_id = TRUE;
    $Result = $this->indexed_objects ();
    $this->_index_by_branch_id = FALSE;
    return $Result;
  }

  /**
   * @param PROJECT_ENTRY_INFO
   * @return integer
   * @access private
   */
  function _id_for_object (&$obj)
  {
    if ($this->_index_by_branch_id)
      return $obj->branch_id;
    else
      return $obj->id;
  }

  /**
   * @var ENTRY
   * @access private
   */
  var $_entry;
  /**
   * @var boolean
   * @access private 
   */
  var $_index_by_branch_id = FALSE;
}

/**
 * Retrieves {@link JOB_BRANCH_INFO}es for a particular {@link JOB}.
 * @package projects
 * @subpackage db
 * @version 3.0.0
 * @since 1.4.1
 */
class JOB_BRANCH_INFO_QUERY extends PROJECT_ENTRY_BRANCH_INFO_QUERY
{
  /**
   * @param ENTRY &$entry Entry for which branches are retrieved.
   */
  function JOB_BRANCH_INFO_QUERY (&$entry)
  {
    PROJECT_ENTRY_BRANCH_INFO_QUERY::PROJECT_ENTRY_BRANCH_INFO_QUERY ($entry);
    $this->add_select ('jtob.*');
    $this->add_table ("{$this->app->table_names->jobs_to_branches} jtob", 'etob.id = jtob.entry_to_branch_id');
  }

  /**
   * @return JOB_BRANCH_INFO
    * @access private
    */
  function _make_object ()
  {
    $class_name = $this->app->final_class_name ('JOB_BRANCH_INFO', 'projects/obj/job.php');
    return new $class_name ($this->_entry);
  }
}

/**
 * Retrieves {@link CHANGE_BRANCH_INFO}es for a particular {@link CHANGE}.
 * @package projects
 * @subpackage db
 * @version 3.0.0
 * @since 1.4.1
 */
class CHANGE_BRANCH_INFO_QUERY extends PROJECT_ENTRY_BRANCH_INFO_QUERY
{
  /**
   * @param ENTRY &$entry Entry for which branches are retrieved.
   */
  function CHANGE_BRANCH_INFO_QUERY (&$entry)
  {
    PROJECT_ENTRY_BRANCH_INFO_QUERY::PROJECT_ENTRY_BRANCH_INFO_QUERY ($entry);
    $this->add_select ('ctob.*');
    $this->add_table ("{$this->app->table_names->changes_to_branches} ctob", 'etob.id = ctob.entry_to_branch_id');
  }

  /**
   * @return CHANGE_BRANCH_INFO
    * @access private
    */
  function _make_object ()
  {
    $class_name = $this->app->final_class_name ('CHANGE_BRANCH_INFO', 'projects/obj/change.php');
    return new $class_name ($this->_entry);
  }
}

?>