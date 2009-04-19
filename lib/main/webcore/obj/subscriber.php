<?php

/**
 * @copyright Copyright (c) 2002-2009 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage obj
 * @version 3.1.0
 * @since 2.2.1
 */

/****************************************************************************

Copyright (c) 2002-2009 Marco Von Ballmoos

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

define ('Subscriptions_disabled', -1);

/** */
require_once ('webcore/obj/unique_object.php');
require_once ('webcore/sys/date_time.php');

/**
 * A subscriber for content.
 * The subscriber can be associated with a registered user, but doesn't have to be.
 * Each subscriber has 0 or more subscriptions in addition to the options shown.
 *
 * This object can be used in a 'read-only' form, with only the 'email'
 * property assigned. This is sufficient to read subscription information
 * If told to create/delete a subscription, it will synchronize with the database
 * and either find the existing id for the given email or create a new record.
 * @package webcore
 * @subpackage obj
 * @version 3.1.0
 * @since 2.2.1
 */
class SUBSCRIBER extends UNIQUE_OBJECT
{
  /**
   * @var string
   */
  public $email = '';

  /**
   * Send messages as HTML or plain text?
   * @var boolean
   */
  public $send_as_html = true;

  /**
   * Send messages for items modified only by this subscriber?
   * If this subscriber is also a {@link USER} and that user is the only modifier for an item,
   * should a message for that item be sent?
   * @var boolean
   */
  public $send_own_changes = true;

  /**
   * Send at most this many objects individually.
   * If there are more than this many objects queued for a subscriber, the objects are grouped
   * into one item. This is an option to control spamming, so that if a larger update is generated,
   * the {@link PUBLISHER} automatically groups objects to keep the total number of messages
   * reasonable.
   * @var integer
   */
  public $max_individual_messages = 5;

  /**
   * Send at most this many objects in one message item.
   * Set this object control the size of generated emails.
   * @var integer
   */
  public $max_items_per_message = 25;

  /**
   * Wait this many hours between sending batches of items.
   * If this is 0, content is always sent as soon as the {@link PUBLISHER} processes it. If this is non-zero,
   * then the publisher checks when messages were last sent to the subscriber. If enough time has
   * elapsed, the current messages are sent. If not, the {@link HISTORY_ITEM}s that generated the items
   * are stored with the subscriber until the next publishing cycle.
   * @var integer
   */
  public $min_hours_to_wait = 0;

  /**
   * When were messages last sent to this subscriber?
   * This field is used with {@link $min_hours_to_wait} to determine when messages should once again
   * be sent.
   * @var DATE_TIME
   */
  public $time_messages_sent;

  /**
   * Send multiply-modified item as one message?
   * If an item has been modified several times since the last messages were sent, should all
   * changes be grouped into one message?
   * @var boolean
   */
  public $group_history_items = true;

  /**
   * Should details of the exact kind of modification be sent?
   * Each modification of an item is recorded with an {@link HISTORY_ITEM}. Should the details of the
   * history item be included in the email?
   * @var boolean
   */
  public $show_history_items = false;

  /**
   * Should an history item title serve as the email title?
   * Each modification of an item is recorded with an {@link HISTORY_ITEM}. If a message contains only
   * one object and one or more history items, the history item's title can be shown
   * in the subject (can help quickly identify exactly what type of mail it is).
   * If this is false, a generic 'Object updated' message is used for the
   * subject, along with the object's path.
   * @var boolean
   */
  public $show_history_item_as_subject = true;

  /**
   * List of {@link HISTORY_ITEM}s queued for this subscriber.
   * If a publication cycle is execution by {@link PUBLISHER}, but this user is not 
   * yet ready for messages (as calculated with {@link $time_messages_sent} and
   * {@link min_hours_to_wait}), the list of historyitems from that publication
   * cycle are stored with the subscriber, so they can be sent when the
   * subscriber is ready to receive them.
   *
   * The list is comma-separated and new ids are appended to any existing ids.
   * @var string
   */
  public $queued_history_item_ids;

  /**
   * How much text from an object should be shown?
   * This is a suggestion to the email renderer to limit larger texts. If zero, all text is shown.
   * @var integer
   */
  public $preferred_text_length = 0;

  /**
   * @param APPLICATION $app
   */
  public function __construct ($app)
  {
    parent::__construct ($app);
    $this->time_messages_sent = $app->make_date_time ();
  }

  /**
   * @return boolean
   */
  public function exists ()
  {
    if ($this->email)
    {
      $this->synchronize ();
    }
    return parent::exists ();
  }

