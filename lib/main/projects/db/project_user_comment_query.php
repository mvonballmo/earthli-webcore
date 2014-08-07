<?php

/**
 * @copyright Copyright (c) 2002-2014 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package projects
 * @subpackage db
 * @version 3.5.0
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
require_once ('webcore/db/user_comment_query.php');

/**
 * Retrieves {@link COMMENT}s visible to a {@link USER}.
 * @package projects
 * @subpackage db
 * @version 3.5.0
 * @since 1.4.1
 */
class PROJECT_USER_COMMENT_QUERY extends USER_MULTI_TYPE_COMMENT_QUERY
{
  /**
   * @param PROJECT_APPLICATION $context Main application.
   */
  public function __construct ($context)
  {
    parent::__construct ($context);

    $this->add_select ('entry.kind as entry_kind, chng.number as chng_number, chng.job_id as chng_job_id' .
                       ', job.status as job_status, job.priority as job_priority, job.closer_id as job_closer_id' .
                       ', job.time_closed as job_time_closed, entry.time_created as job_time_created');

    $this->add_table ("{$this->app->table_names->changes} chng", 'chng.entry_id = entry.id', 'LEFT');
    $this->add_table ("{$this->app->table_names->jobs} job", 'job.entry_id = entry.id', 'LEFT');
  }

  /**
   * Set properties for the job or change associated with a comment.
   * This is usually just the bare minimum of properties needed to display a
   * link to the job or change. This avoids retrieving all the job or change
   * data when only the link needs to be displayed (since this query shows
   * comments, not full entries).
   * @param PROJECT_ENTRY $entry The entry whose properties should be set.
   * @access private
   */
  protected function _prepare_entry ($entry)
  {
    parent::_prepare_entry ($entry);

    $db = $this->db;
    $entry->kind = $db->f ('entry_kind');

    switch ($entry->type)
    {
    case 'change':
      $entry->number = $db->f ('chng_number');
      $entry->job_id = $db->f ('chng_job_id');
      break;
    case 'job':
      $entry->time_created->set_from_iso ($db->f ('job_time_created'));
      $branch_info = $entry->main_branch_info ();
      $branch_info->status = $db->f ('job_status');
      $branch_info->priority = $db->f ('job_priority');
      $branch_info->time_closed->set_from_iso ($db->f ('job_time_closed'));
      $branch_info->closer_id = $db->f ('job_closer_id');
      break;
    }
  }
}

?>