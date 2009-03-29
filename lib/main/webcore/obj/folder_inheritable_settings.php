<?php

/**
 * @copyright Copyright (c) 2002-2008 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage obj
 * @version 3.0.0
 * @since 2.5.0
 * @access private
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
require_once ('webcore/obj/storable.php');

/**
 * Inheritable settings for {@link FOLDER}s.
 * Use this base class for options that are inherited in the folder hierarchy.
 * Settings are created and attached to a {@link FOLDER}. Use {@link
 * attach_to()} to re-target the settings. Call {@link store()} to stop
 * inheriting settings (if {@link inherited()}) or {@link purge()} to start
 * inheriting from the parent folder.
 * @package webcore
 * @subpackage sys
 * @version 3.0.0
 * @since 2.5.0
 * @access private
 */
class FOLDER_INHERITABLE_SETTINGS extends STORABLE
{
  /**
   * Create an history item record when created?
   * If the settings are created, should an history item record for the originating folder be created?
   * Some objects will simply store the object and settings all at once, so they will not want to
   * create a separate history item record.
   * @var boolean
   * @access private
   */
  public $create_history_item_for_self = TRUE;

  /**
   * @param FOLDER $folder Attached to this folder.
   */
  function FOLDER_INHERITABLE_SETTINGS ($folder)
  {
    STORABLE::STORABLE ($folder->app);
    $this->attach_to ($folder);
  }

  /**
   * Settings were retrieved for this folder.
   * The settings are not necessarily defined by this folder. Use {@link definer()} to
   * determine the defining folder.
   * @return FOLDER
   */
  function folder ()
  {
    $this->assert (isset ($this->_folder), '\'_folder\' is not set', 'folder', 'FOLDER_SECURITY');
    return $this->_folder;
  }

  /**
   * Folder which defines these settings.
   * @return FOLDER
   */
  function definer ()
  {
    if (! isset ($this->_definer))
    {
      $this->_definer = $this->app->login->folder_at_id ($this->_definer_id);
    }

    return $this->_definer;
  }

  /**
   * @return boolean
   */
  function exists ()
  {
    return $this->_exists;
  }

  /**
   * Are these settings defined by the folder?
   * That is, is the {@link definer()} the same as the folder which created
   * these settings?
   * @return boolean
   */
  function inherited ()
  {
    $field_name = $this->_field_name;
    return $this->_folder->$field_name != $this->_folder->id;
  }

  /**
   * Attach or detach these settings to the current folder.
   * If "inherited" is <code>True</code>, then the folder's settings are removed
   * from the database and it is reset to point to it's parent's version of the
   * settings. If "inherited" is <code>False</code>, a copy of these settings is
   * created in the database. The current folder owns these settings and all
   * sub-folders are updated to refer to the new settings.
   * @param boolean $inherited
   * @param boolean $apply_immediately Stores to the database if
   * <code>True</code>; use {@link apply_changes()} to commit changes later
   * otherwise.
   */
  function set_inherited ($inherited, $apply_immediately = TRUE)
  {
    if ($this->inherited () != $inherited)
    {
      if (! $inherited)
      {
        $this->_definer = $this->_folder;
      }
      else
      {
        $this->_definer = $this->parent_definer ();
      }

      $this->_definer = clone_object($this->_definer);

      $field_name = $this->_field_name;
      $this->_definer_id = $this->_definer->id;
      $this->_folder->$field_name = $this->_definer->id;
      $this->_exists = $this->exists_in_database ();
      $this->_changes_pending = TRUE;
    }
    elseif (! $inherited && $this->_stores_data)
      $this->_changes_pending = TRUE;

    if ($apply_immediately)
    {
      $this->apply_changes ();
    }
  }

  /**
   * Commit any changes made with {@link set_inherited()}.
   */
  function apply_changes ()
  {
    if ($this->_changes_pending)
    {
      if ($this->inherited ())
      {
        $this->purge ();
      }
      else
      {
        $this->store ();
      }
      $this->_changes_pending = FALSE;
    }
  }

  /**
   * Move these settings to this folder.
   * Simply transfers ownership of internal fields.
   * @param FOLDER $folder */
  function attach_to ($folder)
  {
    $this->_folder = $folder;
    $field_name = $this->_field_name;
    $this->_definer_id = $folder->$field_name;
    $this->_exists = ($folder->$field_name == $folder->id);
    if ($folder->$field_name == $folder->id)
    {
      $this->_definer = $folder;
    }
  }

  /**
   * @param DATABASE $db
   */
  function load ($db)
  {
    parent::load ($db);
    $this->folder_id = $db->f ("folder_id");
    $this->_exists = TRUE;
  }

  /**
   * @param SQL_STORAGE $storage Store values to this object.
   */
  function store_to ($storage)
  {
    if ($this->_stores_data)
    {
      $tname = $this->_settings_table_name ();
      $storage->restrict ($tname, 'folder_id');
      $storage->add ($tname, 'folder_id', Field_type_integer, $this->_definer_id, Storage_action_create);
    }
    $this->_exists = TRUE;
  }

  /**
   * Store the object for the first time.
   * @access private
   */
  function _create ()
  {
    if ($this->_stores_data)
    {
      parent::_create ();
    }
    $this->_update_folder_tree (array ($this->_folder), $this->_folder);
    $this->_definer = $this->_folder;
    $this->_definer_id = $this->_definer->id;
    $field_name = $this->_field_name;
    $this->_folder->$field_name = $this->_folder->id;
  }

  /**
   * Update an existing object.
   * @access private
   */
  function _update ()
  {
    if ($this->_stores_data)
    {
      parent::_update ();
    }
  }

