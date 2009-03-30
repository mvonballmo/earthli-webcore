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
require_once ('webcore/db/folder_comment_query.php');

/**
 * Retrieves {@link COMMENT}s related to a particular {@link BRANCH}.
 * @package projects
 * @subpackage db
 * @version 3.0.0
 * @since 1.4.1
 */
class BRANCH_COMMENT_QUERY extends FOLDER_MULTI_TYPE_COMMENT_QUERY
{
  /**
   * @param BRANCH $branch Branch for which comments are retrieved.
   */
  public function BRANCH_COMMENT_QUERY ($branch)
  {
    $folder = $branch->parent_folder ();
    FOLDER_MULTI_TYPE_COMMENT_QUERY::FOLDER_MULTI_TYPE_COMMENT_QUERY ($folder);
    $this->_branch = $branch;

    $table_names = $this->app->table_names;

    $this->add_select ('entry.kind as entry_kind, chng.number as chng_number, chng.job_id as chng_job_id, etob.branch_release_id' .
                       ', jtob.branch_status, jtob.branch_priority, jtob.branch_closer_id' .
                       ', jtob.branch_time_closed, entry.time_created as job_time_created');

    $this->add_table ("{$table_names->changes} chng", 'chng.entry_id = entry.id', 'LEFT');
    $this->add_table ("{$table_names->jobs} job", 'job.entry_id = entry.id', 'LEFT');
    $this->add_table ("{$table_names->entries_to_branches} etob", 'etob.entry_id = entry.id');
    $this->add_table ("{$table_names->jobs_to_branches} jtob", 'jtob.entry_to_branch_id = etob.id', 'LEFT');
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
   * Set the type for the entry.
   * This allows the query to determine which type of object to create for each
   * row in the result.
   * @param ENTRY $entry The entry whose properties should be set.
   * @access private
   */
  protected function _prepare_entry ($entry)
  {
    parent::_prepare_entry ($entry);

    $db = $this->db;
    $branch_info = $entry->main_branch_info ();
    $branch_info->release_id = $db->f ('branch_release_id');

    switch ($entry->type)
    {
    case 'job':
      $entry->time_created->set_from_iso ($db->f ('job_time_created'));
      $branch_info = $entry->main_branch_info ();
      $branch_info->status = $db->f ('branch_status');
      $branch_info->priority = $db->f ('branch_priority');
      $branch_info->time_closed->set_from_iso ($db->f ('branch_time_closed'));
      $branch_info->closer_id = $db->f ('branch_closer_id');
      break;
    case 'change':
      $entry->number = $db->f ('chng_number');
      $entry->job_id = $db->f ('chng_job_id');
      break;
    }
  }
}
?>