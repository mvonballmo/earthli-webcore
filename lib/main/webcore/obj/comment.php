<?php

/**
 * @copyright Copyright (c) 2002-2013 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage obj
 * @version 3.4.0
 * @since 2.2.1
 */

/****************************************************************************

Copyright (c) 2002-2013 Marco Von Ballmoos

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
 * Comments are annotations attached to {@link ENTRY}s or other comments.
 * Each comment is numbered, numbers are maintained individually for each entry.
 * @package webcore
 * @subpackage obj
 * @version 3.4.0
 * @since 2.2.1
 */
class COMMENT extends ATTACHMENT_HOST
{
  /**
   * The id of the entry that owns this comment.
   * @var integer
   * @access private
   */
  public $entry_id;

  /**
   * The id of the comment above this comment.
   * If this is empty, the comment is a directy annonation of the entry.
   * @var integer
   * @access private
   */
  public $parent_id;

  /**
   * This is the nth comment in attached to this entry.
   * Comments are not renumbered when an item is deleted or purged. This can
   * leave gaps in the numbering, but that's a feature, showing activity in the
   * system as well. That way, censoring will be apparent, to some degree.
   * @var integer
   */
  public $number;

  /**
   * Identifies the icon/type of this comment.
   * Kinds can be customized per deployment.
   * @see WEBCORE_SETTINGS
   * @var integer
   */
  public $kind;

  /**
   * Comment annotates this entry.
   * @return ENTRY
   */
  public function entry ()
  {
    $this->assert (isset ($this->_entry), '_entry is not cached.', 'entry', 'COMMENT');
    return $this->_entry;
  }

  /**
   * The id of the parent folder.
   * @return integer
   */
  public function parent_folder_id ()
  {
    if (isset ($this->_parent_folder))
    {
      return $this->_parent_folder->id;
    }

    $entry = $this->entry ();
    return $entry->parent_folder_id ();
  }

  /**
   * A QUERY for retrieving comments directly attached to this one.
   * @return ENTRY_COMMENT_ENTRY
   */
  public function comment_query ()
  {
    $this->assert ($this->exists (), 'Comment does not exist yet.', 'comment_query', 'COMMENT');

    $class_name = $this->app->final_class_name ('ENTRY_COMMENT_QUERY', 'webcore/db/entry_comment_query.php');
    $Result = new $class_name ($this->entry ());
    $Result->restrict ("com.parent_id = $this->id");

    if (isset ($this->_sub_comments))
    {
      $Result->cache ($this->_sub_comments);
    }

    return $Result;
  }

  /**
   * Returns a list of comments directly attached to this one.
   * May also contain the entire tree of sub-comments below this one.
   * @return array[COMMENT]
   */
  public function sub_comments ()
  {
    if (! isset ($this->_sub_comments))
    {
      $comment_query = $this->comment_query ();
      $this->_sub_comments = $comment_query->objects ();
    }

    return $this->_sub_comments;
  }

  /**
   * Caches a prepared sub-comment.
   * Generally called when a tree of comments is built to avoid re-querying for
   * data that has already been retrieved.
   * @param COMMENT $c
   * @access private
   */
  public function add_comment ($c)
  {
    $this->_sub_comments [] = $c;
  }

  /**
   * Caches a list of sub-comment.
   * Generally called when a tree of comments is built to avoid re-querying for
   * data that has already been retrieved.
   * @param array[COMMENT] $subs
   * @access private
   */
  public function set_sub_comments ($subs)
  {
    $this->_sub_comments = $subs;
  }

  /**
   * Indicate that this comment's sub-comments are cached.
   * This is necessary since the size of the array can't be reliably used since
   * many comments have no sub-comments.
   * @param boolean $flag
   * @access private
   */
  public function set_comments_cached ($flag)
  {
    $this->_sub_comments = array ();
  }

  /**
   * All properties of this entry's kind.
   * These are the properties defined in the user data file.
   * @see _project_entry_kinds()
   */
  public function icon_properties ()
  {
    $props = $this->app->display_options->comment_icons ();
    if (isset ($props [$this->kind - 1]))
    {
      return $props [$this->kind - 1];
    }
    else
    {
      $prop = new PROPERTY_VALUE ($this->app);
      $prop->title = "[Unknown kind ($this->kind)]";
      return $prop;
    }
  }

  /**
   * HTML code for the icon to use for this comment.
   * @param string $size
   * @return string
   */
  public function icon ($size = '15px')
  {
    $props = $this->icon_properties ();
    return $props->icon_as_html ($size);
  }

  /**
   * The raw URL to the icon to use for this comment.
   * @param string $size
   * @return string
   */
  public function icon_url ($size = '15px')
  {
    $props = $this->icon_properties ();
    return $props->expanded_icon_url($size);
  }

