<?php

/**
 * @copyright Copyright (c) 2002-2010 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package projects
 * @subpackage obj
 * @version 3.3.0
 * @since 1.7.0
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
require_once ('webcore/obj/comment.php');

/**
 * A comment for use with {@link PROJECT}s.
 * @package projects
 * @subpackage obj
 * @version 3.3.0
 * @since 1.4.1
 */
class PROJECT_COMMENT extends COMMENT
{
  /**
   * Apply class-specific restrictions to this query.
   * @param SUBSCRIPTION_QUERY $query
   * @param HISTORY_ITEM $history_item Action that generated this request. May be empty.
   * @access private
   */
  protected function _prepare_subscription_query ($query, $history_item)
  {
    $entry = $this->entry ();
    $folder = $this->parent_folder ();

    // If it's a change, grab the job id

    if (isset ($entry->job_id))
    {
      $job_id = $entry->job_id;
    }
    else
    {
      $job_id = 0;
    }

    $query->restrict ('watch_comments > 0');
    $query->restrict_kinds (array (Subscribe_folder => $folder->id
                                   , Subscribe_entry => $job_id
                                   , Subscribe_entry => $this->entry_id
                                   , Subscribe_comment => $this->parent_id
                                   , Subscribe_comment => $this->id
                                   , Subscribe_user => $this->creator_id));
  }
}

?>