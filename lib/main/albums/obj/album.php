<?php

/**
 * @copyright Copyright (c) 2002-2014 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package albums
 * @subpackage obj
 * @version 3.4.0
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
require_once ('webcore/obj/folder.php');

/**
 * Album is a single day.
 * Constrains {@link ALBUM::$first_day} and {@link ALBUM::$last_day} to a single date,
 * assigning {@link Day_mode_fixed} to both {@link ALBUM::$first_day_mode} and 
 * {@link ALBUM::$last_day_mode}. New journals and pictures use that date by default.
 * @see ALBUM::set_date_style()
 */
define ('Album_is_single_day', 'single');
/**
 * Regular trip album with a start and end date.
 * Constrains {@link ALBUM::$first_day} and {@link ALBUM::$last_day} to different dates,
 * assigning {@link Day_mode_fixed} to both {@link ALBUM::$first_day_mode} and 
 * {@link ALBUM::$last_day_mode}. New journals and pictures use the album start date by
 * default. 
 * @see ALBUM::set_date_style()
 */
define ('Album_is_span', 'span');
/**
 * Fixed start date, open end date.
 * {@link ALBUM::$first_day} is assigned, using {@link Day_mode_fixed} for 
 * {@link ALBUM::$first_day_mode}. New journals and pictures use today's date by
 * default.
 * @see ALBUM::set_date_style()
 */
define ('Album_is_journal', 'journal');
/**
 * Both start date and end date are not fixed.
 * {@link ALBUM::$first_day} is assigned, using {@link Day_mode_fixed} for 
 * {@link ALBUM::$first_day_mode}. New journals and picture use today's date by
 * default.
 * @see ALBUM::set_date_style()
 */
define ('Album_is_adjusted', 'adjusted');

/**
 * Use a set date for the first or last day.
 * Used internally by the {@link ALBUM}.
 * @access private
 */
define ('Day_mode_fixed', 0);
/**
 * Adjust the first or last day to 'fit' all the picture or journal entries.
 * Used internally by the {@link ALBUM}.
 * @access private
 */
define ('Day_mode_adjust', 1);
/**
 * Make today the last day in this album.
 * Used internally by the {@link ALBUM}.
 * @access private
 */
define ('Day_mode_today', 2);

/**
 * Album resources (pictures) are on the local server. 
 * Local albums have more features, in that {@link PICTURE}s can be moved more
 * freely and support uploading and date extraction.
 */
define ('Album_location_type_local', 'local');
/**
 * Album resources (pictures) are on the remote server. 
 * Remote albums allow users to bind in data located in other repositories.
 */
define ('Album_location_type_remote', 'remote');

/**
 * Holds {@link PICTURE}s and {@link JOURNAL}s in a photo-album.
 * @package albums
 * @subpackage obj
 * @version 3.4.0
 * @since 2.5.0
 */
class ALBUM extends FOLDER
{
  /**
   * Id of the picture to use for the album.
   * Must be a picture in this album if not empty.
   * @var integer
   * @access private
   */
  public $main_picture_id = 0;

  /**
   * URL from which pictures are loaded.
   * @var string
   * @access private
   */
  public $url_root;

  /**
   * First day of this album.
   * @var DATE_TIME
   */
  public $first_day;

  /**
   * Last day of this album.
   * @var DATE_TIME
   */
  public $last_day;

  /**
   * Show times when displaying dates.
   * @var boolean
   */
  public $show_times;

  /**
   * Show temperatures in Celsius.
   * Shown in Fahrenheit if false.
   * @var boolean
   */
  public $show_celsius = 1;

  /**
   * Where are pictures stored?
   * This can be either {@link Album_location_type_remote} or {@link
   * Album_location_type_local}.
   * @var boolean
   */
  public $location = Album_location_type_remote;

  /**
   * How is the first day maintained?
   * @see Day_mode_fixed
   * @var integer
   * @access private
   */
  public $first_day_mode = '';