  /**
   * Should messages be sent or queued for this user?
   * A subscriber can retain subscriptions, but disable actually sending notifications. This means that messages
   * ordinarily triggered by subscriptions are neither sent nor queued in the {@link $queued_history_item_ids}.
   * @return boolean
   */
  public function enabled ()
  {
    return $this->min_hours_to_wait != Subscriptions_disabled;
  }

  /**
   * Can messages be sent to this subscriber?
   * This function returns false if {@link $min_hours_to_wait} have not elapsed since messages were last
   * sent to this user.
   * @return boolean
   */
  public function ready_for_messages ()
  {
    if ($this->time_messages_sent->is_valid ())
    {
      $php_time_sent = $this->time_messages_sent->as_php ();
      return ($php_time_sent + ($this->min_hours_to_wait * 3600)) < time ();
    }

    return $this->min_hours_to_wait != Subscriptions_disabled;
  }

  /**
   * Is this a queued history item for this subscriber?
   * @param integer $id
   * @return boolean
   */
  public function is_queued_history_item ($id)
  {
    if ($this->queued_history_item_ids)
    {
      if (! isset ($this->_queued_history_items))
      {
        $this->_queued_history_items = explode (',', $this->queued_history_item_ids);
      }

      return in_array ($id, $this->_queued_history_items);
    }
    
    return false;
  }

  /**
   * Add history items to the subscriber's queue.
   * If {@link ready_for_messages()} returns false, the {@link PUBLISHER} adds the history items to the subscriber's
   * queue, to be processed when the subscriber is ready.
   * @param string $id Comma-separated list of history item ids.
   */
  public function add_queued_history_items ($id)
  {
    if (isset ($this->queued_history_item_ids) && $this->queued_history_item_ids)
    {
      $this->queued_history_item_ids .= ',' . $id;
    }
    else
    {
      $this->queued_history_item_ids = $id;
    }

    $id_array = explode (',', $this->queued_history_item_ids);
    $id_array = array_unique ($id_array);
    $this->queued_history_item_ids = implode (',', $id_array);
  }

  /**
   * Remove all queued history items.
   * Also marks the last time messages were sent.
   */
  public function clear_queued_history_items ()
  {
    $this->queued_history_item_ids = '';
    $this->time_messages_sent->set_now ();
  }

  /**
   * The user record associated with this subscriber.
   * Subscribers can be either anonymous or registered users. If the subscriber is
   * registered, the associated user object is returned here.
   * @return USER
   */
  public function user ()
  {
    if (! isset ($this->_user))
    {
      $user_query = $this->app->user_query ();
      $user_query->set_kind (Privilege_kind_registered);
      $this->_user = $user_query->object_at_email ($this->email);
    }

    return $this->_user;
  }

  /**
   * (Un)Subscribe to the object indicated by the "id" and "kind".
   * @see subscribe()
   * @see unsubscribe()
   * @param integer $id Unique id for the subscribed object.
   * @param integer $kind Can be any of the {@link Subscribe_constants}.
   * @param boolean $enabled Turn the subscription on or off.
   */
  public function set_subscribed ($obj, $kind, $enabled)
  {
    if ($enabled)
    {
      if (! $this->subscribed ($obj, $kind))
      {
        $this->subscribe ($obj->id, $kind);
      }
    }
    else
    {
      $this->unsubscribe ($obj->id, $kind);
    }
  }

  /**
   * Subscribe to the object indicated by the "id" and "kind".
   * @param integer $id Unique id for the subscribed object.
   * @param integer $kind Can be any of the {@link Subscribe_constants}.
   */
  public function subscribe ($id, $kind)
  {
    $this->synchronize ();

    if (! $this->exists ())
    {
      $this->store ();
    }

    $this->db->logged_query ("INSERT INTO {$this->app->table_names->subscriptions}" .
                             " VALUES ($this->id, '$kind', $id, 1, 1)");
  }

  /**
   * Unsubscribe from the object indicated by the "id" and "kind".
   * @param integer $id Unique id for the subscribed object.
   * @param integer $kind Can be any of the {@link Subscribe_constants}.
   */
  public function unsubscribe ($id, $kind)
  {
    $this->synchronize ();

    if ($this->exists ())
    {
      $this->db->logged_query ("DELETE FROM {$this->app->table_names->subscriptions}" .
                               " WHERE subscriber_id = $this->id" .
                               " AND kind = '$kind'" .
                               " AND ref_id = $id");
    }
  }

