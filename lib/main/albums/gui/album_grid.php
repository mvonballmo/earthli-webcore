<?php

/**
 * @copyright Copyright (c) 2002-2010 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package albums
 * @subpackage gui
 * @version 3.3.0
 * @since 2.5.0
 */

/****************************************************************************

Copyright (c) 2002-2010 Marco Von Ballmoos

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
require_once ('webcore/gui/folder_grid.php');

/**
 * Display {@link ALBUM}s from a {@link QUERY}.
 * @package albums
 * @subpackage gui
 * @version 3.3.0
 * @since 2.5.0
 */
class ALBUM_GRID extends FOLDER_GRID
{
  /**
   * @var boolean
   */
  public $show_separator = true;

  /**
   * @var string
   */
  public $object_name = 'Album';

  /**
   * @var string
   */
  public $box_style = '';

  /**
   * @var integer
   */
  public $spacing = 0;

  /**
   * @param ALBUM $obj
   * @access private
   */
  protected function _draw_box ($obj)
  {
    $main_pic = $obj->main_picture ();
?>
    <?php
    if ($main_pic)
    {
      $f = $main_pic->date->formatter ();
      $f->show_CSS = false;
      $pic_title = $main_pic->title_as_plain_text () . " (" . $obj->format_date ($main_pic->date, $f) . ")";
      ?>
      <div style="position: relative">
        <p>
        <a href="view_folder.php?<?php echo "id=$obj->id"; ?>"><img src="<?php echo $main_pic->full_thumbnail_name (); ?>" title="<?php echo $pic_title; ?>" alt="<?php echo $pic_title; ?>"></a>
        </p>
      <div style="position: absolute; left: 0; top: 0">
        <?php
        $this->_draw_menu_for ($obj, Menu_size_minimal, Menu_align_inline);
        ?>
      </div>
    <?php
    }
    else
    {
      ?>
      <div style="float: left; padding-right: 15px; padding-top: .5em">
        <?php
        $this->_draw_menu_for ($obj, Menu_size_minimal, Menu_align_inline);
        ?>
      </div>
      <p>
        <a href="view_folder.php?<?php echo "id=$obj->id"; ?>"><?php echo $obj->title; ?></a>
      </p>
    <?php
    }
    ?>
    </div>
  <h3>
    <?php echo $obj->title_as_html (); ?>
  </h3>
  <p class="detail">
  <?php
    if ($obj->is_multi_day ())
    {
      echo $obj->format_date ($obj->first_day) . ' - ' . $obj->format_date ($obj->last_day);
    }
    else
    {
      echo $obj->format_date ($obj->first_day);
    }
  ?>
  </p>
  <div style="margin-right: 20%"><?php echo $obj->summary_as_html (); ?></div>
<?php
  }
}

?>