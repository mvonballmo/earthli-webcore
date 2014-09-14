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
 * Added to file names to make thumbnails.
 */
define ('Picture_thumbnail_suffix', '_tn');

/**
 * A {@link PICTURE}, with date, description and thumbnail.
 * @package albums
 * @subpackage obj
 * @version 3.6.0
 * @since 2.5.0
 */
class PICTURE extends ALBUM_ENTRY
{
  /**
   * URL of the picture.
   * May be relative to the {@link $url_root} of the {@link ALBUM}.
   * @var string
   */
  public $file_name;

  /**
   * Return the path to the picture as a URL.
   * @param boolean $force_root If set to {@link Force_root_on}, returns a fully
   * resolved URL.
   * @return URL
   * @see thumbnail_location()
   */
  public function location ($force_root = false)
  {
    $Result = new URL ($this->file_name);

    /* Must append an extension here or the URL will assume it's a
     * folder when appending or prepending
     */

    if ($Result->extension () == '')
    {
      $Result->replace_extension ('jpg');
    }

    /* If the file path is not already a full url, then prepend
     * the folder's path.
     */

    if (! $Result->has_domain ())
    {
      if ($force_root)
      {
        $root_override = Force_root_on;
      }
      else
      {
        $root_override = null;
      }

      $f = $this->parent_folder ();
      $Result->prepend ($f->picture_folder_url ($root_override));
    }

    return $Result;
  }

  /**
   * Return the path to the thumbnail as a URL.
   * @param boolean $force_root If set, returns a fully resolved URL.
   * @return URL
   * @see location()
   */
  public function thumbnail_location ($force_root = false)
  {
    $Result = $this->location ($force_root);
    $Result->append_to_name (Picture_thumbnail_suffix);
    return $Result;
  }

  /**
   * Return the URL of the picture as a string.
   * @param boolean $force_root If set, returns a fully resolved URL.
   * @return string
   * @see full_thumbnail_name()
   */
  public function full_file_name ($force_root = false)
  {
    $url = $this->location ($force_root);
    return $url->as_html ();
  }

  /**
   * Return the URL of the thumbnail as a string.
   * The thumbnail is the same URL as the filename, but with '_tn' attached before the extension.
   * @param boolean $force_root If set, returns a fully resolved URL.
   * @return string
   * @see full_file_name()
   */
  public function full_thumbnail_name ($force_root = false)
  {
    $url = $this->location ($force_root);
    $url->append_to_name (Picture_thumbnail_suffix);
    return $url->as_html ();
  }

  /**
   * Calculate metrics for this picture.
   * Constraints are based on album sizing options and picture size.
   * @param boolean $apply_folder_size Resize to the album options before returning.
   * @param boolean $load_image Loads original size from the image if
   * <code>True</code>. See {@link IMAGE::set_url()} for more information.
   * @see thumbnail_metrics()
   * @return IMAGE_METRICS
   */
  public function metrics ($apply_folder_size = true, $load_image = true)
  {
    $class_name = $this->app->final_class_name ('IMAGE_METRICS', 'webcore/util/image.php');
    $Result = new $class_name ();
    $url = $this->location (Force_root_on);
    $Result->set_url ($url->as_text (), $load_image);

    if ($apply_folder_size)
    {
      $fldr = $this->parent_folder ();
      if ($fldr->max_picture_width && $fldr->max_picture_height)
      {
        if ($load_image)
        {
          $Result->resize_to_fit ($fldr->max_picture_width, $fldr->max_picture_height);
        }
        else
        {
          $Result->resize ($fldr->max_picture_width, $fldr->max_picture_height);
        }
      }
    }

    return $Result;
  }

  /**
   * Metrics for the thumbnail.
   * @see metrics()
   * @param boolean $load_image Loads original size from the image if
   * <code>True</code>. See {@link IMAGE::set_url()} for more information.
   * @return IMAGE_METRICS
   */
  public function thumbnail_metrics ($load_image = true)
  {
    $class_name = $this->app->final_class_name ('IMAGE_METRICS', 'webcore/util/image.php');
    $Result = new $class_name ();
    $url = $this->thumbnail_location (Force_root_on);
    $Result->set_url ($url->as_text (), $load_image);
    return $Result;
  }

  /**
   * @param DATABASE $db
   */
  public function load ($db)
  {
    parent::load ($db);
    $this->file_name = $db->f ('file_name');
  }

  /**
   * @param SQL_STORAGE $storage Store values to this object.
   */
  public function store_to ($storage)
  {
    parent::store_to ($storage);
    $storage->add ($this->secondary_table_name (), 'file_name', Field_type_string, $this->file_name);
  }

  /**
   * Name of the home page name for this object.
   * @return string
   */
  public function page_name ()
  {
    return $this->app->page_names->picture_home;
  }

  /**
   * Name of this object's secondary database table.
   * @return string
   * @access private
   */
  public function secondary_table_name ()
  {
    return $this->app->table_names->pictures;
  }

