<?php

/**
 * @copyright Copyright (c) 2002-2014 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage obj
 * @version 3.6.0
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
require_once ('webcore/obj/content_object.php');

/**
 * Should the database be updated?
 * Used with the state-altering commands, like {@link show()}, {@link hide()},
 * {@link lock()}, {@link delete()} and {@link restore()}.
 */
define ('Defer_database_update', false);

/**
 * Base class of all content objects in a WebCore application.
 * These objects all maintain a link to their containing folder (sometimes indirectly)
 * and a {@link $state} (visible/deleted/etc.) within that folder. All content objects
 * also have a {@link $title} and a {@link $description}.
 * @package webcore
 * @subpackage obj
 * @version 3.6.0
 * @since 2.5.0
 */
abstract class OBJECT_IN_FOLDER extends CONTENT_OBJECT
{
  /**
   * @var integer
   */
  public $state = Visible;

  /**
   * ID of the user with owner privileges on this object.
   * This is usually the same as the {@link $creator_id}.
   * @var integer
   * @see owner()
   */
  public $owner_id;

  /**
   * @return USER
   */
  public function owner ()
  {
    return $this->app->user_at_id ($this->owner_id);
  }

  /**
   * @return boolean
   */
  public function invisible ()
  {
    return $this->state & Invisible;
  }

  /**
   * @return boolean
   */
  public function visible ()
  {
    return $this->state & Visible;
  }

  /**
   * @return boolean
   */
  public function deleted ()
  {
    return ($this->state & Deleted) == Deleted;
  }

  /**
   * @return boolean
   */
  public function locked ()
  {
    return ($this->state & Locked) == Locked;
  }

  /**
   * Describe the state of this object.
   * Useful for formatting titles and object descriptions.
   * @see state_as_icon()
   * @return string
   */
  public function state_as_string ()
  {
    switch ($this->state)
    {
    case Visible:
      return 'Visible';
    case Deleted:
      return 'Deleted';
    case Locked:
      return 'Locked';
    case Hidden:
      return 'Hidden';
    default:
      if ($this->state & Invisible)
      {
        return 'Invisible';
      }

      return 'Unknown';
    }
  }

  /**
   * Describe the state of this object with an HTML icon.
   * Useful for formatting titles and object descriptions.
   * @see state_as_string()
   * @param string $size CSS for the size of the picture.
   * @return string
   */
  public function state_as_icon ($size = Sixteen_px)
  {
    return $this->app->resolve_icon_as_html ($this->state_icon_url(), $size, $this->state_as_string());
  }

  /**
   * @return TITLE_FORMATTER
   */
  public function title_formatter ()
  {
    $Result = parent::title_formatter ();
    if ($this->invisible ())
    {
      $Result->css_class = 'invisible';
    }
    else
    {
      $Result->css_class = 'visible';
    }
    return $Result;
  }

  /**
   * @return string
   */
  public function raw_title ()
  {
    $Result = parent::raw_title ();
    if ($this->invisible ())
    {
      $Result .= ' (' . $this->state_as_string () . ')';
    }
    return $Result;
  }

  public function social_image_url()
  {
    return '';
  }

  /**
   * Reroutes unhandled folder aliases through the {@link FOLDER}.
   * @param string $url
   * @param boolean $root_override Overrides {@link $resolve_to_root} if set to
   * {@link Force_root_on}.
   * @return string
   */
  public function resolve_file ($url, $root_override = null)
  {
    $folder = $this->parent_folder ();
    if (isset ($folder))
    {
      return $folder->resolve_file ($url, $root_override);
    }

    return parent::resolve_file ($url, $root_override);
  }

  /**
   * Containing folder for this object.
   * Returns the full object for this object's folder. Use {@link parent_folder_id()} if
   * you only need to link to this object's folder -- it avoids loading the folder object
   * from database.
   * @return FOLDER
   */
  public function parent_folder ()
  {
    if (! isset ($this->_parent_folder))
    {
      $this->_parent_folder = $this->_load_parent_folder ();
    }
    return $this->_parent_folder;
  }

