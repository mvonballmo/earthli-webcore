<?php

/**
 * @copyright Copyright (c) 2002-2014 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage obj
 * @version 3.5.0
 * @since 2.2.1
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
require_once ('webcore/obj/attachment_host.php');

/**
 * Main object in a WebCore {@link APPLICATION}.
 * @package webcore
 * @subpackage obj
 * @version 3.5.0
 * @since 2.2.1
 */
class ENTRY extends ATTACHMENT_HOST
{
  /**
   * The last modification time, either of the entry itself or a comment.
   * @return DATE_TIME
   */
  public function time_changed ()
  {
    $this->_load_change_info ();
    return $this->_time_changed;
  }

  /**
   * The last modifier, either of the entry itself or a comment.
   * @return USER
   */
  public function changer ()
  {
    $this->_load_change_info ();
    return $this->_changer;
  }

  /**
   * A query that addresses all the comments for this entry.
   * @return ENTRY_COMMENT_QUERY
   */
  public function comment_query ()
  {
    include_once ('webcore/db/entry_comment_query.php');
    return new ENTRY_COMMENT_QUERY ($this);
  }

  /**
   * Return a comment for this entry.
   * Does not imply storage in the database; simply provides a comment correctly
   * connected to this entry and an optional parent comment.
   * @param integer $parent_id
   * @return COMMENT
   */
  public function new_comment ($parent_id)
  {
    $Result = $this->_make_comment ();
    $Result->parent_id = $parent_id;
    return $Result;
  }

  /**
   * @return TITLE_FORMATTER
   */
  public function title_formatter ()
  {
    $Result = parent::title_formatter ();
    $Result->page_name = $this->app->page_names->entry_home;
    $Result->max_visible_output_chars = $this->app->max_title_size ('entry');
    return $Result;
  }

  /**
   * @param SQL_STORAGE $storage Store values to this object.
   */
  public function store_to ($storage)
  {
    parent::store_to ($storage);
    $tname =$this->table_name ();
    $fldr_id = $this->parent_folder_id ();
    $storage->add ($tname, 'folder_id', Field_type_integer, $fldr_id);
  }

  /**
   * Name of the home page name for this object.
   * @return string
   */
  public function page_name ()
  {
    return $this->app->page_names->entry_home;
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
    $folder = $this->parent_folder ();
    $folder_url = $folder->object_url ($use_links, $separator, $formatter);

    if (! isset ($separator))
    {
      $separator = $this->app->display_options->obj_url_separator;
    }

    return $folder_url . $separator . $Result;
  }

  /**
   * @param PURGE_OPTIONS $options
   * @access private
   */
  protected function _purge ($options)
  {
    $comment_query = $this->comment_query ();
    $comments = $comment_query->objects ();
    
    foreach ($comments as &$comment)
    {
      $comment->purge ($options);
    }
        
    /* Remove subscriptions */
    $this->db->logged_query ("DELETE LOW_PRIORITY FROM {$this->app->table_names->subscriptions} WHERE ref_id = $this->id AND kind = '" . Subscribe_entry . "'");

    parent::_purge ($options);
  }

  /**
   * Name of this object's database table.
   * @return string
   * @access private
   */
  public function table_name ()
  {
    return $this->app->table_names->entries;
  }

  /**
   * @return COMMENT
   * @access private
   */
  protected function _make_comment ()
  {
    $class_name = $this->app->final_class_name ('COMMENT', 'webcore/obj/comment.php');
    $Result = new $class_name ($this->app);
    $Result->set_entry ($this);
    return $Result;
  }

  /**
   * @access private
   */
  protected function _load_change_info ()
  {
    if (! isset ($this->_latest_comment_time))
    {
      $qs = "SELECT com.creator_id, com.time_created FROM {$this->app->table_names->comments} com" .
            " INNER JOIN {$this->app->table_names->entries} entry on com.entry_id = entry.id" .
            " WHERE ORDER BY com.time_created DESC LIMIT 1";

      $this->db->logged_query ($qs);

      if ($this->db->next_record ())
      {
        $this->_time_changed = $this->app->make_date_time ();
        $this->_time_changed->set_from_iso ($this->db->f ('time_created'));
        $this->_changer = $this->app->user_at_id ($this->db->f ('id'));
      }
      else
      {
        $this->_time_changed = $this->time_modified;
        $this->_changer = $this->modifier ();
      }
    }
  }

