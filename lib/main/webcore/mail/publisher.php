<?php

/**
 * @copyright Copyright (c) 2002-2010 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage mail
 * @version 3.3.0
 * @since 2.2.1
 */

/****************************************************************************

Copyright (c) 2002-2010 Marco Von Ballmoos

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

require_once ('webcore/log/loggable.php');
require_once ('webcore/obj/subscriber.php');  // for a constant
require_once ('webcore/db/subscriber_query.php');
require_once ('webcore/mail/mail_object_renderer.php');
require_once ('webcore/gui/object_list_title.php');

/**
 * Handles bulk-mailing to {@link SUBSCRIBER}s.
 * @package webcore
 * @subpackage mail
 * @version 3.3.0
 * @since 2.2.1
 */
class PUBLISHER extends LOGGABLE
{
  /**
   * Run without updating the database or sending email.
   * @var boolean
   */
  public $testing = false;

  /**
   * Show contents of emails when in test mode.
   * @var boolean
   */
  public $preview = false;

  /**
   * The channel for log messages generated as publication notifications.
   * @var string
   */
  public $default_channel = Msg_channel_publisher;

  /**
   * The type of log messages generated as publication notifications.
   * @var string
   */
  public $default_type = Msg_type_info;

  /**
   * How much of the object descriptions to send?
   * @var integer
   */
  public $excerpt_length = 0;

  /**
   * Should history item details be included in messages?
   * @var boolean
   */
  public $include_history_items_in_messages = true;

  /**
   * @param MAIL_PROVIDER $provider Publish mail using this provider.
   */
  public function __construct ($provider)
  {
    parent::__construct ($provider->context);

    $this->provider = $provider;
    $this->logs->set_logger ($provider->logs->logger);

    $this->excerpt_length = $this->app->mail_options->excerpt_length;

    $this->_subscription_settings = new SUBSCRIPTION_SETTINGS ($this->app);
  }

