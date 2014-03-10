<?php

/**
 * @copyright Copyright (c) 2002-2014 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage obj
 * @version 3.5.0
 * @since 2.4.0
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
require_once ('webcore/obj/unique_object.php');

/**
 * An object which tracks a change history.
 * These objects provide quick access to creator/time created and modifier/last modified
 * information. In addition, all changes made to the object are stored as {@link HISTORY_ITEM}s,
 * retaining a complete history of the object. These history items are also used when automatically
 * publishing content out of a WebCore application: they are used to determine which events
 * have occurred within the system.
 * @package webcore
 * @subpackage obj
 * @version 3.5.0
 * @since 2.4.0
 * @abstract
 */
abstract class AUDITABLE extends UNIQUE_OBJECT
{
  /**
   * Time the object was created.
   * Cached within the object for quick retrieval.
   * @var DATE_TIME
   */
  public $time_created;

  /**
   * Last time the object was modified
   * Cached within the object for quick retrieval.
   * @var DATE_TIME
   * @see modified()
   */
  public $time_modified;

  /**
   * ID of the user that created the object.
   * @var integer
   * @see creator()
   */
  public $creator_id;

  /**
   * ID of the user that last modified the object.
   * @var integer
   * @see modifier()
   */
  public $modifier_id;

  /**
   * If true, the modifier is automatically set to the {@link $login} when changes are made.
   *
   * @var boolean
   */
  public $update_modifier_on_change = true;

  /**
   * @param APPLICATION $app Main application.
   */
  public function __construct ($app)
  {
    parent::__construct ($app);

    $this->time_created = $app->make_date_time ();
    $this->time_modified = $app->make_date_time ();
  }

  /**
   * Has this object been changed since it was created?
   * @return boolean
   */
  public function modified ()
  {
    return ! $this->time_created->equals ($this->time_modified);
  }

  /**
   * @return USER
   */
  public function creator ()
  {
    return $this->app->user_at_id ($this->creator_id);
  }

  /**
   * @return USER
   */
  public function modifier ()
  {
    return $this->app->user_at_id ($this->modifier_id);
  }

  /**
   * Title for an associated history item.
   * Override this function to show other information for an history item's object in the title. The subscriber
   * for whom the title is prepared is also sent, so that any subscriber settings can be used to render the
   * title.
   * @param ENTRY $obj
   * @param SUBSCRIBER $subscriber
   * @return string
   */
  public function title_for_history_item ($obj, $subscriber)
  {
    $type_info = $this->type_info ();
    $type_info->singular_title;
    $kind = $obj->supported_kind_as_text ();
    if ($kind)
    {
      $Result = $type_info->singular_title . ' ' . $kind;
    }
    else
    {
      if ($subscriber->show_history_item_as_subject)
      {
        $Result = $obj->title_as_plain_text ();
      }
      else
      {
        $Result = $type_info->singular_title . ' updated';
      }
    }

    $location = $this->object_url_as_text ();
    return $Result . " ($location)";
  }

  /**
   * State of item when created.
   * @return string
   */
  public function history_item_kind_for_new ()
  {
    return History_item_created;
  }

  /**
   * List of subscribers for this object.
   * @param HISTORY_ITEM $history_item Action that generated this request. May be empty.
   * @return SUBSCRIPTION_QUERY
   */
  public function subscriber_query ($history_item = null)
  {
    $class_name = $this->app->final_class_name ('SUBSCRIPTION_QUERY', 'webcore/db/subscriber_query.php');
    $Result = new $class_name ($this->app);
    $this->_prepare_subscription_query ($Result, $history_item);

    return $Result;
  }

  /**
   * Query for history items for this user.
   * @return HISTORY_ITEM_QUERY
   */
  public function history_item_query ()
  {
    $Result = $this->_make_history_item_query ();
    $Result->restrict ("object_id = $this->id");
    $history_item = $this->handler_for (Handler_history_item);
    $this->assert (! empty ($history_item->object_type), 'object_type cannot be empty.', 'history_item_query', 'AUDITABLE');
    $Result->restrict ("object_type = '$history_item->object_type'");
    return $Result;
  }

  /**
   * Retrieve a new history item for this object.
   * Return an class-specific object which can be used with {@link store_audited()}.
   * @return HISTORY_ITEM
   */
  public function new_history_item ()
  {
    $Result = $this->handler_for (Handler_history_item);
    $Result->user_id = $this->login->id;
    $Result->set_object ($this);
    return $Result;
  }

  /**
   * Set up this object so it will {@link store()} a new object.
   */
  public function initialize_as_new ()
  {
    parent::initialize_as_new ();
    $this->time_created->clear ();
    $this->time_modified->clear ();
  }