  /**
   * Contains permissions for this object.
   * Generally returns the {@link parent_folder()}; used by {@link USER::
   * is_allowed()} to determine access permissions.
   * @return FOLDER
   */
  public function security_context ()
  {
    return $this->parent_folder ();
  }

  /**
   * The id of the parent folder.
   * Allows connection to this object's folder without actually loading the object
   * for that folder from the database. Use {@link parent_folder()} to get the actual
   * folder object.
   * @return integer
   */
  public function parent_folder_id ()
  {
    $folder = $this->parent_folder ();
    if (isset($folder))
    {
      return $folder->id;
    }
    
    return 0;
  }

  /**
   * Move the object to the specified folder.
   * Logged-in user must have the proper security clearance to perform the
   * action. If the folder is the same as the current {@link parent_folder()},
   * the function does nothing.
   * @param FOLDER $folder
   * @param FOLDER_OPERATION_OPTIONS $options
   */
  public function move_to ($folder, $options)
  {
    $parent = $this->parent_folder ();
    if (! $parent->equals ($folder))
    {
      $privilege_set = $this->_privilege_set ();
      if ($this->login->is_allowed ($privilege_set, Privilege_delete, $this->parent_folder ()) && 
        $this->login->is_allowed ($privilege_set, Privilege_create, $folder))
      {
        $this->_move_to ($folder, $options);
      }
      else
      {
        $msg = 'Could not move ' . get_class ($this) . '[' . $this->title_as_plain_text () . '] to folder [' . $folder->title_as_plain_text () . '] (insufficent privileges).';
        if ($options->raise_on_security_failure)
        {
          $this->raise ($msg, 'move_to', 'OBJECT_IN_FOLDER');
        }
        else
        {
          log_message ($msg, Msg_type_debug_warning, Msg_channel_system);
        }
      }
    }
  }

  /**
   * Copy the object to the specified folder.
   * Logged-in user must have the proper security clearance to perform the
   * action. If the folder is the same as the current {@link parent_folder()},
   * the function makes an exact copy (except for {@link $id}).
   * @param FOLDER $folder
   * @param FOLDER_OPERATION_OPTIONS $options
   */
  public function copy_to ($folder, $options)
  {
    $privilege_set = $this->_privilege_set ();
    if ($this->login->is_allowed ($privilege_set, Privilege_create, $folder))
    {
      $this->_copy_to ($folder, $options);
    }
    else
    {
      $msg = 'Could not copy ' . get_class ($this) . '[' . $this->title_as_plain_text () . '] to folder [' . $folder->title_as_plain_text () . '] (insufficent privileges).';
      if ($options->raise_on_security_failure)
      {
        $this->raise ($msg, 'copy_to', 'OBJECT_IN_FOLDER');
      }
      else
      {
        log_message ($msg, Msg_type_debug_warning, Msg_channel_system);
      }
    }
  }

  /**
   * Flag the object as deleted.
   * Does not remove the object from the database.
   * @param boolean $update_now Actualize the database?
   */
  public function delete ($update_now = true)
  {
    $this->set_state (Deleted, $update_now);
  }

  /**
   * Un-delete the object.
   * This has the same effect as 'show'.
   * @param boolean $update_now Actualize the database?
   */
  public function restore ($update_now = true)
  {
    $this->set_state (Visible, $update_now);
  }

  /**
   * Set the object as visible.
   * All users with 'view' rights in this object's folder will be able to see it.
   * @param boolean $update_now Actualize the database?
   */
  public function show ($update_now = true)
  {
    $this->set_state (Visible, $update_now);
  }

  /**
   * Set the object as hidden.
   * Only users with 'view invisible' rights in this folder will be able to see it.
   * @param boolean $update_now Actualize the database?
   */
  public function hide ($update_now = true)
  {
    $this->set_state (Hidden, $update_now);
  }

  /**
   * Set the object as locked.
   * This object is visible, but has the special status of 'locked'. What this means
   * varies with the object type.
   * @param boolean $update_now Actualize the database?
   */
  public function lock ($update_now = true)
  {
    $this->set_state (Locked, $update_now);
  }