  /**
   * Publish objects in the 'query' whose publication state matches 'state'.
   * Notifications are sent according to user preferences by default.
   * @param HISTORY_ITEM_QUERY $query
   */
  public function publish_history_items ($query)
  {
    $this->app->display_options->overridden_max_title_size = 100;

    /* Build an indexed list of all history items in the query. */

    $query->restrict ("act.publication_state = '" . History_item_needs_send . "'");
    $query->set_order ('act.access_id ASC, act.object_type ASC, act.time_created ASC');
    $query_history_items = $query->indexed_objects ();
    $num_history_items = sizeof ($query_history_items);
    $this->record ("[$num_history_items] history items found matching given query.");

    /* Retrieve a list of all subscribers that have pending history items. */

    $sub_query = new SUBSCRIBER_QUERY ($this->app);
    $sub_query->restrict ("queued_history_item_ids <> ''");
    $sub_query->restrict ('time_messages_sent + INTERVAL min_hours_to_wait HOUR <= NOW()');
    $subscribers = $sub_query->objects ();
    $this->record ("[" . sizeof ($subscribers) . "] subscribers found with pending history items.");

    /* For each subscriber that is now ready to receive messages, search the list of
       queued history items, building a global list of history items that are not already in the list
       generated by the query above. */

    $queued_history_item_ids = array ();

    foreach ($subscribers as $subscriber)
    {
      if ($subscriber->ready_for_messages ())
      {
        if ($subscriber->queued_history_item_ids)
        {
          $history_item_ids = explode (',', $subscriber->queued_history_item_ids);
          foreach ($history_item_ids as $history_item_id)
          {
            if (! isset ($query_history_items [$history_item_id]) && ! isset ($queued_history_item_ids [$history_item_id]))
            {
              $queued_history_item_ids [$history_item_id] = $history_item_id;
            }
          }
        }
      }
    }

    /* If there were any queued history items ready to send to subscribers, get them and add
       them to the list. Otherwise, use the list from the given query. */

    if (sizeof ($queued_history_item_ids))
    {
      $this->record ("[" . sizeof ($queued_history_item_ids) . "] unique pending history items found in subscribers.");
      $queued_history_item_ids_string = implode (',', $queued_history_item_ids);
      $queued_history_item_query = $this->login->all_history_item_query ();
      $queued_history_items = $queued_history_item_query->indexed_objects_at_ids ($queued_history_item_ids_string);

      if (sizeof ($query_history_items))
      {
        $history_items = $query_history_items + $queued_history_items;

        /* Used to use: ksort ($history_items, SORT_NUMERIC);, but we actually
           want a sort by folder id, object type, object id. ... */
      }
      else
      {
        $history_items = $queued_history_items;
      }
    }
    else
    {
      $history_items = $query_history_items;
    }

    $num_history_items = sizeof ($history_items);

    /* The list of history items now contains all the history items that were passed in with the query
       (this is the list of history items the application is publishing now) and all of the history items
       that are now publishable for those subscribers that are using queued delivery. */

    if ($num_history_items)
    {
      /* Build a three-dimensional array of history items, grouping them by [type][object][history_item]. */

      foreach ($history_items as $history_item_id => $history_item)
      {
        $sorted_history_items [$history_item->object_type][$history_item->object_id][] = $history_item;
      }

      /* Build a two-dimensional array of objects, grouping them by [type][object]. */

      foreach ($sorted_history_items as $object_type => $objects_and_history_items)
      {
        if ($object_type)
        {
          $objects [$object_type] = array ();
          $query = $this->_object_query_for ($object_type);

          /* If this is not set, an error is raised, but it is possible to
             ignore errors when publishing, so make sure not to attempt to
             use the query. */
          if (isset ($query))
          {
            $object_ids = array_keys ($objects_and_history_items);
            $query->restrict_by_op ($query->alias . '.id', $object_ids, Operator_in);
            $objects [$object_type] = $query->indexed_objects ();

            /* If this is the folder query, make sure to remove the restriction so that ensuing queries
              using the folder query don't inherit it. */

            $query->clear_restrictions ();
          }
        }
        else
        {
          $this->record ('Empty object type was ignored (bad data).', Msg_type_warning);
        }
      }

      /* Build a list of subscribers, with structure [email].objects[folder][type][id].history_items */

      $subscriber_records = array ();

      foreach ($sorted_history_items as $object_type => $objects_and_history_items)
      {
        foreach ($objects_and_history_items as $object_id => $obj_history_items)
        {
          foreach ($obj_history_items as $history_item)
          {
            if (isset($objects [$object_type][$object_id]))
            {
              $obj = $objects [$object_type][$object_id];
              $subscribers = $this->_subscribers_for ($history_item, $obj);
              $num_subscribers = sizeof ($subscribers);

              $this->record ($obj->title_as_plain_text () . ' (' . $history_item->title_as_plain_text () . "): [$num_subscribers] subscribers.");

              if ($num_subscribers > 0)
              {
                foreach ($subscribers as $subscriber)
                {
                  /* If the history item was from a pending history item list, make sure to
                   * only send it to subscribers who actually have the history item
                   * pending. Otherwise, it might be sent to some subscribers
                   * multiple times.
                   */

                  if (! isset ($queued_history_item_ids [$history_item->id]) || ($subscriber->is_queued_history_item ($history_item->id)))
                  {
                    /* Give the subscriber a chance to decide whether this particular
                     * history item should be sent or not. Basic filtering is already
                     * done when retrieving the subscriber list for an object,
                     * but this allows more fine-grained filtering (which would
                     * not be practical in the main query).
                     */

                    if ($subscriber->wants_notification ($history_item))
                    {
                      if (! isset ($subscriber_records [$subscriber->email]))
                      {
                        $sub_rec = new stdClass();
                        $sub_rec->subscriber = $subscriber;
                        $sub_rec->num_objects = 0;
                        $subscriber_records [$subscriber->email] = $sub_rec;
                      }

                      /* Organize the history items and objects in each subscriber. Store
                       * an extra list of all history items in the subscriber
                       * directly. This will be used later, if the subscriber is
                       * not yet ready to receive messages.
                       */

                      $subscriber_records [$subscriber->email]->history_items [$history_item->id] = $history_item;
                      $subscriber_records [$subscriber->email]->num_objects += 1;
                      $subscriber_records [$subscriber->email]->objects [$history_item->access_id][$object_type][$object_id]->object = $obj;
                      $subscriber_records [$subscriber->email]->objects [$history_item->access_id][$object_type][$object_id]->history_items [] = $history_item;
                    }
                  }
                }
              }
            }
            else
            {
              $this->record ("Object [$object_type][$object_id] was not found.", Msg_type_warning);
            }
          }
        }
      }

      /* Iterate the subscribers, adding the extant history item ids to the subscriber's
       * list in the database. Save each subscriber, so the history item selection is
       * independent of actually sending the messages.
       */

      foreach ($subscriber_records as $subscriber_rec)
      {
        $subscriber = $subscriber_rec->subscriber;

        if (sizeof ($subscriber_rec->history_items))
        {
          $history_item_ids = array_keys ($subscriber_rec->history_items);
          $num_ids = sizeof ($history_item_ids);
          $ids_for_subscriber = join (',', $history_item_ids);
          $this->record ("$subscriber->email: [$num_ids] subscribed items found.");
          if ($num_ids)
          {
            $this->record ("$subscriber->email: Queued [$ids_for_subscriber].", Msg_type_debug_info);
          }
          $subscriber->add_queued_history_items ($ids_for_subscriber);
          if (! $this->testing)
          {
            $subscriber->store ();
          }
        }
      }

      /* Since all subscribers now store the history items in which they're interested, we
       * can mark the history items as processed. If there are errors while sending
       * emails, the unsent history items will simply be retrieved with the
       * subscribers on the next publishing execution.
       */

      $this->_update_publication_status_for ($history_items);

      /* Now we have a list of subscribers, each with the list of applicable
       * objects and history items. Now that all of the ids have been replaced with
       * objects, we are ready to check subscriber options to build the
       * publisher items that will be sent to the publishing mechanism itself.*/

      foreach ($subscriber_records as $subscriber_rec)
      {
        $subscriber = $subscriber_rec->subscriber;

        if ($subscriber->ready_for_messages ())
        {
          /* Determine whether grouped messaging will be required. */

          $num_history_items = sizeof ($subscriber_rec->history_items);
          $num_objects = $subscriber_rec->num_objects;

          if ($subscriber->group_history_items)
          {
            $num_items = $num_objects;
          }
          else
          {
            $num_items = $num_history_items;
          }

          if ($subscriber->max_individual_messages && ($num_items > $subscriber->max_individual_messages))
          {
            /* Multiple messages are required, 'group_history_items' flag will be ignored. */

            $item = new PUBLISHER_MESSAGE ($this, $subscriber);
            $num_objects_in_message = 0;

            foreach ($subscriber_rec->objects as $objs_by_folder)
            {
              foreach ($objs_by_folder as $obj_types)
              {
                foreach ($obj_types as $obj_rec)
                {
                  /* Retain the last object record for use if the loop ends and there
                     is only one object in the message, we can treat it as a normal
                     single-object message, without the generic title. */

                  $last_obj_rec = $obj_rec;

                  if ($subscriber->show_history_items)
                  {
                    foreach ($obj_rec->history_items as $history_item)
                    {
                      $item->add_object ($history_item);
                    }
                  }

                  $item->add_main_object ($obj_rec->object);
                  $num_objects_in_message += 1;

                  if ($num_objects_in_message == $subscriber->max_items_per_message)
                  {
                    $item->num_items = $num_objects_in_message;
                    if ($num_objects_in_message == 1)
                    {
                      $item->set_subject ($last_obj_rec->object->title_for_history_item ($last_obj_rec->history_items [0], $subscriber));
                    }
                    $items [] = $item;

                    $item = new PUBLISHER_MESSAGE ($this, $subscriber);
                    $num_objects_in_message = 0;
                  }
                }
              }
            }

            if ($num_objects_in_message)
            {
              $item->num_items = $num_objects_in_message;
              if ($num_objects_in_message == 1)
              {
                $item->set_subject ($last_obj_rec->object->title_for_history_item ($last_obj_rec->history_items [0], $subscriber));
              }
              $items [] = $item;
            }
          }
          else
          {
            foreach ($subscriber_rec->objects as $objs_by_folder)
            {
              foreach ($objs_by_folder as $obj_types)
              {
                foreach ($obj_types as $obj_rec)
                {
                  if ($subscriber->group_history_items)
                  {
                    $item = new PUBLISHER_MESSAGE ($this, $subscriber);

                    if ($subscriber->show_history_items)
                    {
                      foreach ($obj_rec->history_items as $history_item)
                      {
                        $item->add_object ($history_item);
                      }
                    }

                    $item->add_main_object ($obj_rec->object);
                    $item->set_subject ($obj_rec->object->title_for_history_item ($obj_rec->history_items [0], $subscriber));
                    $item->num_items = 1;
                    $items [] = $item;
                  }
                  else
                  {
                    foreach ($obj_rec->history_items as $history_item)
                    {
                      $item = new PUBLISHER_MESSAGE ($this, $subscriber);
                      if ($subscriber->show_history_items)
                      {
                        $item->add_object ($history_item);
                      }
                      $item->add_main_object ($obj_rec->object);
                      $item->set_subject ($obj_rec->object->title_for_history_item ($history_item, $subscriber));
                      $item->num_items = 1;
                      $items [] = $item;
                    }
                  }
                }
              }
            }
          }
        }
      }

      /* At the end, we have a single array of PUBLISHER_MESSAGE records,
       * each of which holds a subscriber and a list of objects to publish for
       * that record. This is the format which the publisher accepts
       * internally.
       */

      if (isset($items))
      {
      	$this->_send_items ($items);
      }
    }
  }