  /**
   * Move the object to the specified folder.
   * If both the source and target albums are {@link Album_location_type_local},
   * then move the pictures to the new folder.
   * @param FOLDER $fldr
   * @param FOLDER_OPERATION_OPTIONS $options
   */
  protected function _move_to ($fldr, $options)
  {
    if ($options->update_now)
    {
      $parent = $this->parent_folder ();
      $old_location = $parent->location;
      $old_folder = url_to_folder ($parent->picture_folder_url (true));
    }

    parent::_move_to ($fldr, $options);

    if ($options->update_now)
    {
      if (($old_location == Album_location_type_local) && ($fldr->location == Album_location_type_local))
      {
        $new_folder = url_to_folder ($fldr->picture_folder_url (true));

        if ($old_folder != $new_folder)
        {
          $old_url = new FILE_URL ($old_folder);
          $old_url->replace_name ($this->file_name);
          if ($old_url->extension () == '')
          {
            $old_url->replace_extension ('jpg');
          }

          $new_url = new FILE_URL ($new_folder);
          $new_url->replace_name ($this->file_name);
          if ($new_url->extension () == '')
          {
            $new_url->replace_extension ('jpg');
          }

          if (file_exists ($old_url->as_text ()))
          {
            ensure_path_exists ($new_folder);
            log_message ('Moved [' . $old_url->as_text () . '] to [' . $new_url->as_text () . ']', Msg_type_debug_info, Msg_channel_system);
            if (! rename ($old_url->as_text (), $new_url->as_text ()))
            {
              $this->raise ('_move_to', 'PICTURE', 'Could not move main image for [' . $this->title_as_plain_text () . '].');
            }
          }

          $old_url->append_to_name (Picture_thumbnail_suffix);
          $new_url->append_to_name (Picture_thumbnail_suffix);
          if (file_exists ($old_url->as_text ()))
          {
            ensure_path_exists ($new_folder);
            log_message ('Moved [' . $old_url->as_text () . '] to [' . $new_url->as_text () . ']', Msg_type_debug_info, Msg_channel_system);
            if (! rename ($old_url->as_text (), $new_url->as_text ()))
            {
              $this->raise ('_move_to', 'PICTURE', 'Could not move thumbnail image for [' . $this->title_as_plain_text () . '].');
            }
          }
        }
      }
    }
  }

  /**
   * Copy the object to the specified folder.
   * If both the source and target albums are {@link Album_location_type_local},
   * and they are different folders, copy the pictures to the new folder.
   * @param FOLDER $fldr
   * @param FOLDER_OPERATION_OPTIONS $options
   */
  protected function _copy_to ($fldr, $options)
  {
    if ($options->update_now)
    {
      $parent = $this->parent_folder ();
      $old_location = $parent->location;
      $old_folder = url_to_folder ($parent->picture_folder_url (true));
    }

    parent::_copy_to ($fldr, $options);

    if ($options->update_now)
    {
      if (($old_location == Album_location_type_local) && ($fldr->location == Album_location_type_local))
      {
        $new_folder = url_to_folder ($fldr->picture_folder_url (true));

        if ($old_folder != $new_folder)
        {
          $old_url = new FILE_URL ($old_folder);
          $old_url->replace_name ($this->file_name);
          if ($old_url->extension () == '')
          {
            $old_url->replace_extension ('jpg');
          }

          $new_url = new FILE_URL ($new_folder);
          $new_url->replace_name ($this->file_name);
          if ($new_url->extension () == '')
          {
            $new_url->replace_extension ('jpg');
          }

          if (file_exists ($old_url->as_text ()))
          {
            ensure_path_exists ($new_folder);
            log_message ('Copied [' . $old_url->as_text () . '] to [' . $new_url->as_text () . ']', Msg_type_debug_info, Msg_channel_system);
            if (! copy ($old_url->as_text (), $new_url->as_text ()))
            {
              $this->raise ('_copy_to', 'PICTURE', 'Could not copy main image for [' . $this->title_as_plain_text () . '].');
            }
          }

          $old_url->append_to_name (Picture_thumbnail_suffix);
          $new_url->append_to_name (Picture_thumbnail_suffix);
          if (file_exists ($old_url->as_text ()))
          {
            ensure_path_exists ($new_folder);
            log_message ('Copied [' . $old_url->as_text () . '] to [' . $new_url->as_text () . ']', Msg_type_debug_info, Msg_channel_system);
            if (! copy ($old_url->as_text (), $new_url->as_text ()))
            {
              $this->raise ('_copy_to', 'PICTURE', 'Could not copy thumbnail image for [' . $this->title_as_plain_text () . '].');
            }
          }
        }
      }
    }
  }

  /**
   * @param PURGE_OPTIONS $options
   * @access private
   */
  protected function _purge ($options)
  {
    if ($options->remove_resources)
    {
      $url = $this->location (true);
      $file_name = url_to_file_name ($url->as_text ());
      if ($file_name)
      {
        @unlink ($file_name);
      }
      $url = $this->thumbnail_location (true);
      $file_name = url_to_file_name ($url->as_text ());
      if ($file_name)
      {
        @unlink ($file_name);
      }
    }

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
      case Handler_navigator:
        include_once ('albums/gui/picture_navigator.php');
        return new PICTURE_NAVIGATOR ($this);
      case Handler_print_renderer:
      case Handler_html_renderer:
      case Handler_text_renderer:
        include_once ('albums/gui/picture_renderer.php');
        return new PICTURE_RENDERER ($this->app, $options);
      case Handler_commands:
        include_once ('albums/cmd/picture_commands.php');
        return new PICTURE_COMMANDS ($this);
      case Handler_history_item:
        include_once ('albums/obj/album_history_items.php');
        return new PICTURE_HISTORY_ITEM ($this->app);
      case Handler_location:
        include_once ('albums/gui/picture_renderer.php');
        return new PICTURE_LOCATION_RENDERER ($this->context);
      default:
        return parent::_default_handler_for ($handler_type, $options);
    }
  }

  /**
   * Name of this type of album entry.
   * @var string
   * @access private
   */
  public $type = 'picture';
}

?>