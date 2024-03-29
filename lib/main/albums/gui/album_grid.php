<?php

/**
 * @copyright Copyright (c) 2002-2014 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package albums
 * @subpackage gui
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
require_once('webcore/gui/folder_grid.php');

/**
 * Display {@link ALBUM}s from a {@link QUERY}.
 * @package albums
 * @subpackage gui
 * @version 3.6.0
 * @since 2.5.0
 */
class ALBUM_GRID extends FOLDER_GRID
{
  /**
   * @param CONTEXT $context Context to which this grid belongs.
   */
  public function __construct($context)
  {
    parent::__construct($context);

    $this->css_class .= ' small-tiles';
  }

  /**
   * @param ALBUM $obj
   * @access private
   */
  protected function _draw_box($obj)
  {
    $main_pic = $obj->main_picture();
    if ($main_pic)
    {
      $f = $main_pic->date->formatter();
      $f->show_CSS = false;
      $pic_title = $main_pic->title_as_plain_text() . " (" . $obj->format_date($main_pic->date, $f) . ")";
      ?>
      <h3>
        <?php echo $obj->title_as_link(); ?>
      </h3>
      <?php
      $this->_display_start_overlay_commands($obj);
      ?>
      <div>
        <a href="view_folder.php?<?php echo "id=$obj->id"; ?>"><img
            src="<?php echo $main_pic->full_thumbnail_name(); ?>" title="<?php echo $pic_title; ?>"
            alt="<?php echo $pic_title; ?>"></a>
      </div>
    <?php
      $this->_display_finish_overlay_commands();
    }
    else
    {
      $this->_display_start_minimal_commands_block($obj);
      ?>
      <h3>
        <?php echo $obj->title_as_link(); ?>
      </h3>
    <?php
      $this->_display_finish_minimal_commands_block();
    }
    ?>
    <p class="detail">
      <?php
      if ($obj->is_multi_day())
      {
        echo $obj->format_date($obj->first_day) . ' - ' . $obj->format_date($obj->last_day);
      }
      else
      {
        echo $obj->format_date($obj->first_day);
      }
      ?>
    </p>
    <div class="text-flow multi-column-grid-description">
      <?php echo $obj->summary_as_html(); ?>
    </div>
  <?php
  }
}

?>