  /**
   * Get the body for this description.
   * @param PUBLISHER_MESSAGE $item
   * @return string
   * @access private
   */
  public function body_for ($item)
  {
    if (isset ($this->_rendered_bodies) && isset ($this->_rendered_bodies [$item->identifier]))
    {
      $Result = $this->_rendered_bodies [$item->identifier];
    }
    else
    {
      $class_name = $this->app->final_class_name ('WEBCORE_MAIL_BODY_RENDERER', 'webcore/mail/webcore_mail_body_renderer.php');
      $mail_renderer = new $class_name ($this->context);

      $mail_renderer->add ($this->_subscription_settings, $this->_renderer_for ($this->_subscription_settings));

      foreach ($item->objects as $obj)
      {
        $mail_renderer->add ($obj, $this->_renderer_for ($obj));
      }

      if ($item->subscriber->send_as_html)
      {
        $Result = $mail_renderer->as_html ($item->rendering_options ());
      }
      else
      {
        $Result = $mail_renderer->as_text ($item->rendering_options ());
      }

      $this->_renderered_bodies [$item->identifier] = $Result;
    }

    return $this->_replace_aliases ($item, $Result);
  }

  /**
   * Mark the given records as published in the database.
   * Called from {@link publish_history_items()} once all interested subscribers have
   * been updated with the history item ids.
   * @param array[HISTORY_ITEM] $history_items
   * @access private
   */
  protected function _update_publication_status_for ($history_items)
  {
    $table_name = $this->app->table_names->history_items;
    $all_affected_history_item_ids = implode (',', array_keys ($history_items));
    $num_history_items = sizeof ($history_items);
    $this->record ("Marked [$num_history_items] items in [$table_name] as published.");
    $this->record ("Marked [$all_affected_history_item_ids] in [$table_name] as published.", Msg_type_debug_info);
    if (! $this->testing)
    {
      $this->db->logged_query ("UPDATE $table_name SET publication_state = '" . History_item_sent . "' WHERE id IN ($all_affected_history_item_ids)");
    }
  }

