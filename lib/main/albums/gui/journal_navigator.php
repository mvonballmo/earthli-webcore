<?php

/**
 * @copyright Copyright (c) 2002-2013 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package albums
 * @subpackage gui
 * @version 3.3.0
 * @since 2.9.0
 * @access private
 */

/****************************************************************************

Copyright (c) 2002-2013 Marco Von Ballmoos

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
require_once ('webcore/gui/entry_navigator.php');

/**
 * Display a list of {@link JOURNAL}s 'around' the current one.
 * @package albums
 * @subpackage gui
 * @version 3.3.0
 * @since 2.9.0
 */
class JOURNAL_NAVIGATOR extends MULTI_TYPE_ENTRY_NAVIGATOR
{
  /**
   * Modify the query to navigate.
   * @param QUERY $query
   * @access private
   */
  protected function _adjust_query ($query)
  {
    $calendar = read_var ('calendar');
    $first_day = read_var ('first_day');
    $last_day = read_var ('last_day');
    $folder = $this->_entry->parent_folder ();

    if ($calendar)
    {
      $this->page->location->append ("Calendar", "view_calendar.php?id=$folder->id");
    }

    if ($first_day)
    {
      $day = $this->app->make_date_time ($first_day, Date_time_iso);
      $url = new URL ($this->env->url (Url_part_no_host_path));
      $url->replace_argument ('id', $folder->id);
      $url->replace_name_and_extension ('view_journals.php');
      $this->page->location->append ($folder->format_date ($day), $url->as_text ());

      $query->set_days ($first_day, $last_day);
    }

    $query->set_order ('date ASC');

    parent::_adjust_query ($query);
  }
}

?>