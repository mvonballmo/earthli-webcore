<?php

/**
 * @copyright Copyright (c) 2002-2009 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package projects
 * @subpackage db
 * @version 3.1.0
 * @since 1.4.1
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

/**
 * Sets up a query for a particular entry type.
 * @param QUERY $query
 * @param string $type
 * @access private
 */
function project_query_set_type ($query, $type)
{
  $table_names = $query->app->table_names;

  switch ($type)
  {
  case 'change':
    $query->add_select ('chng.*');
    $query->add_table ($table_names->changes . ' chng', 'chng.entry_id = entry.id');
    break;
  case 'job':
    $query->add_select ('job.*');
    $query->add_table ($table_names->jobs . ' job', 'job.entry_id = entry.id');
    break;
  default:
    $query->add_select ('chng.*, job.*');
    $query->add_table ($table_names->changes . ' chng', 'chng.entry_id = entry.id', 'LEFT');
    $query->add_table ($table_names->jobs . ' job', 'job.entry_id = entry.id', 'LEFT');
    break;
  }
  $query->order_by_recent ();
}

/**
 * Set the most recent ordering for a query.
 * @param QUERY $query
 * @param string $type
 * @access private
 */
function project_query_order_by_recent ($query, $type)
{
  switch ($type)
  {
  case 'change':
    $query->set_order ('chng.time_applied DESC, entry.time_created DESC');
    break;
  case 'job':
    $query->set_order ('entry.state, (closer_id = 0) DESC, job.status ASC, job.priority DESC, job.time_closed DESC, entry.time_created DESC');
    break;
  default:
    $query->set_order ('entry.type, entry.time_created DESC');
    break;
  }
}

function release_query_order ($query)
{
  $query->add_select ('((rel.state = ' . Shipped . ') or (rel.state = ' . Locked . ')) as release_is_shipped');
  $query->add_select ('ISNULL(time_next_deadline) OR (time_next_deadline = \'0000-00-00 00:00:00\') as deadline_is_empty');
  $query->set_order ('bra.id, release_is_shipped ASC, time_shipped DESC, deadline_is_empty ASC, time_next_deadline ASC');
}

?>