  /**
   * Remove all queued history items for this subscriber.
   * Call this once all emails have been sent to a subscriber in order to clear
   * the database or any pending history items for that subscriber.
   * @param SUBSCRIBER $subscriber
   * @access private
   */
  protected function _clear_queued_history_items_for ($subscriber)
  {
    $this->record ("$subscriber->email: Cleared queued history items from database.");
    $subscriber->clear_queued_history_items ();
    if (! $this->testing)
    {
      $subscriber->store ();
    }
  }

  /**
   * Send the queued messages.
   * Each message contains all the information it needs to send an email to the
   * given subscriber. Once all emails are sent to a subscriber, the queued
   * history items for that subscriber are cleared in the database.
   * @param array[PUBLISHER_MESSAGE] $items
   * @access private
   */
  protected function _send_items ($items)
  {
    if (sizeof ($items))
    {
      $last_subscriber = null;
      $msg = $this->_make_mail_message ();

      foreach ($items as $item)
      {
        $subscriber = $item->subscriber;
        if (isset ($last_subscriber) && (strcmp ($subscriber->email, $last_subscriber->email) != 0))
        {
          $this->_clear_queued_history_items_for ($last_subscriber);
        }

        if ($subscriber->email)
        {
          $item->apply ($msg);

          if ($this->testing && $this->preview)
          {
            echo "<p class=\"field\">$msg->subject</p>";
            if ($msg->send_as_html)
            {
              echo '<div class="chart"><div class="chart-body">' . $msg->body . '</div></div>';
            }
            else
            {
              echo "<pre>$msg->body</pre>";
            }
          }

          $this->record ("$subscriber->email: Sent [$msg->subject].");
          $this->record ("$subscriber->email: Contents of [$msg->subject] are [$item->identifier].", Msg_type_debug_info);

          if (! $this->testing)
          {
            $msg->send ($this->provider);
          }
        }
        else
        {
          $this->record ("Subscriber [$subscriber->id] has no email address (but has subscriptions).", Msg_type_warning);
        }

        $last_subscriber = $subscriber;
      }

      $this->_clear_queued_history_items_for ($subscriber);
    }
  }

