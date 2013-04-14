<?php

/**
 * @copyright Copyright (c) 2002-2013 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package albums
 * @subpackage gui
 * @version 3.4.0
 * @since 2.7.0
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
require_once ('webcore/gui/folder_renderer.php');

/**
 * Render details for an {@link ALBUM}.
 * @package albums
 * @subpackage gui
 * @version 3.4.0
 * @since 2.5.0
 */
class ALBUM_RENDERER extends FOLDER_RENDERER
{
  /**
   * Outputs the object as HTML.
   * @param ALBUM $obj
   * @access private
   */
  protected function _display_as_html ($obj)
  {
    $main_pic = $obj->main_picture ();
    if (isset ($main_pic))
    {
      $f = $main_pic->date->formatter ();
      $f->set_type_and_clear_flags (Date_time_format_short_date);
      $pic_title = $this->context->text_options->convert_to_html_attribute ("$main_pic->title (" . $main_pic->date->format ($f) . ")");

    ?>
    <div>
      <p>
        <img src="<?php echo $main_pic->full_thumbnail_name (); ?>" alt="<?php echo $pic_title; ?>" title="<?php echo $pic_title; ?>">
      </p>
    </div>
    <?php
    }

    if (! $obj->is_root ())
    {
  ?>
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
  <?php
    }

    $this->_echo_html_descriptions ($obj);
    $this->_echo_html_user_information ($obj, 'info-box-bottom');
  }

  /**
   * Outputs the object as plain text.
   * @param ALBUM $obj
   * @access private
   */
  protected function _display_as_plain_text ($obj)
  {
    if (! $obj->is_organizational ())
    {
      $f = $obj->first_day->formatter ();
      $f->clear_flags ();
      echo $this->line ($obj->format_date ($obj->first_day, $f) . ' - ' . $obj->format_date ($obj->last_day, $f));
    }
    parent::_display_as_plain_text ($obj);
  }
}

?>