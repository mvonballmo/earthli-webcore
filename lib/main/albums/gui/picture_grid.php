<?php

/**
 * @copyright Copyright (c) 2002-2009 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package albums
 * @subpackage gui
 * @version 3.1.0
 * @since 2.5.0
 */

/****************************************************************************

Copyright (c) 2002-2009 Marco Von Ballmoos

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
 * Display {@link PICTURE}s from a {@link QUERY}.
 * @package albums
 * @subpackage gui
 * @version 3.1.0
 * @since 2.5.0
 */
class PICTURE_GRID extends ALBUM_ENTRY_GRID
{
  /**
   * @var string
   */
  public $box_style = 'chart';

  /**
   * @var string
   */
  public $object_name = 'Picture';

  /**
   * @var integer
   */
  public $spacing = 8;

  /**
   * @var integer
   */
  public $padding = 0;

  /**
   * @var integer
   */
  public $description_length = 100;

  /**
   * @var boolean
   */
  public $show_separator = false;

  /**
   * @var boolean
   */
  public $show_user = false;

  /**
   * @var boolean
   */
  public $show_folder = false;

  /**
   * @var boolean
   */
  public $show_controls = true;

  /**
   * @var boolean
   */
  public $show_date = true;

  /**
   * @param PICTURE $obj
   * @access private
   */
  protected function _draw_box ($obj)
  {
    $folder = $obj->parent_folder ();
?>
  <div class="chart-title" style="text-align: center">
  <?php
    if ($this->show_folder)
    {
      echo $folder->title_as_link () . $this->app->display_options->object_separator;
    }
    echo $this->obj_link ($obj);
  ?>
  </div>
  <div class="chart-body" style="text-align: center">
    <?php
      if ($this->show_controls)
      {
        $this->_draw_menu_for ($obj, Menu_size_minimal, Menu_align_right);
      }
      $this->_url->replace_argument ('id', $obj->id);
    ?>
    <p style="clear: both">
      <a href="<?php echo $this->_url->as_html (); ?>"><img class="frame" src="<?php echo $obj->full_thumbnail_name (); ?>" alt="Picture"></a>
    </p>
    <?php
      if ($this->show_date)
      {
    ?>
    <p class="detail"><?php echo $folder->format_date ($obj->date); ?></p>
    <?php
      }

    ?>
    <div style="text-align: justify">
    <?php
      $munger = $obj->html_formatter ();
      $obj->max_visible_output_chars = $this->description_length;
      echo $obj->description_as_html ($munger);
    ?>
    </div>
  </div>
<?php
  }
}

/**
 * Display {@link PICTURE}s from a {@link QUERY}.
 * @package albums
 * @subpackage gui
 * @version 3.1.0
 * @since 2.5.0
 */
class PICTURE_SUMMARY_GRID extends ENTRY_SUMMARY_GRID
{
  /**
   * @var string
   */
  public $object_name = 'Picture';

  /**
   * @var integer
   */
  public $width = '75%';

  /**
   * Return the size of the object.
   * @param OBJECT_IN_FOLDER $obj
   * @return integer
   * @access private
   */
  protected function _size_of ($obj)
  {
    return @filesize (url_to_file_name ($obj->full_file_name (true)));
  }

  /**
   * @param PICTURE $obj
   * @access private
   */
  protected function _echo_header ($obj)
  {
    $box = $this->app->make_box_renderer ();
    $box->start_column_set ();
    $box->new_column ('padding-right: 2em');
    parent::_echo_header ($obj);
    $box->new_column ();
?>
    <a href="<?php echo $obj->home_page (); ?>"><img class="frame" src="<?php echo $obj->full_thumbnail_name (); ?>" alt="Picture"></a>
<?php
    $box->finish_column_set ();
  }
}

?>