  /**
   * @param integer $state Change to this state.
   * @param boolean $update_now Actualize the database?
   */
  public function set_state ($state, $update_now)
  {
    if ($this->state != $state)
    {
      if ($this->exists ())
      {
        if ($update_now)
        {
          $history_item = $this->new_history_item ();
          $history_item->kind = $this->history_item_kind_for_transition_to ($state);
          $this->state = $state;
          $this->store_if_different ($history_item);
          $this->_state_changed ();
        }
      }

      $this->state = $state;
    }
  }

  /**
   * Which kind of history item does this state change generate?
   * @param integer $state
   * @return string
   */
  public function history_item_kind_for_transition_to ($state)
  {
    switch ($state)
    {
    case Deleted:
      return History_item_deleted;
    case Hidden:
      return History_item_hidden;
    case Visible:
      if ($this->invisible ())
      {
        return History_item_restored;
      }

      return History_item_updated;
    case Locked:
      return History_item_locked;
    default:
      if ($this->invisible ())
      {
        if ($state & Invisible)
        {
          return History_item_hidden_update;
        }

        return History_item_updated;
      }

      return History_item_updated;
    }
  }

  /**
   * Return a set of default options for moving this object.
   * @return FOLDER_OPERATION_OPTIONS
   */
  public function make_move_options ()
  {
    return new FOLDER_OPERATION_OPTIONS ();
  }

  /**
   * @param DATABASE $db Database from which to load values.
   */
  public function load ($db)
  {
    parent::load ($db);
    $this->state = $db->f ('state');
    $this->owner_id = $db->f ('owner_id');
    $this->_stored_state = $this->state;
    $this->_state_when_loaded = $this->state;
  }

  /**
   * @param SQL_STORAGE $storage Store values to this object.
   */
  public function store_to ($storage)
  {
    parent::store_to ($storage);
    $table_name = $this->table_name ();
    $storage->add ($table_name, 'state', Field_type_integer, $this->state);
    $storage->add ($table_name, 'owner_id', Field_type_integer, $this->owner_id);
  }

  /**
   * Final preparation before storing to the database.
   * @access private
   */
  protected function _pre_store ()
  {
    parent::_pre_store ();

    if (isset ($this->_stored_state) && ($this->_stored_state != $this->state))
    {
      $this->_state_changed ();
    }

    if (! $this->exists ())
    {
      $this->owner_id = $this->login->id;
    }

    $this->_stored_state = $this->state;
  }

