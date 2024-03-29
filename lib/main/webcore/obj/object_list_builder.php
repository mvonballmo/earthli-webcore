<?php

/**
 * @copyright Copyright (c) 2002-2014 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage obj
 * @version 3.6.0
 * @since 2.7.1
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
require_once ('webcore/forms/form.php');

/**
 * Manages a list of {@link FOLDER}s and {@link ENTRY} objects.
 * Used by the {@link MULTIPLE_OBJECT_ACTION_FORM} and {@link PRINT_PREVIEW}.
 * @package webcore
 * @subpackage obj
 * @version 3.6.0
 * @since 2.7.1
 */
class OBJECT_LIST_BUILDER extends WEBCORE_OBJECT
{
  /**
   * List of selected folders.
   * Use {@link load_from_request()} to populate.
   * @var FOLDER[]
   */
  public $folders;

  /**
   * List of selected entries.
   * Use {@link load_from_request()} to populate.
   * @var ENTRY[]
   */
  public $entries;

  /**
   * @param FOLDER $folder Objects are from this folder.
   */
  public function __construct ($folder)
  {
    parent::__construct ($folder->app);

    $this->_folder = $folder;
    $this->entries = array ();
    $this->folders = array ();
  }

  /**
   * @return boolean
   */
  public function has_entries ()
  {
    return sizeof ($this->entries) > 0;
  }

  /**
   * @return boolean
   */
  public function has_folders ()
  {
    return sizeof ($this->folders) > 0;
  }

  /**
   * Are any folders or entries selected?
   * @return boolean
   */
  public function has_objects ()
  {
    return $this->has_folders () || $this->has_entries ();
  }

  /**
   * Return a human readable description of the selected object.
   * e.g. 1 album, 4 pictures and 2 journals
   * @return string
   */
  public function description ()
  {
    if (isset($this->_contents) && sizeof ($this->_contents))
    {
      if (sizeof ($this->_contents) > 1)
      {
        $last = array_pop ($this->_contents);
        $Result = implode (', ', $this->_contents);
        
        return $Result . ' and ' . $last;
      }

      return $this->_contents [0];
    }
    
    return '';
  }
  
  /**
   * List of entry ids; used by {@link FORM}s.
   * @return int[]
   */
  public function entry_ids ()
  {
    return $this->_entry_ids;
  }
  
  /**
   * List of folder ids; used by {@link FORM}s.
   * @return int[]
   */
  public function folder_ids ()
  {
    return $this->_folder_ids;
  }

  /**
   * Read in values from the {@link $method} array.
   * @access private
   */
  public function load_from_request ()
  {
    $this->entries = array ();
    $this->folders = array ();

    $this->_folder_ids = read_var ('folder_ids');
    if ($this->_folder_ids)
    {
      $folder_query = $this->login->folder_query ();
      $this->folders = $folder_query->objects_at_ids ($this->_folder_ids);
      
      $folder_info = $this->app->type_info_for ('FOLDER');
      $folder_count = is_array($this->folders) ? sizeof($this->folders) : 1;
      $this->_contents [] = $folder_info->format_amount ($folder_count);
    }

    $entry_types = $this->app->entry_type_infos ();

    /* If the generic array is not set, search for type-specific ones. */
    
    $this->_entry_ids = read_var ('entry_ids');
    if (! empty ($this->_entry_ids))
    {
      $entry_id_count = is_array($this->_entry_ids) ? sizeof($this->_entry_ids) : 1;
      $this->_contents [] = $entry_types [0]->format_amount ($entry_id_count);
    }
    else
    {
      $this->_entry_ids = array ();
      foreach ($entry_types as $type_info)
      {
        $request_ids = read_var ("{$type_info->id}_ids");
        if (! empty ($request_ids))
        {
          $this->_entry_ids = array_merge ($this->_entry_ids, $request_ids);
          $this->_contents [] = $type_info->format_amount (sizeof ($request_ids));
        }
      }
    }
    
    /* If there are still no entries, see if all entries should be retrieved. */
    
    if (empty ($this->_entry_ids))
    {
      $entry_type = read_var ('entry_type');
      if ($entry_type)
      {
        $folder_id = read_var ('id');
    
        $this->assert (! empty ($folder_id), 'Cannot retrieve all items with an [id].', 'load_from_request', 'OBJECT_LIST_BUILDER');
    
        $folder_query = $this->login->folder_query ();
        /** @var FOLDER $folder */
        $folder = $folder_query->object_at_id ($folder_id);
        $entry_query = $folder->entry_query ();
        $entry_query->set_type ($entry_type);
    
        $this->entries = $entry_query->objects ();
        $this->_entry_ids = $entry_query->indexed_ids ();
      
        $type_info = $this->app->type_info_for ($entry_type);  
        $this->_contents [] = $type_info->format_amount (sizeof ($this->entries));
      }
    }
    else
    {
      $entry_query = $this->login->all_entry_query ();
      if (sizeof ($this->app->entry_type_infos ()) > 1)
      {
        $entry_query->set_order ('entry.type, entry.time_created');
      }
      $this->entries = $entry_query->objects_at_ids ($this->_entry_ids);
    }
  }

  /**
   * Entries and subfolders are retrieved from this folder.
   * @var FOLDER $folder
   * @access private
   */
  protected $_folder;

  /**
   * The list of ids for the entries included in this list.
   * @var int[]
   */
  private $_entry_ids;

  /**
   * The list of ids for the folders included in this list.
   * @var int[]
   */
  private $_folder_ids;

  /**
   * Description of the contents of the list builder.
   * Returned by {@link description()}.
   * @var string[]
   * @access private
   */
  protected $_contents;
}