  /**
   * How is the first day maintained?
   * @see Day_mode_fixed
   * @var integer
   * @access private
   */
  public $last_day_mode = '';

  /**
   * Maximum width of picture when displayed full-size.
   * @var integer
   */
  public $max_picture_width;

  /**
   * Maximum height of picture when displayed full-size.
   * @var integer
   */
  public $max_picture_height;

  /**
   * @param ALBUM_APPLICATION $app Main application.
   */
  public function __construct ($app)
  {
    parent::__construct ($app);

    include_once ('webcore/sys/date_time.php');
    $this->first_day = $app->make_date_time ();
    $this->last_day = $app->make_date_time ();
  }

  /**
   * Resolves all embedded folder aliases and returns a valid server URL.
   * @param boolean $root_override If set to {@link Force_root_on}, returns a fully
   * resolved URL.
   * @return string
   */
  public function expanded_url_root ($root_override = null)
  {
    return $this->app->resolve_path ($this->url_root, $root_override);
  }

  /**
   * URL of the base folder for pictures in this album.
   * @param boolean $root_override If set to {@link Force_root_on}, returns a fully
   * resolved URL.
   * @return string
   */
  public function picture_folder_url ($root_override = null)
  {
    $url = new URL ($this->expanded_url_root ($root_override));
    $url->append ('images');
    return $url->as_text ();
  }

  /**
   * The picture used to represent this album.
   * May be empty.
   * @return PICTURE
   */
  public function main_picture ()
  {
    $pic_query = $this->entry_query ();
    $pic_query->set_type ('picture');
    return $pic_query->object_at_id ($this->main_picture_id);
  }

  /**
   * Format a date using album options.
   * Pass in a formatter to customize (toggling local time generation).
   * @param DATE_TIME $d
   * @param DATE_TIME_FORMATTER $f
   * @return string
   * @access private
   */
  public function format_date ($d, $f = 0)
  {
    if (! $f)
    {
      $f = $d->formatter ();
    }

    if ($this->show_times)
    {
      $f->type = Date_time_format_date_and_time;
    }
    else
    {
      $f->type = Date_time_format_date_only;
    }

    $f->show_local_time = false;
    $f->show_time_zone = false;

    return $d->format ($f);
  }

  /**
   * Format a temperature as HTML.
   * Use the Fahrenheit/Celsius formatting option.
   * @param integer $temp
   * @return string
   * @access private
   */
  public function temperature_as_html ($temp)
  {
    if ($this->show_celsius)
    {
      return "$temp&deg;C";
    }
    else
    {
      $temp = round (($temp * 9) / 5) + 32;
      return "$temp&deg;F";
    }
  }
  
  /**
   * Is this a single date or a time span?
   * Renderers use this function to determine whether to show two dates or a single
   * date. If times are not displayed, an album with both {@link $first_day} and
   * {@link $last_day} on the same day will show up as a single date.
   * @return boolean
   */
  public function is_multi_day ()
  {
    $parts = Date_time_date_part;
    if ($this->show_times)
    {
      $parts = Date_time_both_parts;
    }
    return ! $this->first_day->equals ($this->last_day, $parts);
  }
  
  /**
   * Does the given date pass the day and day mode filters?
   * @param DATE_TIME $date
   * @see $ALBUM::$first_day_mode
   * @see $ALBUM::$last_day_mode
   */
  public function is_valid_date ($date)
  {
    if ($this->show_times)
    {
      $parts = Date_time_both_parts;
    }
    else
    {
      $parts = Date_time_date_part;
    }
    
    return ((($this->first_day_mode == Day_mode_adjust) || ($this->first_day->less_than_equal ($date, $parts)))
            &&
            ((($this->last_day_mode == Day_mode_fixed) && ($date->less_than_equal ($this->last_day, $parts)))
             || (($this->last_day_mode == Day_mode_today) && ($date->less_than_equal (new DATE_TIME (), $parts)))
             || ($this->last_day_mode == Day_mode_adjust)));
  }
  