  /**
   * Apply any changes needed when the state changes.
   * This is useful for objects that need to synchronize sub-objects with their own states.
   * @access private
   */
  protected function _state_changed () {}

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
      case Handler_history_item:
        include_once ('webcore/obj/webcore_history_items.php');
        return new OBJECT_IN_FOLDER_HISTORY_ITEM ($this->app);
      case Handler_location:
        include_once ('webcore/gui/location_renderer.php');
        return new OBJECT_IN_FOLDER_LOCATION_RENDERER ($this->context);
      default:
        return parent::_default_handler_for ($handler_type, $options);
    }
  }

  /**
   * Create a class-specific history item query.
   * @return OBJECT_IN_FOLDER_HISTORY_ITEM_QUERY
   * @access private
   */
  protected function _make_history_item_query ()
  {
    $class_name = $this->app->final_class_name ('OBJECT_IN_FOLDER_HISTORY_ITEM_QUERY', 'webcore/db/history_item_query.php');
    return new $class_name ($this->app);
  }

  /**
   * Set the containing folder for the object.
   * Use this method only to attach the current folder to an object. To
   * move an object to another folder, use {@link move_to()} instead.
   * @access private
   */
  public function set_parent_folder ($folder)
  {
    $this->_parent_folder = $folder;
  }

  /**
   * Load the folder or raise an exception.
   * @return FOLDER
   * @access private
   */
  protected function _load_parent_folder ()
  {
    $this->raise ("Parent folder for [$this->title] is not set.", '_load_parent_folder', 'OBJECT_IN_FOLDER');
  }

  /**
   * Return an icon used by {@link state_as_icon()}.
   * @return string
   * @access private
   */
  public function state_icon_url ()
  {
    switch ($this->state)
    {
    case Visible:
      return '{icons}buttons/view';
    case Deleted:
      return '{icons}buttons/delete';
    case Locked:
      return '{icons}indicators/locked';
    case Hidden:
      return '{icons}indicators/invisible';
    default:
      if ($this->state & Invisible)
      {
        return '{icons}indicators/invisible';
      }

      return '{icons}indicators/unknown';
    }
  }

  /**
   * Move the object to the specified folder.
   * Called from {@link move_to()} after checking security privileges.
   * @param FOLDER $folder
   * @param FOLDER_OPERATION_OPTIONS $options
   */
  protected function _move_to ($folder, $options)
  {
    if ($options->update_now)
    {
      $history_item = $this->new_history_item ();
      $this->set_parent_folder ($folder);
      $this->store_if_different ($history_item);
    }
    else
    {
      $this->_parent_folder = $folder;
    }
  }

  /**
   * Copy the object to the specified folder.
   * Called from {@link copy_to()} after checking security privileges. This
   * object becomes the copied object -- if {@link
   * FOLDER_OPERATION_OPTIONS::$update_now} is <code>False</code>, the
   * object is simply cloned, but not stored.
   * @param FOLDER $folder
   * @param FOLDER_OPERATION_OPTIONS $options
   */
  protected function _copy_to ($folder, $options)
  {
    $this->initialize_as_new ();
    if ($options->update_now)
    {
      $history_item = $this->new_history_item ();
      $this->set_parent_folder ($folder);
      $this->store_if_different ($history_item);
    }
    else
    {
      $this->_parent_folder = $folder;
    }
  }

  /**
   * Name of the {@link FOLDER_PERMISSIONS} to use for this object.
   * Used by functions like {@link move_to()}, {@link delete()}, {@link purge()}
   * and others to apply security.
   * @access private
   * @abstract
   */
  protected abstract function _privilege_set ();

  /**
   * Copy properties from the given object.
   * @param OBJECT_IN_FOLDER $other
   * @access private
   */
  protected function copy_from ($other)
  {
    parent::copy_from($other);
    $this->_parent_folder = $other->_parent_folder;
  }

  /**
   * @var FOLDER
   * @access private
   */
  protected $_parent_folder;

  /**
   * Retains the state in the database.
   * If this state differs the current state when the object is stored, a call to {@link _state_changed()}
   * is triggered, allowing descendents to adjust their sub-objects if necessary.
   * @var integer
   * @access private
   */
  protected $_state_when_loaded;
}

/**
 * Options used when moving/copying objects within {@link FOLDER}s.
 * @see OBJECT_IN_FOLDER::move_to()
 * @package webcore
 * @subpackage obj
 * @version 3.6.0
 * @since 2.5.0
 */
class FOLDER_OPERATION_OPTIONS
{
  /**
   * Object should have same access permission after move.
   * If the object inherits permissions from its current container, it must define its own set
   * in order to retain the same access control, since the new container may not have the same
   * set of permissions as the previous one. This option is only used if {@link $update_now}
   * is true.
   * @var boolean
   */
  public $maintain_permissions = false;

  /**
   * Store newly created objects as {@link Draft}s.
   * Objects that do not descend from {@link DRAFTABLE_ENTRY} ignore this
   * option.
   * @var boolean
   */
  public $copy_as_draft = false;

  /**
   * Object should be stored immediately.
   * If this is false, the change to the folder location will not be stored to the database
   * until {@link store()} or {@link store_if_different()} is called.
   * @var boolean
   */
  public $update_now = true;

  /**
   * Raise an error if security settings prevent the action.
   * If <code>False</code>, logs an error (failing silently) and continues the
   * operation on other objects, if any.
   * @var boolean
   */
  public $raise_on_security_failure = false;
}