  /**
   * Calls {@link _update_folder()} for the whole folder tree.
   * @see FOLDER
   * @param tree[FOLDER] $folders
   * @param FOLDER $source_folder
   * @access private
   */
  function _update_folder_tree ($folders, $source_folder)
  {
    if (sizeof ($folders))
    {
      $this->_update_folder_list ($folders, $source_folder, TRUE);
      foreach ($folders as $folder)
        $this->_update_folder_tree ($folder->sub_folders (), $source_folder);
    }
  }

  /**
   * Calls {@link _update_folder()} for each folder in the list.
   * @see FOLDER
   * @param tree[FOLDER] $folders
   * @param FOLDER $source_folder
   * @param boolean $creating Flag passed to {@link _update_folder()}.
   * @access private
   */
  function _update_folder_list ($folders, $source_folder, $creating)
  {
    if (sizeof ($folders))
    {
      foreach ($folders as $folder)
        $this->_update_folder ($folder, $source_folder, $creating);
    }
  }

  /**
   * Update "target_folder" to use settings from "source_folder".
   * If "creating" is set, the settings are being created and the target folder
   * should be updated to reflect the new setting.
   * @param FOLDER $target_folder
   * @param FOLDER $source_folder
   * @param boolean $creating
   * @access private
   */
  function _update_folder ($target_folder, $source_folder, $creating)
  {
    $field_name = $this->_field_name;

    if ($this->create_history_item_for_self || ($target_folder->id != $this->_folder->id))
    {
      $history_item = $target_folder->new_history_item ();
      if ($target_folder->visible ())
      {
        $history_item->kind = History_item_updated;
      }
      else
      {
        $history_item->kind = History_item_hidden_update;
      }
      $history_item->title = $this->_history_item_title ($creating);
      $history_item->record_difference ($this->_history_item_description ($creating, $source_folder));
      $history_item->store ();
    }

    if ($creating)
    {
      $target_folder->$field_name = $source_folder->id;
      $target_folder->store ();
    }
  }

  /**
   * Use this folder to "reset" to inherited.
   * @return FOLDER
   * @access private
   */
  function parent_definer ()
  {
    $parent = $this->_folder->parent_folder ();
    if (isset ($parent))
    {
      $field_name = $this->_field_name;
      $folder_query = $this->login->folder_query ();
      return $folder_query->object_at_id ($parent->$field_name);
    }

    $null_reference = null;

    return $null_reference;
  }

  /**
   * @param PURGE_OPTIONS $options
   * @access private
   */
  function _purge ($options)
  {
    $field_name = $this->_field_name;

    /* Update this folder and all subfolders to use the settings used
       by the parent (which is not necessarily the definer of the settings). */

    $this->_definer = $this->parent_definer ();
    if (isset ($this->_definer))
    {
      $this->_definer_id = $this->_definer->id;

      $folder_query = $this->login->folder_query ();
      $folder_query->restrict ("$field_name = {$this->_folder->id}");
      $folders = $folder_query->objects ();
      $this->_update_folder_list ($folders, $this->_definer, FALSE);

      $field_name_value = $this->_definer_id;
    }
    else
    {
      $this->_definer_id = 0;
      $field_name_value = 0;
    }

    $tname = $this->_settings_table_name ();
    if ($tname)
    {
      $this->db->logged_query ("DELETE LOW_PRIORITY FROM $tname WHERE folder_id = {$this->_folder->id}");
    }

    $this->_folder->$field_name = $field_name_value;
    $this->_exists = FALSE;
  }

  /**
   * Title for a folder's history item for inheriting this option.
   * @param boolean $adding Is the option being added?
   * @return string
   * @access private
   * @abstract
   */
  function _history_item_title ($creating)
  {
    $this->raise_deferred ('_history_item_title', 'FOLDER_INHERITABLE_SETTINGS');
  }

  /**
   * Description for a folder's history item for inheriting this option.
   * @param boolean $adding Is the option being added?
   * @param FOLDER $folder Folder from which the option is being added.
   * @return string
   * @access private
   * @abstract
   */
  function _history_item_description ($creating, $folder)
  {
    $this->raise_deferred ('_history_item_description', 'FOLDER_INHERITABLE_SETTINGS');
  }

  /**
   * Name of the table in which settings are stored.
   * Set {@link $_stores_data} to <code>False</code> to prevent {@link store()}
   * from saving settings with this object (useful if there are multiple records
   * for a single 'set' of settings).
   * @return string
   * @access private
   * @abstract
   */
  function _settings_table_name ()
  {
    $this->raise_deferred ('_settings_table_name', 'FOLDER_INHERITABLE_OPTIONS');
  }

  /**
   * ID of the folder to which the settings are attached.
   * Whereas these settings may be used by any other folder, they belong to the folder
   * specified here.
   * @see definer()
   * @var integer
   */
  protected $_definer_id;
  /**
   * Settings are defined in this folder.
   * @var FOLDER
   * @access private
   */
  protected $_definer;
  /**
   * Name of the object field in the folder and database.
   * @var string
   * @access private
   */
  protected $_field_name;
  /**
   * Settings retrieved from this folder.
   *  @access private
   * @var FOLDER
   */
  protected $_folder;
  /**
   * If <code>True</code>, stores to {@link _settings_table_name()}.
   * If <code>False</code>, settings are not automatically saved when {@link
   * store()} is called. Descendents should redefine this property, if
   * necessary.
   * @see FOLDER_SECURITY
   * @var boolean
   * @access private
   */
  protected $_stores_data = TRUE;
  /**
   * @var boolean
   * @access private
   */
  protected $_exists;
}

?>