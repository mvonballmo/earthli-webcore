<?php

/**
 * @copyright Copyright (c) 2002-2014 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package albums
 * @subpackage gui
 * @version 3.5.0
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
require_once ('albums/gui/album_entry_grid.php');

/**
 * Display {@link JOURNAL}s from a {@link QUERY}.
 * @package albums
 * @subpackage gui
 * @version 3.5.0
 * @since 2.5.0
 */
class JOURNAL_GRID extends ALBUM_ENTRY_GRID
{
  /**
   * @var boolean
   */
  public $show_user = true;

  /**
   * @var boolean
   */
  public $show_folder = false;

  /**
   * @param JOURNAL $obj
   * @access private
   */
  protected function _draw_box ($obj)
  {
    /** @var ALBUM $folder */
    $folder = $obj->parent_folder ();
    $creator = $obj->creator ();
?>
  <div class="grid-item">
    <?php
      $this->_display_start_minimal_commands_block($obj);
    ?>
      <div style="float: left; margin-right: 5px">
        <?php echo $obj->weather_icon (); ?>
      </div>
      <div style="margin-left: 35px">
        <h3>
          <?php
          if ($this->show_folder)
          {
            echo $folder->title_as_link () . $this->app->display_options->object_separator;
          }
          echo $this->obj_link ($obj);
          ?>
        </h3>
        <p class="date-time">
          <?php echo $folder->format_date ($obj->date); ?>
        </p>
        <div class="text-flow">
          <?php
          $munger = $obj->html_formatter ();
          $munger->max_visible_output_chars = 250;
          echo $obj->description_as_html ($munger);
          ?>
          <p class="info-box-bottom">
            <?php if ($this->show_user) { echo $creator->title_as_link (); ?> - <?php } ?>
            <?php echo $obj->time_created->format (); ?>
          </p>
        </div>
      </div>
    <?php
    $this->_display_finish_minimal_commands_block();
    ?>
    </div>
<?php
  }
}


/**
 * Display {@link JOURNAL}s from a {@link QUERY}.
 * @package albums
 * @subpackage gui
 * @version 3.5.0
 * @since 2.5.0
 */
class JOURNAL_SUMMARY_GRID extends ENTRY_SUMMARY_GRID
{
  /**
   * Show search details for an object.
   * @param JOURNAL $obj
   * @access private
   */
  protected function _echo_details ($obj)
  {
    parent::_echo_details ($obj);
?>
    <tr>
      <th>Temperature:</th>
      <td><?php echo $obj->temperature_as_html (); ?></td>
    </tr>
    <tr>
      <th>Weather:</th>
      <td><?php echo $obj->weather_icon (); ?></td>
    </tr>
<?php
  }

  /**
   * Return the block of text to summarize.
   * @param JOURNAL $obj
   * @return string
   * @access private
   */
  protected function _text_to_summarize ($obj)
  {
    return $obj->description . ' ' . $obj->weather;
  }  
}

?>