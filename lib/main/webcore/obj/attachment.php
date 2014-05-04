<?php

/**
 * @copyright Copyright (c) 2002-2014 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage obj
 * @version 3.5.0
 * @since 2.5.0
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
require_once ('webcore/obj/object_in_folder.php');

/**
 * A file attachment for an object in the WebCore.
 * These can be attached to {@link ENTRY} objects, {@link FOLDER}s, {@link COMMENT}s, {@link USER}s
 * and {@link GROUP}s.
 * @package webcore
 * @subpackage obj
 * @version 3.5.0
 * @since 2.5.0
 */
class ATTACHMENT extends OBJECT_IN_FOLDER
{
  /**
   * The type of object to which this belongs.
   * @var string
   */
  public $type;

  /**
   * To which object id is this attached?
   * @var integer
   */
  public $object_id;

  /**
   * What was the name of the file when uploaded?
   * @var string
   */
  public $original_file_name;

  /**
   * Location within the server file system.
   * @var string
   */
  public $file_name = '';

  /**
   * How large is the file?
   * @var string
   */
  public $size;

  /**
   * MIME type of the file.
   * May be empty or unknown.
   * @var string
   */
  public $mime_type;

  /**
   * @var boolean
   */
  public $is_image;

  /**
   * @var boolean
   */
  public $is_archive;

  /**
   * Fully resolved server-local path to the file.
   * @return string
   */
  public function full_file_name ()
  {
    $Result = $this->file_name;
    if (! $Result || ! file_exists ($this->file_name))
    {
      $Result = url_to_file_name ($this->full_url (Force_root_on));
    }
    return $Result;
  }

  /**
   * Fully resolved URL to the file.
   * @see full_file_name()
   * @param boolean $root_override Overrides {@link $resolve_to_root} if set to
   * {@link Force_root_on}.
   * @return string
   */
  public function full_url ($root_override = null)
  {
    if (file_exists ($this->file_name))
    {
      $Result = file_name_to_url ($this->file_name);
    }
    else
    {
      $Result = $this->_host->resolve_url ('{att_link}' . $this->file_name, $root_override);
    }

    return $Result;
  }

  /**
   * Fully resolved server-local path to the thumbnail.
   * Will be empty if not {@link is_image} or no thumnail was generated.
   * @see thumbnail_url()
   * @param string $for_file The optional file for which to get a thumbnail
   * name; if empty, the result of {@link full_file_name} is used.
   * @return string
   */
  public function thumbnail_file_name ($for_file = '')
  {
    if (! $for_file)
    {
      $for_file = $this->full_file_name ();
    }
    $url = new FILE_URL ($for_file);
    $url->append_to_name ('_tn');
    return $url->as_text ();
  }

  /**
   * Fully resolved URL to the thumbnail.
   * May be empty.
   * @see thumbnail_file_name()
   * @return string
   */
  public function thumbnail_url ()
  {
    $thumb_url = $this->thumbnail_file_name ();
    if (file_exists ($thumb_url))
    {
      return file_name_to_url ($thumb_url);
    }
    
    return '';
  }

  /**
   * Thumbnail as an HTML tag.
   * @param string $css_class
   * @return string
   */
  public function thumbnail_as_html ($css_class = 'frame')
  {
    $thumb_url = $this->thumbnail_url ();
    if ($thumb_url)
    {
      if ($css_class)
      {
        return "<img class=\"$css_class\" src=\"$thumb_url\" alt=\"$this->title\">\n";
      }

      return "<img src=\"$thumb_url\" alt=\"$this->title\">\n";
    }
    
    return '';
  }

  /**
   * File type icon.
   * Retrieves file type to icon mappings from the {@link APPLICATION::file_type_manager()}.
   * @param string $size The size of the icon to return.
   * @return string
   */
  public function icon_as_html ($size = One_hundred_px)
  {
    $ft = $this->app->file_type_manager ();
    $url = new FILE_URL ($this->file_name);
    return $ft->icon_as_html ($this->mime_type, $url->extension (), $size);
  }

  /**
   * Attached to this object.
   * @return ATTACHMENT_HOST
   */
  public function host ()
  {
    $this->assert (isset ($this->_host), '_host is not cached.', 'host', 'ATTACHMENT');
    return $this->_host;
  }

  /**
   * Set a new file for the attachment.
   * Used during previews of uploaded forms when the file is not yet
   * in the final location.
   * @param string $new_file_name Full path and file name.
   */
  public function set_full_file_name ($new_file_name)
  {
    $old_file_name = $this->full_file_name ();
    if ($old_file_name != $new_file_name)
    {
      $this->_old_file_name = $old_file_name;
      $this->file_name = $new_file_name;

      $class_name = $this->app->final_class_name ('IMAGE', 'webcore/util/image.php');
      /** @var $img IMAGE */
      $img = new $class_name ();
      $img->set_file ($this->full_file_name ());
      $this->is_image = $img->loadable ();
      if (! $this->is_image)
      {
        $class_name = $this->app->final_class_name ('ARCHIVE', 'webcore/util/archive.php');
        /** @var $archive ARCHIVE */
        $archive = new $class_name ($this->full_file_name ());
        $this->is_archive = $archive->readable ();
      }
    }
  }

  /**
   * Set the parent object for this attachment.
   * Does not store to the database. Sets up both the host and the folder information
   * for this attachment; used during object setup when retrieved from database.
   * @param ATTACHMENT_HOST $host
   */
  public function set_host ($host)
  {
    $this->set_parent_folder ($host->parent_folder ());
    $this->_host = $host;
    $this->object_id = $host->id;
  }

