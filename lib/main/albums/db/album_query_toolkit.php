<?php

/**
 * @copyright Copyright (c) 2002-2014 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package albums
 * @subpackage db
 * @version 3.4.0
 * @since 2.9.1
 */

/****************************************************************************

Copyright (c) 2002-2014 Marco Von Ballmoos

This file is part of earthli Albums.

earthli Albums is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

earthli Albums is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with earthli Albums; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

For more information about the earthli Albums, visit:

http://www.earthli.com/software/webcore/albums

****************************************************************************/


/**
 * Sets up a query for a particular entry type.
 * @param QUERY $query
 * @param string $type
 * @access private
 */
function album_query_set_type ($query, $type)
{
  $table_names = $query->app->table_names;

  switch ($type)
  {
  case 'picture':
    $query->add_select ('pic.*');
    $query->add_table ($table_names->pictures . ' pic', 'pic.entry_id = entry.id');
    break;
  case 'journal':
    $query->add_select ('jrnl.*');
    $query->add_table ($table_names->journals . ' jrnl', 'jrnl.entry_id = entry.id');
    break;
  default:
    $query->add_select ('jrnl.*, pic.*');
    $query->add_table ($table_names->journals . ' jrnl', 'jrnl.entry_id = entry.id', 'LEFT');
    $query->add_table ($table_names->pictures . ' pic', 'pic.entry_id = entry.id', 'LEFT');
    break;
  }
  $query->order_by_day ('ASC');
}

?>