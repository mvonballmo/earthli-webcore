<?php

/**
 * @copyright Copyright (c) 2002-2014 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package albums
 * @subpackage obj
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
require_once ('albums/obj/album_entry.php');

/**
 * The weather and description from a {@link JOURNAL}.
 * @package albums
 * @subpackage obj
 * @version 3.6.0
 * @since 2.5.0
 */
class JOURNAL extends ALBUM_ENTRY
{
  /**
   * High temperature for the day (Celsius).
   * @var integer
   */
  public $hi_temp;

  /**
   * Low temperature for the day (Celsius).
   * @var integer
   */
  public $lo_temp;

  /**
   * Brief description of the weather.
   * @var string
   */
  public $weather;

  /**
   * Index into a list of deployment-dependent weather types.
   * @var string
   */
  public $weather_type;

  /**
   * All properties of this entry's kind.
   * These are the properties defined in the user data file.
   */
  public function weather_icon_properties ()
  {
    $props = $this->app->display_options->weather_icons ();
    if (isset ($props [$this->weather_type - 1]))
    {
      return $props [$this->weather_type - 1];
    }
    else
    {
      $prop = new PROPERTY_VALUE ($this->app);
      $prop->title = "[Unknown weather ($this->weather_type)]";
      return $prop;
    }
  }

  /**
   * High and low temperatures formatted as HTML.
   * @return string
   */
  public function temperature_as_html ()
  {
    /** @var ALBUM $folder */
    $folder = $this->parent_folder ();
    $lo = $folder->temperature_as_html ($this->lo_temp);
    $hi = $folder->temperature_as_html ($this->hi_temp);
    if ($lo == $hi)
    {
      return '<span class="field">' . $hi . '</span>';
    }

    return '<span class="field">' . $lo . '</span> to <span class="field">' . $hi . '</span>';
  }

  /**
   * High and low temperatures formatted as plain text.
   * @return string
   */
  public function temperature_as_text ()
  {
    if ($this->lo_temp == $this->hi_temp)
    {
      return "{$this->hi_temp}�C";
    }

    return "{$this->lo_temp}�C to {$this->hi_temp}�C";
  }

  /**
   * Weather transformed into HTML.
   * If no specific munger is provided, the one from {@link html_formatter()} is used.
   * @param HTML_MUNGER $munger
   * @return string
   */
  public function weather_as_html ($munger = null)
  {
    if (! isset ($munger))
    {
      $munger = $this->html_formatter ();
      $munger->force_paragraphs = false;
    }

    return $this->_text_as_html ($this->weather, $munger);
  }

  /**
   * Weather transformed into formatted plain text.
   * If no specific munger is provided, the one from {@link plain_text_formatter()} is used.
   * @param PLAIN_TEXT_MUNGER $munger
   * @return string
   */
  public function weather_as_plain_text ($munger = null)
  {
    return $this->_text_as_plain_text ($this->weather, $munger);
  }

  /**
   * Restrict the query to this journal's day only.
   * @param ALBUM_ENTRY_QUERY
   */
  public function adjust_query ($query)
  {
    /* Copy instead of taking a reference. */

    $first_day = clone($this->date);
    $first_day->set_time_from_iso ('00:00:00');

    $last_day = clone($this->date);
    $last_day->set_time_from_iso ('23:59:59');

    $query->set_days ($first_day->as_iso (), $last_day->as_iso ());
  }

  /**
   * Query for all of the pictures on the same day as this journal.
   * @return ALBUM_ENTRY_QUERY
   */
  public function picture_query ()
  {
    $folder = $this->parent_folder ();
    /** @var ALBUM_ENTRY_QUERY $Result */
    $Result = $folder->entry_query ();
    $Result->set_type ('picture');
    $this->adjust_query ($Result);
    return $Result;
  }

  public function set_social_options(PAGE_SOCIAL_OPTIONS $social_options)
  {
    parent::set_social_options ($social_options);

    $picture_query = $this->picture_query ();
    $pictures = $picture_query->objects ();

    if (count($pictures) > 0)
    {
      /** @var PICTURE $key_photo_for_day */
      $key_photo_for_day = $pictures[0];
    }

    foreach ($pictures as $p)
    {
      if ($p->is_key_photo_for_day)
      {
        $key_photo_for_day = $p;

        break;
      }
    }

    if (isset($key_photo_for_day))
    {
      $metrics = $key_photo_for_day->metrics (false);

      $social_options->image = $key_photo_for_day->full_file_name (true);
      $social_options->image_width = $metrics->original_width;
      $social_options->image_height = $metrics->original_height;
    }
  }


  /**
   * @param DATABASE $db
   */
  public function load ($db)
  {
    parent::load ($db);
    $this->lo_temp = $db->f ("lo_temp");
    $this->hi_temp = $db->f ("hi_temp");
    $this->weather = $db->f ("weather");
    $this->weather_type = $db->f ("weather_type");
  }

  /**
   * @param SQL_STORAGE $storage Store values to this object.
   */
  public function store_to ($storage)
  {
    parent::store_to ($storage);
    $table_name =$this->secondary_table_name ();
    $storage->add ($table_name, 'weather', Field_type_string, $this->weather);
    $storage->add ($table_name, 'lo_temp', Field_type_integer, $this->lo_temp);
    $storage->add ($table_name, 'hi_temp', Field_type_integer, $this->hi_temp);
    $storage->add ($table_name, 'weather_type', Field_type_integer, $this->weather_type);
  }

  /**
   * Name of the home page name for this object.
   * @return string
   */
  public function page_name ()
  {
    return $this->app->page_names->journal_home;
  }

  /**
   * Name of this object's secondary database table.
   * @return string
   * @access private
   */
  public function secondary_table_name ()
  {
    return $this->app->table_names->journals;
  }

  /**
   * Return default handler objects for supported tasks.
   * @param string $handler_type Specific functionality required.
   * @param object $options
   * @return object
   * @access private
   */
  protected function _default_handler_for ($handler_type, $options = null)
  {
    switch ($handler_type)
    {
      case Handler_navigator:
        include_once ('albums/gui/journal_navigator.php');
        return new JOURNAL_NAVIGATOR ($this);
      case Handler_print_renderer:
      case Handler_html_renderer:
      case Handler_text_renderer:
        include_once ('albums/gui/journal_renderer.php');
        return new JOURNAL_RENDERER ($this->app, $options);
      case Handler_commands:
        include_once ('albums/cmd/journal_commands.php');
        return new JOURNAL_COMMANDS ($this);
      case Handler_history_item:
        include_once ('albums/obj/album_history_items.php');
        return new JOURNAL_HISTORY_ITEM ($this->app);
      case Handler_associated_data:
        include_once ('albums/gui/journal_renderer.php');
        return new JOURNAL_ASSOCIATED_DATA_RENDERER ($this->app, $options);
      case Handler_location:
        include_once ('albums/gui/journal_renderer.php');
        return new JOURNAL_LOCATION_RENDERER ($this->context);
      default:
        return parent::_default_handler_for ($handler_type, $options);
    }
  }

  /**
   * Name of this type of album entry.
   * @var string
   * @access private
   */
  public $type = 'journal';
}