  /**
   * Name of the home page name for this object.
   * @return string
   */
  public function page_name ()
  {
    return $this->app->page_names->attachment_home;
  }

  /**
   * Arguments to the home page url for this object.
   * @return string
   */
  public function page_arguments ()
  {
    return "id=$this->id&type=$this->type";
  }

  /**
   * Name of this object's database table.
   * @return string
   * @access private
   */
  public function table_name ()
  {
    return $this->app->table_names->attachments;
  }

  /**
   * @param DATABASE $db Database from which to load values.
   */
  public function load ($db)
  {
    parent::load ($db);
    $this->type = $db->f ('type');
    $this->object_id = $db->f ('object_id');
    $this->file_name = $db->f ('file_name');
    $this->original_file_name = $db->f ('original_file_name');
    $this->size = $db->f ('size');
    $this->mime_type = $db->f ('mime_type');
    $this->is_image = $db->f ('is_image');
    $this->is_archive = $db->f ('is_archive');
  }

  /**
   * @param SQL_STORAGE $storage Store values to this object.
   */
  public function store_to ($storage)
  {
    parent::store_to ($storage);
    $tname = $this->table_name ();
    $storage->add ($tname, 'type', Field_type_string, $this->type, Storage_action_create);
    $storage->add ($tname, 'object_id', Field_type_integer, $this->object_id, Storage_action_create);
    $storage->add ($tname, 'original_file_name', Field_type_string, $this->original_file_name);
    $storage->add ($tname, 'file_name', Field_type_string, $this->file_name);
    $storage->add ($tname, 'size', Field_type_integer, $this->size);
    $storage->add ($tname, 'mime_type', Field_type_string, $this->mime_type);
    $storage->add ($tname, 'is_image', Field_type_boolean, $this->is_image);
    $storage->add ($tname, 'is_archive', Field_type_boolean, $this->is_archive);
  }

  /**
   * @access private
   */
  public function store ()
  {
    parent::store ();

    if (isset ($this->_old_file_name) && file_exists ($this->_old_file_name))
    {
      @unlink ($this->_old_file_name);
      @unlink ($this->thumbnail_file_name ($this->_old_file_name));
    }
  }

  /**
   * Render the location within the object hierarchy.
   * @param boolean $use_links Show objects as links?
   * @param string $separator Optional separator. If not set, {@link APPLICATION_DISPLAY_OPTIONS::$obj_url_separator} is used.
   * @param TITLE_FORMATTER $formatter Optional formatter to use.
   * @return string
   */
  public function object_url ($use_links, $separator = null, $formatter = null)
  {
    $Result = parent::object_url ($use_links, $separator, $formatter);
    $host = $this->host ();
    $host_url = $host->object_url ($use_links, $separator, $formatter);

    if (! isset ($separator))
    {
      $separator = $this->app->display_options->obj_url_separator;
    }

    return $host_url . $separator . $Result;
  }

  /**
   * @param PURGE_OPTIONS $options
   * @access private
   */
  protected function _purge ($options)
  {
    if ($options->remove_resources)
    {
      @unlink ($this->full_file_name());
      if ($this->is_image)
      {
        @unlink ($this->thumbnail_file_name ());
      }
    }

    parent::_purge ($options);
  }

  /**
   * Name of the {@link FOLDER_PERMISSIONS} to use for this object.
   * @access private
   */
  protected function _privilege_set ()
  {
    return Privilege_set_attachment;
  }

  /**
   * Return default handler objects for supported tasks.
   * @param string $handler_type Specific functionality required.
   * @param OBJECT_RENDERER_OPTIONS $options
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
        include_once ('webcore/gui/attachment_renderer.php');
        return new ATTACHMENT_RENDERER ($this->app, $options);
      case Handler_commands:
        include_once ('webcore/cmd/attachment_commands.php');
        return new ATTACHMENT_COMMANDS ($this);
      case Handler_history_item:
        include_once ('webcore/obj/webcore_history_items.php');
        return new ATTACHMENT_HISTORY_ITEM ($this->app);
      default:
        return parent::_default_handler_for ($handler_type, $options);
    }
  }

  /**
   * Apply class-specific restrictions to this query.
   * @param SUBSCRIPTION_QUERY $query
   * @param HISTORY_ITEM $history_item Action that generated this request. May be empty.
   * @access private
   */
  protected function _prepare_subscription_query ($query, $history_item)
  {
    $folder = $this->parent_folder ();
    $host = $this->host ();

    switch ($this->type)
    {
    case History_item_comment:
      $query->restrict ('watch_comments > 0');
      $query->restrict_kinds (array (Subscribe_folder => $folder->id
                                     , Subscribe_entry => $host->entry_id
                                     , Subscribe_comment => $host->parent_id
                                     , Subscribe_comment => $host->id
                                     , Subscribe_user => $this->creator_id));
      break;
    case History_item_entry:
      $query->restrict ('watch_entries > 0');
      $query->restrict_kinds (array (Subscribe_folder => $folder->id
                                     , Subscribe_entry => $host->id
                                     , Subscribe_user => $this->creator_id));
      break;
    case History_item_folder:
      $query->restrict ('watch_entries > 0');
      $query->restrict_kinds (array (Subscribe_folder => $this->id
                                     , Subscribe_user => $this->creator_id));
      break;
    }
  }

  /**
   * Previously set file name.
   * @var string
   * @access private
   */
  protected $_old_file_name;

  /**
   * @var ATTACHMENT_HOST
   * @access private
   */
  protected $_host;
}