  public function date_style ()
  {
    if ($this->first_day_mode == Day_mode_adjust)
    {
      return Album_is_adjusted;
    }
    
    if ($this->last_day_mode == Day_mode_today)
    {
      return Album_is_journal;
    }

    if ($this->first_day->equals ($this->last_day, Date_time_date_part))
    {
      return Album_is_single_day;
    }

    return Album_is_span;
  }

  /**
   * Change the way the album constrains dates.
   * @param string $style Can be one of {@link Album_is_single_day}, {@link Album_is_span}, {@link Album_is_journal} or {@link Album_is_adjusted}.
   * @param DATE_TIME $first_day
   * @param DATE_TIME $last_day */ 
  public function set_date_style ($style, $first_day, $last_day)
  {
    $this->first_day = $first_day;
    $this->last_day = $last_day;

    switch ($style)
    {
    case Album_is_single_day:
      $this->first_day_mode = Day_mode_fixed;
      $this->last_day_mode = Day_mode_fixed;
      $this->first_day->set_time_from_iso ('00:00:00');
      $this->last_day->set_time_from_iso ('23:59:59');
      break;
    case Album_is_span:
      $this->first_day_mode = Day_mode_fixed;
      $this->last_day_mode = Day_mode_fixed;
      $this->first_day->set_time_from_iso ('00:00:00');
      $this->last_day->set_time_from_iso ('23:59:59');
      break;
    case Album_is_journal:
      $this->first_day_mode = Day_mode_fixed;
      $this->last_day_mode = Day_mode_today;
      $this->first_day->set_time_from_iso ('00:00:00');
      $this->last_day->set_now ();
      break;
    case Album_is_adjusted:
      $this->first_day_mode = Day_mode_adjust;
      $this->last_day_mode = Day_mode_adjust;
      $this->refresh_dates ();
      break;
    }
  }

  /**
   * Change the way dates are maintained.
   * Pass in the two new day modes and indicate whether to update the database
   * immediately or not.
   * @param integer $first
   * @param integer $last
   * @access private
   */
  public function set_day_modes ($first, $last)
  {
    $old_first = $this->first_day_mode;
    $old_last = $this->last_day_mode;

    $this->first_day_mode = $first;
    $this->last_day_mode = $last;

    if (($first == Day_mode_adjust) || ($last == Day_mode_adjust))
    {
      if (($first != $old_first) || ($last != $old_last))
      {
        $this->refresh_dates ();
      }
    }
  }
  
  /**
   * Set the first and last days from content.
   * This function sets the {@link $first_day} and {@link $last_day} as
   * "tightly" as possible, encompassing all pictures and journals. Only has an
   * effect when the album has a {@link $first_day_mode} or {@link
   * $last_day_mode} of {@link Day_mode_adjust}. Use {@link include_entry()}
   * when adding a single picture or journal.
   * @param boolean $update_now Updates the database immediately when
   * <code>True</code>.
   */
  public function refresh_dates ($update_now = false)
  {
    $this->first_day->set_now ();
    $this->last_day->set_now ();

    if ($this->exists ())
    {
      if ($this->first_day_mode == Day_mode_adjust)
      {
        $this->db->logged_query ("SELECT date FROM {$this->app->table_names->entries} entry" .
                                 " WHERE entry.folder_id = $this->id ORDER BY entry.date ASC LIMIT 1");
        if ($this->db->next_record ())
        {
          $this->first_day->set_from_iso ($this->db->f ('date'));
        }
      }

      if ($this->last_day_mode == Day_mode_adjust)
      {
        $this->db->logged_query ("SELECT date FROM {$this->app->table_names->entries} entry" .
                                 " WHERE entry.folder_id = $this->id ORDER BY entry.date DESC LIMIT 1");
        if ($this->db->next_record ())
        {
          $this->last_day->set_from_iso ($this->db->f ('date'));
        }
      }
    }

    if ($update_now)
    {
      $this->db->logged_query ("UPDATE {$this->app->table_names->folders} SET first_day = '" . $this->first_day->as_iso () . "', last_day = '" . $this->last_day->as_iso () . "' WHERE id = $this->id");
    }
  }
  
