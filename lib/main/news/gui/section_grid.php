<?php

/**
 * @copyright Copyright (c) 2002-2009 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package news
 * @subpackage gui
 * @version 3.1.0
 * @since 2.4.0
 */

/****************************************************************************

Copyright (c) 2002-2009 Marco Von Ballmoos

This file is part of earthli News.

earthli News is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

earthli News is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with earthli News; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

For more information about the earthli News, visit:

http://earthli.com/software/webcore/app_news.php

****************************************************************************/

/** */
require_once ('webcore/gui/folder_grid.php');

/**
 * Display {@link SECTION}s from a {@link QUERY}.
 * @package news
 * @subpackage gui
 * @version 3.1.0
 * @since 2.4.0
 */
class SECTION_GRID extends FOLDER_GRID
{
  /**
   * @var string
   */
  public $object_name = 'section';

  /**
   * @var string
   */
  public $box_style = 'object-in-list';

  /**
   * @var boolean
   */
  public $show_separator = false;

  /**
   * @param SECTION $obj
   * @access private
   */
  protected function _draw_box ($obj)
  {
    $t = $obj->title_formatter ();
    $t->max_visible_output_chars = 0;
    echo $obj->title_as_link ($t);

    $entry_query = $obj->entry_query ();
    $entry_query->set_filter (Visible);
    $size = $entry_query->size ();
  ?>
    <div style="float: right">
      (<span class="field"><?php echo $size; ?></span> Articles)
    </div>
  <?php
    if ($size)
    {
      $entry_query->set_limits (0, 10);
      $entries = $entry_query->objects ();
      $count = sizeof ($entries);
      if ($count)
      {
  ?>
    <ul class="detail" style="padding: 0em; margin: 0px; margin-top: 1em; margin-left: 2em">
  <?php
        foreach ($entries as &$entry)
        {
          $t = $entry->title_formatter ();
          $f = $entry->time_created->formatter ();
          $f->type = Date_time_format_short_date;
          $f->show_local_time = false;
          $t->title = $entry->time_created->format ($f);
  ?>
      <li style="margin: 0px">
        <?php echo $entry->title_as_link ($t); ?>
      </li>
  <?php
        }

        if ($size > 10)
        {
          echo "<li style=\"margin-bottom: 0px\">[<a href=\"view_folder.php?id=$obj->id\">More</a>]</li>\n";
        }
    ?>
    </ul>
    <?php
      }
    }
  }
}

?>