  /**
   * Create a class-specific history item query.
   * @return HISTORY_ITEM_QUERY
   * @access private
   */
  protected function _make_history_item_query ()
  {
    include_once ('webcore/db/history_item_query.php');
    return new ENTRY_HISTORY_ITEM_QUERY ($this->app);
  }

  /**
   * Name of the {@link FOLDER_PERMISSIONS} to use for this object.
   * @access private
   */
  protected function _privilege_set ()
  {
    return Privilege_set_entry;
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
        include_once ('webcore/gui/entry_navigator.php');
        return new ENTRY_NAVIGATOR ($this);
      case Handler_commands:
        include_once ('webcore/cmd/entry_commands.php');
        return new ENTRY_COMMANDS ($this);
      case Handler_history_item:
        include_once ('webcore/obj/webcore_history_items.php');
        return new ENTRY_HISTORY_ITEM ($this->app, $options);
      case Handler_rss_renderer:
        include_once ('webcore/gui/rss_renderer.php');
        return new ENTRY_RSS_RENDERER ($this->app, $options);
      case Handler_atom_renderer:
        include_once ('webcore/gui/atom_renderer.php');
        return new ENTRY_ATOM_RENDERER ($this->app, $options);
      case Handler_associated_data:
        include_once ('webcore/gui/entry_renderer.php');
        return new ENTRY_ASSOCIATED_DATA_RENDERER ($this->app, $options);
      case Handler_subscriptions:
        include_once ('webcore/gui/subscription_renderer.php');
        return new ENTRY_SUBSCRIPTION_RENDERER ($this->app, $options);
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
    $query->restrict ('watch_entries > 0');
    $query->restrict_kinds (array (Subscribe_folder => $this->parent_folder_id ()
                                   , Subscribe_entry => $this->id
                                   , Subscribe_user => $this->creator_id));
  }

  /**
   * @var FOLDER
   * @access private
   */
  protected $_folder;

  /**
   * @var DATE_TIME
   * @access private
   */
  protected $_latest_comment_time;

  /**
   * @var USER
   * @access private
   */
  protected $_latest_commenter;
}

/**
 * An {@link ENTRY} with support for an {@link Unpublished} status.
 * @package webcore
 * @subpackage obj
 * @version 3.5.0
 * @since 2.5.0
 */
class DRAFTABLE_ENTRY extends ENTRY
{
  /**
   * When was this entry actually published?
   * This is the time the entry was changed from {@link Unpublished} to {@link Visible}.
   * @var DATE_TIME
   */
  public $time_published;

  /**
   * @var integer
   */
  public $publisher_id;

  /**
   * @var integer
   */
  public $state = Draft;
  
  /**
   * @param APPLICATION $app Main application.
   */
  public function __construct ($app)
  {
    parent::__construct ($app);

    $this->time_published = $app->make_date_time ();
    $this->time_published->clear ();
  }

  /**
   * Is this entry in {@link Draft}, {@link Queued} or {@link Abandoned} status?
   * @return boolean
   */
  public function unpublished ()
  {
    return ($this->state & Unpublished) == Unpublished;
  }

  /**
   * @return boolean
   */
  public function queued ()
  {
    return $this->state == Queued;
  }

  /**
   * @return boolean
   */
  public function abandoned ()
  {
    return $this->state == Abandoned;
  }

  /**
   * @return USER
   */
  public function publisher ()
  {
    return $this->app->user_at_id ($this->publisher_id);
  }

  /**
   * @return boolean
   */
  public function modified ()
  {
    if ($this->time_published->is_valid ())
    {
      return ! $this->time_published->equals ($this->time_modified);
    }

    return parent::modified ();
  }

  /**
   * @return string
   */
  public function state_as_string ()
  {
    switch ($this->state)
    {
    case Draft:
      return 'Draft';
    case Abandoned:
      return 'Abandoned';
    case Queued:
      return 'Queued';
    default:
      if ($this->unpublished ())
      {
        return 'Unpublished';
      }

      return parent::state_as_string ();
    }
  }

  /**
   * Set up this object so it will {@link store()} a new object.
   */
  public function initialize_as_new ()
  {
    parent::initialize_as_new ();
    $this->time_published->clear ();
    $this->publisher_id = 0;
    $this->state = Draft;
  }