  /**
   * Get a renderer from cache, if possible.
   * @param PUBLISHABLE $obj
   * @return MAIL_OBJECT_RENDERER
   * @access private
   */
  protected function _renderer_for ($obj)
  {
    $class_name = strtoupper (get_class ($obj));
    if (isset($this->_renderers [$class_name]))
    {
      return $this->_renderers [$class_name];
    }

    $Result = $obj->handler_for (Handler_mail);
    $this->_renderers [$class_name] = $Result;
    return $Result;
  }

  /**
   * Replace any templated aliases in the text.
   * The publisher embeds a marker which should be replaced with the subscriber's email.
   * That is replaced here, in the post-processing phase. It's done here because the body
   * could be pulled from cache, but the email address differs with each email. This
   * allows the publisher to make the most of caching and still customize the email body
   * for the recipient.
   * @param PUBLICATION_BATCH $batch Contains information about the publication phase
   * @param string $text
   * @return string
   * @access private
   */
  protected function _replace_aliases ($item, $text)
  {
    return str_replace (Subscriber_email_alias, $item->subscriber->email, $text);
  }

  /**
   * Return a query for the requested objects.
   * @param string $object_type
   * @return QUERY
   * @access private
   */
  protected function _object_query_for ($object_type)
  {
    switch ($object_type)
    {
    case History_item_folder:
      return $this->login->folder_query ();
    case History_item_entry:
      return $this->login->all_entry_query ();
    case History_item_comment:
      return $this->login->all_comment_query ();
    case History_item_user:
      return $this->login->user_query ();
    case History_item_group:
      return $this->login->group_query ();
    case History_item_attachment:
      return $this->login->all_attachment_query ();
    default:
      $this->record ("Unknown object type [$object_type]", Msg_type_warning);
      return null;
    }
  }

  /**
   * List of subscribers for this objct.
   * @param HISTORY_ITEM $history_item
   * @param AUDITABLE $obj
   * @return array[SUBSCRIBER]
   */
  protected function _subscribers_for ($history_item, $obj)
  {
    $query = $obj->subscriber_query ($history_item);

    // Don't even bother retrieving disabled subscribers

    $query->restrict ('min_hours_to_wait <> ' . Subscriptions_disabled);

    return $query->objects ();
  }

