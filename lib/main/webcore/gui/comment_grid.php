<?php

/**
 * @copyright Copyright (c) 2002-2010 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage grid
 * @version 3.3.0
 * @since 2.2.1
 */

/****************************************************************************

Copyright (c) 2002-2010 Marco Von Ballmoos

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
 * @version 3.3.0
 * @since 2.2.1
 */
class COMMENT_GRID extends SELECTABLE_GRID
{
  /**
   * @var string
   */
  public $object_name = 'Comment';

  /**
   * @var integer
   */
  public $spacing = 0;

  /**
   * @var boolean
   */
  public $show_separator = true;

  /**
   * @var string
   */
  public $box_style = 'object-in-list';

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

  protected function _echo_header ($obj)
  {
    ?>
    <div class="grid-item">
      <div class="minimal-commands">
        <?php $this->_draw_menu_for ($obj, Menu_size_minimal, Menu_align_inline); ?>
      </div>
      <div class="minimal-commands-content">
        <?php

        $creator = $obj->creator ();
        if ($creator->icon_url)
        {
        ?>
        <div style="float: left">
          <?php echo $creator->icon_as_html (); ?>
        </div>
        <div style="margin-left: 40px">
        <?php
          }
        ?>
          <div style="margin-bottom: 5px">
            <?php echo $obj->icon () . ' ' . $obj->title_as_link (); ?>
          </div>
          <div class="detail">
            by <?php echo $creator->title_as_link (); ?> - <?php echo $obj->time_created->format ();

            if ($obj->modified ())
            {
              $modifier = $obj->modifier ();
              ?>
              (Updated by <?php echo $modifier->title_as_link (); ?> - <?php echo $obj->time_modified->format (); ?>)
            <?php
            }
            ?>
          </div>
        <?php
          if ($creator->icon_url)
          {
        ?>
        </div>
      </div>
    </div>
    <?php
    }
  }
}

/**
 * Displays {@link COMMENTS}s for a {@link SEARCH}.
 * @package webcore
 * @subpackage grid
 * @version 3.3.0
 * @since 2.7.0
 */
class SELECT_COMMENT_GRID extends OBJECT_IN_FOLDER_SUMMARY_GRID
{
}

?>