  /**
   * Adjust first and last days to include the given entry.
   * Only has an effect when the album has a {@link $first_day_mode} or {@link
   * $last_day_mode} of {@link Day_mode_adjust}. Use {@link refresh_dates()} to
   * set the days from already contained content.
   * @param ALBUM_ENTRY $entry
   * @param boolean $update_now Updates the database immediately when
   * <code>True</code>.
   */
  public function include_entry ($entry, $update_now = true)
  {
    $first_day = $this->first_day->as_php ();
    $last_day = $this->last_day->as_php ();
    $day = $entry->date->as_php ();
    
    if ($this->first_day_mode == Day_mode_adjust)
    {
      if ($day < $first_day)
      {
        $this->first_day->set_from_php ($day);
        if ($update_now)
        {
          $this->db->logged_query ("UPDATE {$this->app->table_names->folders} SET first_day = '" . $this->first_day->as_iso () . "' WHERE id = $this->id");
        }
      }
    }

    if ($this->last_day_mode == Day_mode_adjust)
    {
      $need_update = ($day > $last_day);
      if (! $need_update)
      {
        $entry_query = $this->entry_query ();
        $need_update = $entry_query->size () == 0;
      }
      
      if ($need_update)
      {
        $this->last_day->set_from_php ($day);
        if ($update_now)
        {
          $this->db->logged_query ("UPDATE {$this->app->table_names->folders} SET last_day = '" . $this->last_day->as_iso () . "' WHERE id = $this->id");
        }
      }
    }
  }

  /**
   * Can pictures be uploaded for this albums?
   * Returns True is the logged-in user has upload rights and the album's {@link $location} is 'local'.
   * @return true
   */
  public function uploads_allowed ()
  {
    return $this->location == Album_location_type_local && $this->login->is_allowed (Privilege_set_entry, Privilege_upload, $this);
  }

  /**
   * Expand all folder aliases and return a usable URL.
   * Extends the aliases registered with {@link RESOURCE_MANAGER} with support
   * for {pic_thumb} and {pic_image}, which resolve to either the embedded
   * image or thumbnail for a {@link PICTURE} in the album. The identifier
   * following the alias can be either an ID or an image file name (e.g.
   * "{pic_thumb}/1304" or "{pic_thumb}/pic_file_name.jpg").
   * @param string $url
   * @param boolean $root_override Overrides {@link $resolve_to_root} if set to
   * {@link Force_root_on}.
   * @return string
   */
  public function resolve_url ($url, $root_override = null)
  {
    $thumb_key = '{pic_thumb}';
    $pic_key = '{pic_image}';

    if (strpos ($url, $thumb_key) !== false)
    {
      $key = $thumb_key;
    }
    else if (strpos ($url, $pic_key) !== false)
 {
   $key = $pic_key;
 }

    if (isset ($key))
    {
      $id = substr ($url, strlen ($key));
      if ($id && ($id [0] == '/'))
      {
        $id = substr ($id, 1);
      }

      /* Test if it can be an id. If yes, then look up the picture. If no, then assume
         it's a file name and include the id as a url */
      if (is_numeric ($id))
      {
        $pic_query = $this->entry_query ();
        $pic_query->set_type ('picture');
        $pic = $pic_query->object_at_id ($id);
        if (($key == $thumb_key))
        {
          $url = $pic->full_thumbnail_name ();
        }
        else
        {
          $url = $pic->full_file_name ();
        }
      }
      else
      {
        $url = new URL ($this->picture_folder_url ());
        $url->append ($id);
        if (($key == $thumb_key))
        {
          $url->append_to_name ('_tn');
        }
        $url = $url->as_text ();
      }
    }

    return parent::resolve_url ($url, $root_override);
  }