  /**
   * @return MAIL_MESSAGE
   * @access private
   */
  protected function _make_mail_message ()
  {
    $class_name = $this->app->final_class_name ('MAIL_MESSAGE', 'webcore/mail/mail_message.php');
    $Result = new $class_name ($this->context);

    $opts = $this->app->mail_options;
    $Result->set_sender ($opts->send_from_name, $opts->send_from_address);

    return $Result;
  }

  /**
   * @var SUBSCRIPTION_SETTINGS
   * @access private
   */
  protected $_subscription_settings;
}

/**
 * A description of how to build a message to a {@link SUBSCRIBER}.
 * Each user receives one or more mails depending on publishable content and personal settings.
 * The {@link PUBLISHER} prepares one physical item for each message to be sent to a subscriber.
 * Each physical item contains one or more objects, which describe/pertain to one or more logical
 * items. For example, a logical item is an {@link ENTRY}. Publication is triggered by {@link HISTORY_ITEM}s,
 * so, depending on user settings, if a physical item's object list is (history item, entry, history item, entry),
 * it consists of two logical items and four objects.
 * @package webcore
 * @subpackage mail
 * @version 3.3.0
 * @since 2.5.0
 * @access private
 */
class PUBLISHER_MESSAGE extends WEBCORE_OBJECT
{
  /**
   * Uniquely identifies this item.
   * A hash that can be used as a key in a cache.
   * @var string
   */
  public $identifier;

  /**
   * Subscriber for whom the message should be prepared.
   * @var SUBSCRIBER
   */
  public $subscriber;

  /**
   * List of objects to render in the message.
   * @var array[UNIQUE_OBJECT]
   */
  public $objects;

  /**
   * How many logical items are represented?
   * The list of objects in this item may all describe the same item or they may describe multiple items.
   * For example, if the list of objects is (history item, history item, article), then there are three objects, but only
   * one real item. If the list is (history item, article, history item, article), then there are four objects and two
   * items. This value is set when the item is prepared.
   * @var boolean
   */
  public $num_items = 0;

  /**
   * @param PUBLISHER $publisher
   * @param SUBSCRIBER $subscriber
   */
  public function __construct ($publisher, $subscriber)
  {
    parent::__construct ($publisher->context);

    $this->_publisher = $publisher;
    $this->subscriber = $subscriber;
    $this->identifier = "[{$subscriber->send_as_html}]";

    $class_name = $this->app->final_class_name ('PUBLISHER_MESSAGE_SUBJECT');
    $this->_subject = new $class_name ($this->context);
  }

  /**
   * Number of total objects in the body.
   * @return integer
   */
  public function num_objects ()
  {
    return isset ($this->objects) && sizeof ($this->objects);
  }

  /**
   * Apply a fixed subject for this message.
   * @param string $text
   */
  public function set_subject ($text)
  {
    $this->_subject->set_text ($text);
  }

  /**
   * Add an object to the body of the message.
   * @see add_main_object()
   * @param object $obj
   */
  public function add_object ($obj)
  {
    $this->objects [] = $obj;
    $this->identifier .= '|' . $obj->id . '|';
  }

  /**
   * Add an object to the subject and body of the message.
   * @see add_object()
   * @param object $obj
   */
  public function add_main_object ($obj)
  {
    $this->add_object ($obj);
    $this->_subject->add_object ($obj);
  }

  /**
   * Fill out the message body and subject accordingly.
   * @param MAIL_MESSAGE
   */
  public function apply ($msg)
  {
    $msg->set_send_to ($this->subscriber->email);
    $msg->send_as_html = $this->subscriber->send_as_html;
    $subject = $this->subject ();
    $msg->set_content ($subject, $this->body ());
  }

  /**
   * Formats a subject representing the message contents.
   * @return string
   */
  public function subject ()
  {
    return $this->app->title . ': ' . $this->_subject->as_text ();
  }

