<?php

/**
 * @copyright Copyright (c) 2002-2014 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package albums
 * @subpackage db
 * @version 3.6.0
 * @since 2.5.0
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

/** */
require_once ('webcore/db/folder_entry_query.php');
require_once ('albums/db/album_query_toolkit.php');

/**
 * Retrieves {@link PICTURE}s or {@link JOURNAL}s for an {@link ALBUM}.
 * @package albums
 * @subpackage db
 * @version 3.6.0
 * @since 2.5.0
 */
class ALBUM_ENTRY_QUERY extends FOLDER_MULTI_ENTRY_QUERY
{
  /**
   * Apply default restrictions and tables.
   */
  public function apply_defaults () 
  {
    parent::apply_defaults ();
    $this->set_day_field ('entry.date');
    $this->order_by_recent ();
  }

  /**
   * Specify which type of entries to retrieve.
   * If no type is specified, then assume all entries.
   * @param string $type 'job' and 'change' are valid here.
   */
  public function set_type ($type)
  {
    $this->apply_defaults ();
    album_query_set_type ($this, $type);
  }
}

?>