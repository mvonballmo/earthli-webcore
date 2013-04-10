<?php

/**
 * @copyright Copyright (c) 2002-2013 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package albums
 * @subpackage obj
 * @version 3.4.0
 * @since 2.5.0
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
require_once ('webcore/obj/webcore_history_items.php');

/**
 * Manages the audit trail of a {@link PICTURE}.
 * @package albums
 * @subpackage obj
 * @version 3.4.0
 * @since 2.5.0
 * @access private
 */
class PICTURE_HISTORY_ITEM extends ENTRY_HISTORY_ITEM
{
  /**
   * Record class-specific differences.
   * 'orig' is the same as {@link $_object}, but is passed here for convenience.
   * @param PICTURE $orig
   * @param PICTURE $new
   * @access private
   */
  protected function _record_differences ($orig, $new)
  {
    parent::_record_differences ($orig, $new);

    $this->_record_time_difference ('Date', $orig->date, $new->date);
    $this->_record_string_difference ('File name', $orig->file_name, $new->file_name);
  }
}

/**
 * Manages the audit trail of a {@link JOURNAL}.
 * @package albums
 * @subpackage obj
 * @version 3.4.0
 * @since 2.5.0
 * @access private
 */
class JOURNAL_HISTORY_ITEM extends ENTRY_HISTORY_ITEM
{
  /**
   * Record class-specific differences.
   * 'orig' is the same as {@link $_object}, but is passed here for convenience.
   * @param JOURNAL $orig
   * @param JOURNAL $new
   * @access private
   */
  protected function _record_differences ($orig, $new)
  {
    parent::_record_differences ($orig, $new);

    $this->_record_time_difference ('Date', $orig->date, $new->date);

    if ($orig->weather_type != $new->weather_type)
    {
      $orig_weather = $orig->weather_icon_properties ();
      $new_weather = $new->weather_icon_properties ();
      $this->_record_string_difference ('Weather type', $orig_weather->title, $new_weather->title);
    }

    if ($orig->hi_temp != $new->hi_temp)
    {
      $this->_record_string_difference ('Low temperature', $orig->lo_temp, $new->lo_temp);
    }

    if ($orig->lo_temp != $new->lo_temp)
    {
      $this->_record_string_difference ('High temperature', $orig->hi_temp, $new->hi_temp);
    }

    $this->_record_text_difference ('Weather', $orig->weather, $new->weather);
  }
}

/**
 * Manages the audit trail of a {@link ALBUM}.
 * @package albums
 * @subpackage obj
 * @version 3.4.0
 * @since 2.5.0
 * @access private
 */
class ALBUM_HISTORY_ITEM extends FOLDER_HISTORY_ITEM
{
  /**
   * Record class-specific differences.
   * 'orig' is the same as {@link $_object}, but is passed here for convenience.
   * @param ALBUM $orig
   * @param ALBUM $new
   * @access private
   */
  protected function _record_differences ($orig, $new)
  {
    parent::_record_differences ($orig, $new);

    $this->_record_object_difference ('Main picture', $orig->main_picture (), $new->main_picture () );

    $this->_record_string_difference ('URL Root', $orig->url_root, $new->url_root);
    $this->_record_string_difference ('Location', $orig->location, $new->location);

    $this->_record_time_difference ('First day', $orig->first_day, $new->first_day);
    $this->_record_time_difference ('Last day', $orig->last_day, $new->last_day);

    if ($orig->show_times != $new->show_times)
    {
      if ($new->show_times)
      {
        $this->record_difference ('Show times turned on.');
      }
      else
      {
        $this->record_difference ('Show times turned off.');
      }
    }

    if ($orig->show_celsius != $new->show_celsius)
    {
      if ($new->show_celsius)
      {
        $this->record_difference ('Show temperatures in Celsius.');
      }
      else
      {
        $this->record_difference ('Show temperatures in Fahrenheit.');
      }
    }

    if ($orig->first_day_mode != $new->first_day_mode)
    {
      $orig_title = $this->_day_mode_as_text ($orig->first_day_mode);
      $new_title = $this->_day_mode_as_text ($new->first_day_mode);
      $this->_record_string_difference ('First day mode', $orig_title, $new_title);
    }

    if ($orig->last_day_mode != $new->last_day_mode)
    {
      $orig_title = $this->_day_mode_as_text ($orig->last_day_mode);
      $new_title = $this->_day_mode_as_text ($new->last_day_mode);
      $this->_record_string_difference ('Last day mode', $orig_title, $new_title);
    }

    if ($orig->max_picture_width != $new->max_picture_width)
    {
      $this->_record_string_difference ('Maximum picture width', $orig->max_picture_width, $new->max_picture_width);
    }

    if ($orig->max_picture_height != $new->max_picture_height)
    {
      $this->_record_string_difference ('Maximum picture height', $orig->max_picture_width, $new->max_picture_width);
    }
  }

  /**
   * Return the album's day mode as a string.
   * @param integer $day_mode Can be {@link Day_mode_fixed}, {@link Day_mode_today} or {@link Day_mode_adjust}.
   * @return string
   * @access private
   */
  protected function _day_mode_as_text ($day_mode)
  {
    switch ($day_mode)
    {
    case Day_mode_fixed:
      return 'Fixed';
    case Day_mode_adjust:
      return 'Adjusted';
    case Day_mode_today:
      return 'Today';
    default:
      return '';
    }
  }
}

?>