  /**
   * Formats the message contents into a single text.
   * @return string
   */
  public function body ()
  {
    return $this->_publisher->body_for ($this);
  }

  /**
   * The options used to format this message.
   * @return MAIL_OBJECT_RENDERER_OPTIONS
   */
  public function rendering_options ()
  {
    $class_name = $this->app->final_class_name ('MAIL_OBJECT_RENDERER_OPTIONS', 'webcore/mail/mail_object_renderer.php');
    $Result = new $class_name ();
    if (! $Result->ignore_subscriber_preferred_text_length)
    {
        $Result->preferred_text_length = $this->subscriber->preferred_text_length;
    }
    $Result->num_items = $this->num_items;
    $Result->content_summary = $this->_subject->as_text ();
    return $Result;
  }

  /**
   * @var PUBLISHER
   * @access private
   */
  protected $_publisher;

  /**
   * @var PUBLISHER_MESSAGE_SUBJECT
   * @access private
   */
  protected $_subject;
}

/**
 * Formats the subject line for multiple objects.
 * @package webcore
 * @subpackage mail
 * @version 3.3.0
 * @since 2.6.0
 * @access private
 */
class PUBLISHER_MESSAGE_SUBJECT extends OBJECT_LIST_TITLE
{
}

/**
 * Template tag, to be replaced with email address in generated emails.
 * This tag is embedded in the mail body and replaced before sending to an
 * address. Used to generate the link to individual subscription settings.
 * @access private
 */
define ('Subscriber_email_alias', '<?earthli_email?>');

require_once ('webcore/mail/mail_object_renderer.php');

/**
 * Renders a link for a subscriber's personal settings.
 * @package webcore
 * @subpackage mail
 * @version 3.3.0
 * @since 2.5.0
 */
class SUBSCRIPTION_SETTINGS_MAIL_RENDERER extends MAIL_OBJECT_RENDERER
{
  /**
   * Gets the subject for the mail.
   * 
   * @param object $obj
   * @param MAIL_OBJECT_RENDERER_OPTIONS $options
   * @return string
   * @private
   */
  public function subject ($obj, $options)
  {
    return 'Subscription Settings';
  }

  /**
   * Gets the url for the receiver of the mail.
   * 
   * @return string
   * @private
   */
  protected function subscription_settings_url ()
  {
    return $this->app->resolve_file ($this->app->page_names->user_subscriptions_home . "?email=" . Subscriber_email_alias);
  }
  
  /**
   * Returns the object's contents as HTML.
   * 
   * @param object $obj
   * @param MAIL_OBJECT_RENDERER_OPTIONS $options
   * @access private
   */
  protected function _echo_html_content ($obj, $options)
  {
    $url = $this->subscription_settings_url (); 
    echo ("<p class=\"notes\">This email was generated automatically. <a href=\"{$url}\">Check your subscription settings</a>.</p>");
  }

  /**
   * Returns the object's contents as text.
   * 
   * @param object $obj
   * @param MAIL_OBJECT_RENDERER_OPTIONS $options
   * @access private
   */
  protected function _echo_text_content ($obj, $options)
  {
    echo $this->line ("This email was generated automatically. Check your subscription settings:");
    echo $this->line ("<" . $this->subscription_settings_url () . ">");
  }
}

/**
 * 'Fake' class to use with {@link MAIL_SUBSCRIPTION_SETTINGS_RENDERER}.
 * The request for a renderer is based on the type of object to render, so this fake class is
 * passed so that a subscription settings renderer is returned. The subscription settings don't
 * actually need an object, but the renderer caching does.
 * @package webcore
 * @subpackage mail
 * @version 3.3.0
 * @since 2.5.0
 */
class SUBSCRIPTION_SETTINGS extends RENDERABLE
{
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
      case Handler_mail:
        return new SUBSCRIPTION_SETTINGS_MAIL_RENDERER ($this->app);
      default:
        return parent::_default_handler_for ($handler_type, $options);
    }
  }
}

?>