  /**
   * Is this person directly subscribed to this object?
   * Here we only need the email to check. This allows us to check subscriptions
   * through a SUBSCRIBER object created from a USER without querying the
   * database an extra time for subscriber info.
   * @param AUDITABLE $obj Reference to the object to check.
   * @param integer $kind Can be any of the {@link Subscribe_constants}.
   * @return boolean
   */
  public function subscribed ($obj, $kind)
  {
    return in_array ($kind, $this->receives_notifications_through ($obj));
  }
  
  /**
   * How is the user subscribed to this object?
   * Returns a list of {@link Subscribe_constants} that match this person and the
   * given object.
   * @param AUDITABLE $obj
   * @return array[integer]
   */
  public function receives_notifications_through ($obj)
  {
    $Result = array ();
    if (isset ($this->email) && $obj->exists ())
    {
      $query = $obj->subscriber_query ();
      $query->restrict ("subscribers.email = '$this->email'");
      $query->set_select ('subs.kind as subkind');
      $db = $query->raw_output ();
      while ($db->next_record ())
        $Result [] = $db->f ('subkind');
    }
    return $Result;
  }
  
  /**
   * Query for all subscriptions for this user.
   * @return SUBSCRIPTION_QUERY
   */
  public function subscription_query ()
  {
    $class_name = $this->app->final_class_name ('SUBSCRIPTION_QUERY', 'webcore/db/subscriber_query.php');
    $Result = new $class_name ($this->app);
    $Result->set_select ('subs.*');
    $Result->restrict_text ('subscribers.email', $this->email);
    return $Result;
  }

  /**
   * Return this list of subscribed ids for the given type.
   * @param string $kind Can be any one of the {@link Subscribe_constants}.
   * @param string $type Type of entry to find in multiple-entry-type modules.
   * @return array[integer]
   */
  public function subscribed_ids_for ($kind, $type = '')
  {
    $query = $this->subscription_query ();
    
    switch ($kind)
    {
      case Subscribe_folder:
        $query->add_table ($this->app->table_names->folders . ' fldr', 'fldr.id = subs.ref_id');
        $query->restrict_text ('subs.kind', Subscribe_folder);
        $query->set_select ('fldr.id');
        break;
      case Subscribe_entry:
        $query->add_table ($this->app->table_names->entries . ' entry', 'entry.id = subs.ref_id');
        $query->restrict_text ('subs.kind', Subscribe_entry);
        $query->set_select ('entry.id');    
        $type_infos = $this->app->entry_type_infos ();
        if ($type && (sizeof ($type_infos) > 1))
        {
          $query->restrict_text ('entry.type', $type);
        }
        break;
      case Subscribe_user:
        $query->add_table ($this->app->table_names->users . ' usr', 'usr.id = subs.ref_id');
        $query->restrict_text ('subs.kind', Subscribe_user);
        $query->set_select ('usr.id');
        break;    
    }    

    $Result = array ();

    $db = $query->raw_output ();
    while ($db->next_record ())
    {
      $id = $db->f ("id");
      $Result [$id] = $id;
    }

    return $Result;
  }

  /**
   * Should this history item trigger a notification?
   * This uses the {@link $send_own_changes} option.
   * @param HISTORY_ITEM $history_item
   * @return boolean
   */
  public function wants_notification ($history_item)
  {
    $Result = $this->send_own_changes;

    if (! $Result)
    {
      $creator = $history_item->creator ();
      $Result = ($this->email != $creator->email);
    }

    return $Result;
  }
  
  /**
   * Replace subscriptions for the given type.
   * Pass in a list of ids to which this user should be subscribed.
   * @param string $kind Can be any one of the {@link Subscribe_constants}.
   * @param array[integer] $selected_ids List of ids to replace the existing
   * set.
   * @param string $type Type of entry to find in multiple-entry-type modules.
   * (used only for entries).
   */
  public function update_subscriptions_for ($kind, $selected_ids, $type = '')
  {
    $original_ids = $this->subscribed_ids_for ($kind, $type);
    
    if (! empty ($selected_ids))
    {
      foreach ($selected_ids as $id)
      {
        if (! in_array ($id, $original_ids))
        {
          $this->subscribe ($id, $kind);
        }
      }
    }

    if (! empty ($original_ids))
    {
      foreach ($original_ids as $id)
      {
        if (! in_array ($id, $selected_ids))
        {
          $this->unsubscribe ($id, $kind);
        }
      }
    }
  }

