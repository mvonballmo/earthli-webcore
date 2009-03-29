<?php

/**
 * @copyright Copyright (c) 2002-2008 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage grid
 * @version 3.0.0
 * @since 2.7.0
 */

/****************************************************************************

Copyright (c) 2002-2008 Marco Von Ballmoos

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
require_once ('webcore/gui/content_object_grid.php');

/**
 * Display a short summary for {@link OBJECT_IN_FOLDER}s.
 * Used to display the results of a {@link SEARCH} and subscription lists.
 * @package webcore
 * @subpackage gui
 * @version 3.0.0
 * @since 2.7.0
 */
class OBJECT_IN_FOLDER_SUMMARY_GRID extends CONTENT_OBJECT_GRID
{
  /**
   * @var string
   */
  public $box_style = 'object-in-list';
  /**
   * @var boolean
   */
  public $show_separator = FALSE;
  /**
   * @var integer
   */
  public $width = '65%';
  /**
   * Show check-box selectors next to items?
   * @var boolean
   */
  public $items_are_selectable = TRUE;

  /**
   * @param OBJECT_IN_FOLDER $obj
   * @access private
   */
  function _draw_box ($obj)
  {
    $this->_draw_menu_for ($obj, Menu_size_minimal);
?>
  <div class="grid-title">
  <?php
    echo $this->obj_link ($obj);
  ?>
  </div>
  <div class="info-box-top">
  <?php
    $this->_echo_header ($obj);
  ?>
  </div>
<?php
    $this->_echo_text_summary ($obj);
  }
  
  /**
   * Return the size of the object.
   * @param OBJECT_IN_FOLDER $obj
   * @return integer
   * @access private
   */
  function _size_of ($obj)
  {
    return strlen ($obj->description);
  }
  
  /**
   * Show all details for an object.
   * @param OBJECT_IN_FOLDER $obj
   * @access private
   */
  function _echo_header ($obj)
  {
?>
  <table cellpadding="1" cellspacing="0">
    <?php $this->_echo_details ($obj); ?>
    <tr>
      <td class="label">Location:</td>
      <td>
        <?php $this->_echo_folders ($obj); ?>
      </td>
    </tr>
    <tr>
      <td class="label">Size:</td>
      <td>
      <?php echo file_size_as_text ($this->_size_of ($obj)); ?>
      </td>
    </tr>
  </table>
<?php
  }
  
  /**
   * Show search details for an object.
   * @param OBJECT_IN_FOLDER $obj
   * @access private
   */
  function _echo_details ($obj)
  {
    $this->_echo_user_information ('Created by', $obj->creator (), $obj->time_created );
  }
  
  /**
   * Show set of user details.
   * @param string $title
   * @param USER $user
   * @param DATE_TIME $date
   * @access private
   */
  function _echo_user_information ($title, $user, $date)
  {
?>
    <tr>
      <td class="label"><?php echo $title; ?>:</td>
      <td><?php echo $user->title_as_link (); ?></td>
    </tr>
    <tr>
      <td class="label">Date:</td>
      <td><?php echo $date->format (); ?></td>
    </tr>
<?php
  }

  /**
   * Show parent folders in outline form. 
   * @param OBJECT_IN_FOLDER $obj
   * @access private
   */
  function _echo_folders ($obj)
  {
    $folder = $obj->parent_folder ();
    $depth = 0;
    while ($folder->id && ! $folder->is_root ())
    {
      if ($depth)
      {
        echo str_repeat ('&nbsp;', ($depth - 1) * 2);
        echo $this->app->display_options->object_separator;
      }
      if ($folder->icon_url)
      {
        echo $folder->icon_as_html ('16px') . ' ';
      }
      echo $folder->title_as_link ();
      echo '<br>';
      $folder = $folder->parent_folder ();
      $depth++;
    }
  }
}

?>