  /**
   * @param DATABASE $db
   */
  public function load ($db)
  {
    parent::load ($db);
    $this->location = $db->f ('location');
    $this->url_root = strtolower ($db->f ('url_root'));
    $this->main_picture_id = $db->f ('main_picture_id');
    $this->show_celsius = $db->f ('show_celsius');
    $this->show_times = $db->f ('show_times');
    $this->max_picture_width = $db->f ('max_picture_width');
    $this->max_picture_height = $db->f ('max_picture_height');
    $this->first_day_mode = $db->f ('first_day_mode');
    $this->last_day_mode = $db->f ('last_day_mode');

    $this->first_day->set_from_iso ($db->f ('first_day'));

    if ($this->last_day_mode == Day_mode_today)
    {
      $this->last_day->set_now ();
    }
    else
    {
      $this->last_day->set_from_iso ($db->f ('last_day'));
    }
  }

  /**
   * Copy properties from the given object. 
   * @param ALBUM $other
   * @access private
   */
  protected function copy_from ($other)
  {
    parent::copy_from($other);
    $this->first_day = clone ($other->first_day);
    $this->last_day = clone ($other->last_day);
  }
  
  /**
   * @param SQL_STORAGE $storage Store values to this object.
   */
  public function store_to ($storage)
  {
    parent::store_to ($storage);
    $tname =$this->table_name ();
    $storage->add ($tname, 'location', Field_type_string, $this->location);
    $storage->add ($tname, 'url_root', Field_type_string, $this->url_root);
    $storage->add ($tname, 'show_times', Field_type_integer, $this->show_times);
    $storage->add ($tname, 'show_celsius', Field_type_integer, $this->show_celsius);
    $storage->add ($tname, 'first_day_mode', Field_type_integer, $this->first_day_mode);
    $storage->add ($tname, 'last_day_mode', Field_type_integer, $this->last_day_mode);
    $storage->add ($tname, 'max_picture_width', Field_type_integer, $this->max_picture_width);
    $storage->add ($tname, 'max_picture_height', Field_type_integer, $this->max_picture_height);
    $storage->add ($tname, 'main_picture_id', Field_type_integer, $this->main_picture_id);
    $storage->add ($tname, 'first_day', Field_type_date_time, $this->first_day);
    $storage->add ($tname, 'last_day', Field_type_date_time, $this->last_day);
  }

  /**
   * @return JOURNAL
   * @see FOLDER::new_entry()
   * @access private
   */
  protected function _make_journal ()
  {
    $class_name = $this->app->final_class_name ('JOURNAL', 'albums/obj/journal.php');
    return new $class_name ($this->app);
  }

  /**
   * @return PICTURE
   * @see FOLDER::new_entry()
   * @access private
   */
  protected function _make_picture ()
  {
    $class_name = $this->app->final_class_name ('PICTURE', 'albums/obj/picture.php');
    return new $class_name ($this->app);
  }

  /**
   * @param PURGE_OPTIONS $options
   * @access private
   */
  protected function _purge ($options)
  {
    $tables = $this->app->table_names;
    /* remove associated pictures */
    $this->_purge_foreign_key ($tables->entries, 'id', $tables->pictures, 'entry_id');
    /* remove associated journals */
    $this->_purge_foreign_key ($tables->entries, 'id', $tables->journals, 'entry_id');

    parent::_purge ($options);
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
      case Handler_print_renderer:
      case Handler_html_renderer:
      case Handler_text_renderer:
        include_once ('albums/gui/album_renderer.php');
        return new ALBUM_RENDERER ($this->app, $options);
      case Handler_commands:
        include_once ('albums/cmd/album_commands.php');
        return new ALBUM_COMMANDS ($this);
      case Handler_history_item:
        include_once ('albums/obj/album_history_items.php');
        return new ALBUM_HISTORY_ITEM ($this->app);
      default:
        return parent::_default_handler_for ($handler_type, $options);
    }
  }
}

?>