  /**
   * Attach this comment to an entry.
   * Does not store to the database. Sets up both the entry and the folder information
   * for this comment; used during object setup when retrieved from database.
   * @param ENTRY $entry
   */
  public function set_entry ($entry)
  {
    $this->set_parent_folder ($entry->parent_folder ());
    $this->_entry = $entry;
    $this->entry_id = $entry->id;
  }

  /**
   * @return string
   */
  public function raw_title ()
  {
    if (isset ($this->number))
    {
      $number = '#' . $this->number;
    }
    else
    {
      $number = '#???';
    }
    $Result = parent::raw_title ();
    if ($Result)
    {
      $Result = "$number - $Result";
    }
    else
    {
      $Result = $number;
    }
    return $Result;
  }

  /**
   * @param DATABASE $db Database from which to load values.
   */
  public function load ($db)
  {
    parent::load ($db);
    $this->entry_id = $db->f ("entry_id");
    $this->parent_id = $db->f ("parent_id");
    $this->root_id = $db->f ("root_id");
    $this->number = $db->f ("number");
    $this->kind = $db->f ("kind");
  }

  /**
   * @param SQL_STORAGE $storage Store values to this object.
   */
  public function store_to ($storage)
  {
    parent::store_to ($storage);
    $tname =$this->table_name ();
    $storage->add ($tname, 'entry_id', Field_type_integer, $this->entry_id, Storage_action_create);
    $storage->add ($tname, 'parent_id', Field_type_integer, $this->parent_id, Storage_action_create);
    $storage->add ($tname, 'number', Field_type_integer, $this->number, Storage_action_create);
    $storage->add ($tname, 'kind', Field_type_integer, $this->kind);
  }

  /**
   * Name of the home page name for this object.
   * @return string
   */
  public function page_name ()
  {
    return $this->app->page_names->comment_home;
  }
  
  /**
   * @access private
   */
  protected function _create ()
  {
    $this->db->logged_query ("SELECT MAX(number) FROM {$this->app->table_names->comments} WHERE entry_id = $this->entry_id");
    if ($this->db->next_record ())
    {
      $this->number = $this->db->f (0) + 1;
    }
    else
    {
      $this->number = 1;
    }

    parent::_create ();
  }

  /**
   * @param PURGE_OPTIONS $options
   * @access private
   */
  protected function _purge ($options)
  {
    /* remove sub-comments */
    $comments = $this->sub_comments ();
    foreach ($comments as $comment)
    {
      $comment->purge ($options);
    }

    /* Remove subscriptions */
    $this->db->logged_query ("DELETE LOW_PRIORITY FROM {$this->app->table_names->subscriptions} WHERE ref_id = $this->id AND kind = '" . Subscribe_comment . "'");

    parent::_purge ($options);
  }

  /**
   * Name of this object's database table.
   * @return string
   * @access private
   */
  public function table_name ()
  {
    return $this->app->table_names->comments;
  }

  /**
   * Update database for changed state.
   * When a state-change occurs, the state is applied to the comment and all sub-comments.
   * @access private
   */
  protected function _state_changed ()
  {
    if ($this->exists ())
    {
      $sub_comments = $this->sub_comments (); 
      foreach ($sub_comments as &$com)
      {
        $com->set_state ($this->state, true);
      }
    }

    parent::_state_changed ();
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
    $entry = $this->entry ();
    $entry_url = $entry->object_url ($use_links, $separator, $formatter);

    if (! isset ($separator))
    {
      $separator = $this->app->display_options->obj_url_separator;
    }

    return $entry_url . $separator . $Result;
  }

  /**
   * Name of the {@link FOLDER_PERMISSIONS} to use for this object.
   * @access private
   */
  protected function _privilege_set ()
  {
    return Privilege_set_comment;
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
        include_once ('webcore/gui/comment_renderer.php');
        return new COMMENT_RENDERER ($this->app, $options);
      case Handler_commands:
        include_once ('webcore/cmd/comment_commands.php');
        return new COMMENT_COMMANDS ($this);
      case Handler_history_item:
        include_once ('webcore/obj/webcore_history_items.php');
        return new COMMENT_HISTORY_ITEM ($this->app);
      case Handler_subscriptions:
        include_once ('webcore/gui/subscription_renderer.php');
        return new COMMENT_SUBSCRIPTION_RENDERER ($this->app, $options);
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

    $query->restrict ('watch_comments > 0');
    $query->restrict_kinds (array (Subscribe_folder => $folder->id
                                   , Subscribe_entry => $this->entry_id
                                   , Subscribe_comment => $this->parent_id
                                   , Subscribe_comment => $this->id
                                   , Subscribe_user => $this->creator_id));
  }

  /**
   * @var array[COMMENT]
   * @access private
   */
  protected $_sub_comments;

  /**
   * @var ENTRY
   * @access private
   */
  protected $_entry;

  /**
   * @var FOLDER
   * @access private
   */
  protected $_folder;
}
 
?>