  /**
   * @return string
   */
  public function raw_title ()
  {
    return $this->email;
  }

  /**
   * @param DATABASE $db Database from which to load values.
   */
  public function load ($db)
  {
    parent::load ($db);
    $this->send_as_html = $db->f ('send_as_html');
    $this->preferred_text_length = $db->f ('preferred_text_length');
    $this->send_own_changes = $db->f ('send_own_changes');
    $this->max_individual_messages = $db->f ('max_individual_messages');
    $this->max_items_per_message = $db->f ('max_items_per_message');
    $this->min_hours_to_wait = $db->f ('min_hours_to_wait');

    $this->group_history_items = $db->f ('group_history_items');
    $this->show_history_items = $db->f ('show_history_items');
    $this->show_history_item_as_subject = $db->f ('show_history_item_as_subject');

    $this->time_messages_sent->set_from_ISO ($db->f ('time_messages_sent'));
    $this->queued_history_item_ids = $db->f ('queued_history_item_ids');

    $this->email = $db->f ('email');
    $this->_read_only = false;
  }

  /**
   * @param SQL_STORAGE $storage Store values to this object.
   */
  public function store_to ($storage)
  {
    parent::store_to ($storage);
    $tname = $this->table_name ();
    $storage->add ($tname, 'send_as_html', Field_type_boolean, $this->send_as_html);
    $storage->add ($tname, 'preferred_text_length', Field_type_integer, $this->preferred_text_length);
    $storage->add ($tname, 'send_own_changes', Field_type_boolean, $this->send_own_changes);
    $storage->add ($tname, 'max_individual_messages', Field_type_integer, $this->max_individual_messages);
    $storage->add ($tname, 'max_items_per_message', Field_type_integer, $this->max_items_per_message);
    $storage->add ($tname, 'min_hours_to_wait', Field_type_integer, $this->min_hours_to_wait);

    $storage->add ($tname, 'group_history_items', Field_type_boolean, $this->group_history_items);
    $storage->add ($tname, 'show_history_items', Field_type_boolean, $this->show_history_items);
    $storage->add ($tname, 'show_history_item_as_subject', Field_type_boolean, $this->show_history_item_as_subject);

    $storage->add ($tname, 'time_messages_sent', Field_type_date_time, $this->time_messages_sent);
    $storage->add ($tname, 'queued_history_item_ids', Field_type_string, $this->queued_history_item_ids);

    $storage->add ($tname, 'email', Field_type_string, $this->email);
  }

  public function store ()
  {
    $this->synchronize ();
    parent::store ();
    $this->_read_only = false;
  }

  /**
   * Name of the home page name for this object.
   * @return string
   */
  public function page_name ()
  {
    return $this->app->page_names->user_subscriptions_home;
  }
  
  /**
   * Arguments to the home page url for this object.
   * @return string
   */
  public function page_arguments ()
  {
    return 'email=' . urlencode ($this->email);
  }

  /**
   * Name of this object's database table.
   * @return string
   * @access private
   */
  public function table_name ()
  {
    return $this->app->table_names->subscribers;
  }

  /**
   * @param PURGE_OPTIONS $options
   * @access private
   */
  protected function _purge ($options)
  {
    if ($this->email)
    {
      $this->synchronize ();
    }

    $this->db->logged_query ("DELETE LOW_PRIORITY FROM {$this->app->table_names->subscriptions} WHERE subscriber_id = $this->id");

    parent::_purge ($options);
  }

  /**
   * Make the subscriber writable, if possible.
   * If this subscriber is read-only (created by a USER object to retrieve
   * subscriptions), then query now for the id of the subscriber. If the record
   * is not found, then create the database record.
   * @access private
   */
  public function synchronize ()
  {
    $this->assert (! empty ($this->email), "Email for subscriber [$this->id] is empty.", 'synchronize', 'SUBSCRIBER');
    if ($this->_read_only)
    {
      $this->_read_only = false;

      $this->db->logged_query ("SELECT * FROM {$this->app->table_names->subscribers} WHERE email = '$this->email'");
      if ($this->db->next_record ())
      {
        $this->load ($this->db);
      }
    }
  }

  /**
   * Can this subscriber be modified (without going to the database for more info)?
   * See the class description for more information.
   * @var boolean
   * @access private
   */
  protected $_read_only = true;

  /**
   * Queued history items as an array of ids.
   * @var array[integer]
   */
  protected $_queued_history_items;
}

?>