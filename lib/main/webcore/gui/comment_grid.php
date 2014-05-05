<?php

/**
 * @copyright Copyright (c) 2002-2014 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage grid
 * @version 3.5.0
 * @since 2.2.1
 */

/****************************************************************************

Copyright (c) 2002-2014 Marco Von Ballmoos

This file is part of earthli WebCore.

earthli WebCore is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

earthli WebCore is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with earthli WebCore; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

For more information about the earthli WebCore, visit:

http://www.earthli.com/software/webcore

****************************************************************************/

/** */
require_once ('webcore/gui/object_in_folder_grid.php');

/**
 * Displays {@link COMMENT}s from a {@link QUERY}.
 * Shows associated folder, entry and creator information.
 * @package webcore
 * @subpackage grid
 * @version 3.5.0
 * @since 2.2.1
 */
class COMMENT_GRID extends SELECTABLE_GRID
{
  /**
   * @var boolean
   */
  public $show_user = true;

  /**
   * @var boolean
   */
  public $show_folder = true;

  /**
   * @param COMMENT $obj
   * @access private
   */
  protected function _draw_box ($obj)
  {
    $this->_echo_header ($obj);
?>
  <div class="text-flow">
  <?php
    echo $obj->description_as_html ();
  ?>
  </div>
<?php
    $this->_echo_entry_info ($obj);
  }

  /**
   * @param COMMENT $obj
   */
  protected function _echo_entry_info ($obj)
  {
    $folder = $obj->parent_folder ();
    $entry = $obj->entry ();
    $type_info = $entry->type_info ();
?>
  <div class="detail">
  (Attached to <?php
    echo $type_info->singular_title . ' ';
    echo $entry->title_as_link ();
    if ($this->show_folder)
    {
      echo ' in ' . $folder->title_as_link ();
    }
  ?>)
  </div>
<?php
  }

  /**
   * @param COMMENT $obj
   */
  protected function _echo_header ($obj)
  {
    ?>
    <div class="grid-item">
    <?php
      $this->_display_start_minimal_commands_block($obj);
      ?>
        <h3><?php echo $obj->title_as_link (); ?></h3>
      <?php
        $props = $obj->icon_properties ();
        $this->context->start_icon_container($props->icon, Fifteen_px);
        $creator = $obj->creator ();
        if ($creator->icon_url)
        {
          $this->context->start_icon_container($creator->icon_url, Sixteen_px);
        }
      ?>
        <div class="info-box-top">
          <p>
          <?php echo $creator->title_as_link (); ?> &ndash; <?php echo $obj->time_created->format ();

          if ($obj->modified ())
          {
            $modifier = $obj->modifier ();
            ?>
            (updated by <?php echo $modifier->title_as_link (); ?> &ndash; <?php echo $obj->time_modified->format (); ?>)
          <?php
          }
          ?>
          </p>
        </div>
      <?php
        if ($creator->icon_url)
        {
          $this->context->finish_icon_container();
        }
        $this->context->finish_icon_container();
      ?>
      </div>
    </div>
  <?php
  }
}

/**
 * Displays {@link COMMENTS}s for a {@link SEARCH}.
 * @package webcore
 * @subpackage grid
 * @version 3.5.0
 * @since 2.7.0
 */
class SELECT_COMMENT_GRID extends OBJECT_IN_FOLDER_SUMMARY_GRID
{
}

?>