  /**
   * @param DATABASE $db Database from which to load values.
   */
  public function load ($db)
  {
    parent::load ($db);
    $this->publisher_id = $db->f ('publisher_id');
    $this->time_published->set_from_iso ($db->f ('time_published'));
  }

  /**
   * @param SQL_STORAGE $storage Store values to this object.
   * @access private
   */
  public function store_to ($storage)
  {
    parent::store_to ($storage);
    $tname =$this->table_name ();
    $storage->add ($tname, 'time_published', Field_type_date_time, $this->time_published);
    $storage->add ($tname, 'publisher_id', Field_type_integer, $this->publisher_id);
  }

  /**
   * State of item when created.
   * @return string
   */
  public function history_item_kind_for_new ()
  {
    if ($this->visible ())
    {
      return History_item_published;
    }

    return History_item_created;
  }

  /**
   * Which kind of history item does this state change generate?
   * @param integer $state
   * @return string
   */
  public function history_item_kind_for_transition_to ($state)
  {
    if (($this->unpublished ()) && ($state == Visible))
    {
      return History_item_published;
    }
    
    if (($state & Unpublished) && $this->visible ())
    {
      return History_item_unpublished;
    }
    
    if ($state == Queued)
    {
      return History_item_queued;
    }
    
    if ($state == Abandoned)
    {
      return History_item_abandoned;
    }

    return parent::history_item_kind_for_transition_to ($state);
  }

  /* Final preparation before storing to the database.
   * @access private
   */
  protected function _pre_store ()
  {
    parent::_pre_store ();

    if ($this->unpublished ())
    {
      $this->time_published->clear ();
      $this->publisher_id = 0;
      
      if ($this->_state_when_loaded != $this->state)
      {
        // State changed; check history items and revoke notification for published items
        
        $history_item_query = $this->history_item_query ();
        $history_items = $history_item_query->objects ();
        
        foreach ($history_items as &$history_item)
        {
          if (($history_item->kind == History_item_published) && ($history_item->publication_state == History_item_needs_send))
          {
            $history_item->publication_state = History_item_silent;
            $history_item->store();
          }
        }
      }
    }
    elseif (! $this->time_published->is_valid ())
    {
      $this->time_published->set_now ();
      if ($this->update_modifier_on_change)
      {
        $this->publisher_id = $this->login->id;
      }
      else
      {
        $this->publisher_id = $this->modifier_id;
      }
    }
  }

  /**
   * Copy the object to the specified folder.
   * @param FOLDER $fldr
   * @param FOLDER_OPERATION_OPTIONS $options
   */
  protected function _copy_to ($fldr, $options)
  {
    if ($options->copy_as_draft)
    {
      $this->state = Draft;
    }
    else
    {
      $this->state = Visible;
    }
    parent::_copy_to ($fldr, $options);
  }

  /**
   * @return string
   * @access private
   */
  public function state_icon_url ()
  {
    switch ($this->state)
    {
    case Draft:
      return '{icons}indicators/invisible';
    case Queued:
      return '{icons}buttons/queue';
    case Abandoned:
      return '{icons}buttons/abandon';
    default:
      return parent::state_icon_url ();
    }
  }

  /**
   * Return default handler objects for supported tasks.
   * @param string $handler_type Specific functionality required.
   * @param object $options
   * @return object
   */
  protected function _default_handler_for ($handler_type, $options = null)
  {
    switch ($handler_type)
    {
      case Handler_print_renderer:
      case Handler_html_renderer:
      case Handler_text_renderer:
        include_once ('webcore/gui/entry_renderer.php');
        return new DRAFTABLE_ENTRY_RENDERER ($this->app, $options);
      case Handler_rss_renderer:
        include_once ('webcore/gui/rss_renderer.php');
        return new DRAFTABLE_ENTRY_RSS_RENDERER ($this->app);
      case Handler_atom_renderer:
        include_once ('webcore/gui/atom_renderer.php');
        return new DRAFTABLE_ENTRY_ATOM_RENDERER ($this->app);
      case Handler_commands:
        include_once ('webcore/cmd/entry_commands.php');
        return new DRAFTABLE_ENTRY_COMMANDS ($this);
      default:
        return parent::_default_handler_for ($handler_type, $options);
    }
  }

  /**
   * Copy properties from the given object.
   * @param DRAFTABLE_ENTRY $other
   * @access private
   */
  protected function copy_from ($other)
  {
    parent::copy_from($other);
    $this->time_published = clone ($other->time_published);
  }
}

?>