  /**
   * @param DATABASE $db Database from which to load values.
   */
  public function load ($db)
  {
    parent::load ($db);
    $this->time_created->set_from_iso ($db->f ('time_created'));
    $this->time_modified->set_from_iso ($db->f ('time_modified'));
    $this->creator_id = $db->f ('creator_id');
    $this->modifier_id = $db->f ('modifier_id');
  }

  /**
   * Stores this object if the history item warrants it.
   * The object is only stored if there are differences from 'history item'.
   * @param HISTORY_ITEM $history_item
   */
  public function store_as_is_if_different ($history_item)
  {
    $this->_store_if_different ($history_item, 'store_as_is');
  }

  /**
   * Store this object without updating audit information.
   * this function should *only* be used when importing an object from
   * an external source. For example, if there are objects from another
   * WebCore system, or if there are objects externally generated and
   * verified by an XML importer. Using this function makes the assumption
   * that the 'creator' and 'modifier' have been properly set already.
   */
  public function store_as_is ()
  {
    parent::store ();
  }

  /**
   * Stores this object if the history item warrants it.
   * The object is only stored if there are differences from 'history item'.
   * @param HISTORY_ITEM $history_item
   */
  public function store_if_different ($history_item)
  {
    $this->_store_if_different ($history_item, 'store');
  }

  /**
   * @param SQL_STORAGE $storage Store values to this object.
   */
  public function store_to ($storage)
  {
    parent::store_to ($storage);
    $tname = $this->table_name ();
    $storage->add ($tname, 'time_created', Field_type_date_time, $this->time_created, Storage_action_create);
    $storage->add ($tname, 'creator_id', Field_type_integer, $this->creator_id, Storage_action_create);
    $storage->add ($tname, 'time_modified', Field_type_date_time, $this->time_modified);
    $storage->add ($tname, 'modifier_id', Field_type_integer, $this->modifier_id);
  }

  /* Final preparation before storing to the database.
   * @access private
   */
  protected function _pre_store ()
  {
    parent::_pre_store ();

    if (! $this->exists ())
    {
      $this->modifier_id = $this->login->id;
      $this->time_modified->set_now ();
      
      $this->creator_id = $this->login->id;
      $this->time_created->set_now ();
    }
    elseif ($this->state != Abandoned)
    {
      // Abandoned items should be abandoned "in-place"; the abandoner is retained in the item's history.
      
      if ($this->update_modifier_on_change)
      {
        $this->modifier_id = $this->login->id;
      }
      
      $this->time_modified->set_now ();
    } 
  }

  /**
   * Stores this object if the history item warrants it.
   * The object is only stored if there are differences from 'history item'. If the object needs to be stored, the
   * function specified in the second parameter is called.
   * @param HISTORY_ITEM $history_item
   * @param string $store_func_name
   * @access private
   */
  protected function _store_if_different ($history_item, $store_func_name)
  {
    $history_item->record_differences ($this);

    if ($history_item->differences_exist ())
    {
      $exists = $this->exists ();
      $this->$store_func_name ();

      /* If the object was just created, then the history item doesn't have the
       * proper object id stored.
       */

      if (! $exists)
      {
        $history_item->update_object ($this);
      }

      $history_item->store ();
    }
  }

  /**
   * Copy properties from the given object.
   * @param AUDITABLE $other
   * @access private
   */
  protected function copy_from ($other)
  {
    parent::copy_from($other);
    $this->time_created = clone ($other->time_created);
    $this->time_modified = clone ($other->time_modified);
  }

  /**
   * @param PURGE_OPTIONS $options
   * @access private
   */
  protected function _purge ($options)
  {
    /* Remove history items for self */
    $history_item = $this->handler_for (Handler_history_item);
    $this->assert (! empty ($history_item->object_type), 'object_type cannot be empty.', '_purge', 'AUDITABLE');

    $this->db->logged_query ("DELETE LOW_PRIORITY FROM {$this->app->table_names->history_items} WHERE object_id = $this->id AND object_type = '$history_item->object_type'");

    parent::_purge ($options);
  }

  /**
   * Create a class-specific history item query.
   * @return HISTORY_ITEM_QUERY
   * @access private
   */
  protected function _make_history_item_query ()
  {
    include_once ('webcore/db/history_item_query.php');
    return new HISTORY_ITEM_QUERY ($this->app);
  }

  /**
   * Apply class-specific restrictions to this query.
   * @param SUBSCRIPTION_QUERY $query
   * @param HISTORY_ITEM $history_item Action that generated this request. May be empty.
   * @access private
   */
  protected function _prepare_subscription_query ($query, $history_item)
  {
    // NOP
  }
}

?>