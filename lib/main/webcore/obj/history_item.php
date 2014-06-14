<?php

/**
 * @copyright Copyright (c) 2002-2014 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage history
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
 * Records a change made in the {@link APPLICATION}.
 * Every time a user makes a modification to the application's data, a history
 * item is recorded to the database, recording which information was changed, by
 * whom and when.
 * @package webcore
 * @version 3.5.0
 * @since 2.4.0
f * @subpackage history
 */
class HISTORY_ITEM extends UNIQUE_OBJECT
{
  /**
   * Which kind of object is this?
   * This identifies the class name of the object. This, together with the {@link $object_id} forms the primary
   * key for this object. Descendents need to redefine this value; each uniquely identified group of objects needs
   * its own identifier (e.g. entries, comments, etc.)
   * @var string
   */
  public $object_type = '';

  /**
   * Id of the object which was changed.
   * @var integer
   */
  public $object_id;

  /**
   * Which user made this change?
   * @var integer
   */
  public $user_id;

  /**
   * When was the history item recorded?
   * @var DATE_TIME
   */
  public $time_created;

  /**
   * What is the publication status?
   * An history item can be published to interested users via email notification. This
   * flag is either {@link History_item_silent}, {@link History_item_sent} or
   * {@link History_item_needs_send}.
   * @var integer
   */
  public $publication_state = History_item_needs_send;

  /**
   * Id of object which controls access for this object.
   * The object to which this refers differs depending on the type of history item. Content-based history items specify a folder id.
   * @var integer
   */
  public $access_id;

  /**
   * What kind of change is this?
   * An history item can be {@link History_item_created} or {$link History_item_updated}. Descendents can define more kinds.
   * @see OBJECT_IN_FOLDER_HISTORY_ITEM
   * var @string
   */
  public $kind;

  /**
   * Short description of the history item.
   * This will be automatically generated from the list of actual changes made to the object. The user may override this
   * title when making the change. This title is used when publishing the results of the history item as an email.
   * @var string
   */
  public $title = '';

  /**
   * Longer description provided by the user.
   * This is optional and may be empty. The system will always generate a listing of the actual changes made in
   * {@link $system_description}.
   *  @var string
   */
  public $description = '';

  /**
   * Point-by-point description of changes made to the object.
   * This is generated by the system when the changed object is stored, detailing which fields were updated.
   *  @var string
   */
  public $system_description = '';

  /**
   * @param APPLICATION $context Application for this history item
   */
  public function __construct ($context)
  {
    parent::__construct ($context);
    $this->time_created = $context->make_date_time ();
  }

  /**
   * @return USER
   */
  public function creator ()
  {
    return $this->app->user_at_id ($this->user_id);
  }

  /**
   * Text version of the {@link $kind}.
   * @return string
   */
  public function kind_as_text ()
  {
    $Result = $this->supported_kind_as_text ();
    if (! $Result)
    {
      $Result = 'updated';
    }
    return $Result;
  }

  /**
   * @throws UNKNOWN_VALUE_EXCEPTION
   * @return string
   */
  public function publication_state_as_text ()
  {
    switch ($this->publication_state)
    {
    case History_item_silent:
      return 'not published';
    case History_item_sent:
      return 'published';
    case History_item_needs_send:
      return 'queued for publication';
    default:
      throw new UNKNOWN_VALUE_EXCEPTION($this->publication_state);
    }
  }

  /**
   * An HTML icon for published/not published/queued.
   * @param string $size
   * @throws UNKNOWN_VALUE_EXCEPTION
   * @return string
   */
  public function publication_state_as_icon ($size = Sixteen_px)
  {
    $icon_name = $this->publication_state_icon_url();

    return $this->app->resolve_icon_as_html ($icon_name, $size, $this->publication_state_as_text());
  }

  /**
   * @return string
   * @throws UNKNOWN_VALUE_EXCEPTION
   */
  public function publication_state_icon_url()
  {
    switch ($this->publication_state)
    {
      case History_item_silent:
        $icon_name = '{icons}indicators/not_published';
        break;
      case History_item_sent:
        $icon_name = '{icons}indicators/published';
        break;
      case History_item_needs_send:
        $icon_name = '{icons}indicators/queued_for_publication';
        break;
      default:
        throw new UNKNOWN_VALUE_EXCEPTION($this->publication_state);
    }
    return $icon_name;
  }

  /**
   * An HTML icon for the kind of transition.
   * This can be various things, like 'Deleted', 'Restored', 'Edited', etc.
   * @param string $size The size of image to return.
   * @return string
   */
  public function kind_as_icon ($size = Sixteen_px)
  {
    return $this->app->resolve_icon_as_html ($this->kind_icon_url(), $size, $this->kind);
  }

  /**
   * Description transformed into HTML.
   * If no specific munger is provided, the one from {@link OBJECT_IN_FOLDER::html_formatter()} is used.
   * @param HTML_MUNGER $munger
   * @return string
   */
  public function description_as_html ($munger = null)
  {
    return $this->_text_as_html ($this->description, $munger);
  }

  /**
   * System description transformed into HTML.
   * If no specific munger is provided, the one from {@link OBJECT_IN_FOLDER::html_formatter()} is used.
   * @param HTML_MUNGER $munger
   * @return string
   */
  public function system_description_as_html ($munger = null)
  {
    $sys_desc = $this->system_description;
    if (! $this->exists ())
    {
      if (isset ($this->_differences))
      {
        $sys_desc = implode ("\n", $this->_differences);
      }
    }

    return $this->_text_as_html ("<ol>$sys_desc</ol>", $munger);
  }

  /**
   * Description transformed into formatted plain text.
   * If no specific munger is provided, the one from {@link OBJECT_IN_FOLDER::plain_text_formatter()} is used.
   * @param PLAIN_TEXT_MUNGER $munger
   * @return string
   */
  public function description_as_plain_text ($munger = null)
  {
    return $this->_text_as_plain_text ($this->description, $munger);
  }

  /**
   * System description transformed into formatted plain text.
   * If no specific munger is provided, the one from {@link OBJECT_IN_FOLDER::plain_text_formatter()} is used.
   * @param PLAIN_TEXT_MUNGER $munger
   * @return string
   */
  public function system_description_as_plain_text ($munger = null)
  {
    $sys_desc = $this->system_description;
    if (! $this->exists ())
    {
      if (isset ($this->_differences))
      {
        $sys_desc = implode ("\n", $this->_differences);
      }
    }

    return $this->_text_as_plain_text ("<ol>$sys_desc</ol>", $munger);
  }

  /**
   * Set the object on which the history item occurs.
   * @param AUDITABLE $obj
   * @access private
   */
  public function set_object ($obj)
  {
    $this->update_object ($obj);
  }

  /**
   * Update the object id internally.
   * If the object was just created, then the id has just become available. Override this function to set
   * internal fields that depend on the object id.
   * @param AUDITABLE $obj
   * @access private
   */
  public function update_object ($obj)
  {
    $this->object_id = $obj->id;

    // deliberately make a copy here, so the reference is broken and the object can later be used
    // to compare against another object to see whether it has changed.

    $this->_object = clone($obj);
  }

  /**
   * Set the containing folder for the object.
   * @access private
   */
  public function set_parent_folder ($fldr)
  {
    $this->_parent_folder = $fldr;
  }

  /**
   * Add a difference to the system description.
   * @param string $text
   */
  public function record_difference ($text)
  {
    if ($text)
    {
      $this->_differences [] = $text;
    }
  }

  /**
   * Were differences discovered and logged by this history item?
   * In order to get a sensible answer, one should first set the object using {@link set_object()}, apply
   * changes to the object, then call {@link record_differences()} with the changed object. Call this function
   * to determine whether anything worth storing has changed. If so, storing the history item will record a detailed log
   * of information about the exact differences between the objects.
   * @return boolean
   * @access private
   */
  public function differences_exist ()
  {
    return $this->_is_new || (sizeof ($this->_differences) > 0);
  }

  /**
   * Is the passed-in object different from this history item's?
   * The object is compared to that previously set with {@link set_object()}. The objects must be the same. That
   * is, their ids must match or the function throws an exception. This function is used to track differences between
   * versions of an object, not to detect differences between objects.
   * @param AUDITABLE $obj
   * @access private
   */
  public function record_differences ($obj)
  {
    $objects_are_same_or_new = ($obj->exists () == $this->_object->exists ()) && ($obj->id == $this->_object->id);
    $this->assert ($objects_are_same_or_new, "Cannot compare two different objects (expected [{$this->_object->id}], got [$obj->id]", 'record_differences', 'HISTORY_ITEM');

    if (! $obj->exists ())
    {
      if (! isset ($this->kind))
      {
        $this->kind = $obj->history_item_kind_for_new ();
      }
      $this->_is_new = true;
    }
    else
    {
      if (! isset ($this->kind))
      {
        $this->kind = History_item_updated;
      }
      $this->_record_differences ($this->_object, $obj);
    }
  }
  
  /**
   * Assign the {@link $title} and {@link $system_description} if not already assigned.
   */
  public function prepare_for_storage ()
  {
    // flatten the differences list into text (if this history item is being created)

    if (! $this->exists ())
    {
      if (isset ($this->_differences))
      {
        $this->system_description = implode ("\n", $this->_differences);
      }
    }

    // Assign a default title, if needed.

    if (! isset ($this->title) || ! $this->title)
    {
      $this->title = $this->_make_default_title ();
    }
  }

  /**
   * @param DATABASE $db Database from which to load values.
   */
  public function load ($db)
  {
    parent::load ($db);
    $this->object_type = $db->f ('object_type');
    $this->object_id = $db->f ('object_id');
    $this->user_id = $db->f ('user_id');
    $this->time_created->set_from_iso ($db->f ('time_created'));
    $this->publication_state = $db->f ('publication_state');
    $this->access_id = $db->f ('access_id');
    $this->kind = $db->f ('kind');
    $this->title = $db->f ('title');
    $this->description = $db->f ('description');
    $this->system_description = $db->f ('system_description');
  }

  /**
   * @param SQL_STORAGE $storage Store values to this object.
   */
  public function store_to ($storage)
  {
    $this->prepare_for_storage ();

    parent::store_to ($storage);
    $tname = $this->table_name ();
    $storage->add ($tname, 'object_type', Field_type_string, $this->object_type, Storage_action_create);
    $storage->add ($tname, 'object_id', Field_type_integer, $this->object_id, Storage_action_create);
    $storage->add ($tname, 'user_id', Field_type_integer, $this->user_id, Storage_action_create);
    $storage->add ($tname, 'time_created', Field_type_date_time, $this->time_created, Storage_action_create);
    $storage->add ($tname, 'access_id', Field_type_integer, $this->access_id, Storage_action_create);
    $storage->add ($tname, 'publication_state', Field_type_string, $this->publication_state);
    $storage->add ($tname, 'kind', Field_type_string, $this->kind);
    $storage->add ($tname, 'title', Field_type_string, $this->title);
    $storage->add ($tname, 'description', Field_type_string, $this->description);
    $storage->add ($tname, 'system_description', Field_type_string, $this->system_description, Storage_action_create);
  }

  /* Final preparation before storing to the database.
   * @access private
   */
  protected function _pre_store ()
  {
    parent::_pre_store ();

    $this->user_id = $this->login->id;
    $this->time_created->set_now ();
  }

  /**
   * Record class-specific differences.
   * 'orig' is the same as {@link $_object}, but is passed here for convenience.
   * @param NAMED_OBJECT $orig The original object.
   * @param NAMED_OBJECT $new The new object.
   * @internal param \AUDITABLE $obj
   * @access private
   */
  protected function _record_differences ($orig, $new) {}

  /**
   * Record difference in string, if any.
   * @param string $name
   * @param string $orig_text
   * @param string $new_text
   * @access private
   */
  protected function _record_string_difference ($name, $orig_text, $new_text)
  {
    if ($orig_text != $new_text)
    {
      if (! $orig_text) $orig_text = '[not set]';
      if (! $new_text) $new_text = '[not set]';
      $this->record_difference ("$name changed to $new_text from $orig_text.");
    }
  }

  /**
   * Record difference between two objects, if any.
   * Pass in the name to use in the message and the two options. You can also specify your own text to use when
   * one of the objects is not set.
   * @param string $name
   * @param NAMED_OBJECT $orig The original object.
   * @param NAMED_OBJECT $new The new object.
   * @param string $empty_text Text to use when object is not set.
   * @param string $prefix Show this after each object's title.
   * @param string $suffix
   * @access private
   */
  protected function _record_object_difference ($name, $orig, $new, $empty_text = '[not set]', $prefix = '', $suffix = '')
  {
    if (isset ($orig))
    {
      $t = $orig->title_formatter ();
      $t->max_visible_output_chars = 0;
      $orig_title = $prefix . $orig->title_as_plain_text ($t) . $suffix;
    }
    else
    {
      $orig_title = $empty_text;
    }
    if (isset ($new))
    {
      $t = $new->title_formatter ();
      $t->max_visible_output_chars = 0;
      $new_title = $prefix . $new->title_as_plain_text ($t) . $suffix;
    }
    else
    {
      $new_title = $empty_text;
    }
    $this->_record_string_difference ($name, $orig_title, $new_title);
  }

  /**
   * Record difference in times, if any
   * @param string $name
   * @param DATE_TIME $orig_date
   * @param DATE_TIME $new_date
   * @param string $type Type of date-time formatting to use.
   * @access private
   */
  protected function _record_time_difference ($name, $orig_date, $new_date, $type = Date_time_format_short_date_and_time)
  {
    if (! $orig_date->equals ($new_date))
    {
      $f = $orig_date->formatter ();
      $f->set_type_and_clear_flags ($type);
      if ($orig_date->is_valid ())
      {
        $orig_text = $orig_date->format ($f);
      }
      else
      {
        $orig_text = '[not set]';
      }
      if ($new_date->is_valid ())
      {
        $new_text = $new_date->format ($f);
      }
      else
      {
        $new_text = '[not set]';
      }
      $this->_record_string_difference ($name, $orig_text, $new_text);
    }
  }

  /**
   * Record difference in text size, if any.
   * @param string $name
   * @param string $orig_text
   * @param string $new_text
   * @access private
   */
  protected function _record_text_difference ($name, $orig_text, $new_text)
  {
    if ($orig_text != $new_text)
    {
      $this->record_difference ($name . ' was changed. (' . strlen ($orig_text) . ' bytes => ' . strlen ($new_text) . ' bytes)');
    }
  }

  /**
   * Record difference for booleans, if any.
   * @param string $name
   * @param string $orig_value
   * @param string $new_value
   * @access private
   */
  protected function _record_boolean_difference ($name, $orig_value, $new_value)
  {
    if ($orig_value != $new_value)
    {
      if ($new_value)
      {
        $this->record_difference ($name . ' was turned on.');
      }
      else
      {
        $this->record_difference ($name . ' was turned off.');
      }
    }
  }

  /**
   * Return a title inferred from this object's properties.
   * If an history item is stored without a title, this is called to generate the most
   * appropriate default title.
   * @return string
   */
  protected function _make_default_title ()
  {
    $Result = $this->supported_kind_as_text ();
    if (!empty ($Result))
    {
      $Result = ucfirst ($Result);
    }
    else
    {
      $num_diffs = sizeof ($this->_differences);
      if ($num_diffs)
      {
        $Result = $this->_differences [$num_diffs - 1];
      }
      else
      {
        $Result = 'Updated';
      }
    }

    return $Result;
  }

  /**
   * @return string
   */
  public function raw_title ()
  {
    return $this->title;
  }

  /**
   * Name of the home page name for this object.
   * @return string
   */
  public function page_name ()
  {
    return $this->app->page_names->history_item_home;
  }

  /**
   * Name of this object's database table.
   * @return string
   * @access private
   */
  public function table_name ()
  {
    return $this->app->table_names->history_items;
  }

  /**
   * Returns text only if this history item uses that kind.
   * @return string
   */
  public function supported_kind_as_text ()
  {
    switch ($this->kind)
    {
    case History_item_created:
      return 'created';
    default:
      return '';
    }
  }

  /**
   * Return the basic name of this kind's icon.
   * @return string
   * @access private
   */
  public function kind_icon_url ()
  {
    switch ($this->kind)
    {
    case History_item_created:
      return '{icons}indicators/created';
    default:
      return '{icons}indicators/updated';
    }
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
        include_once ('webcore/gui/history_item_renderer.php');
        return new HISTORY_ITEM_RENDERER ($this->app, $options);
      case Handler_mail:
        include_once ('webcore/mail/history_item_mail_renderer.php');
        return new HISTORY_ITEM_MAIL_RENDERER ($this->app);
      default:
        return parent::_default_handler_for ($handler_type, $options);
    }
  }

  /**
   * Link to the folder in which this history item's {@link $_object} exists.
   * @var FOLDER
   * @access private
   */
  protected $_parent_folder;

  /**
   * Object for which the history item was created.
   * This is used with {@link differences_exist()} to determine whether the history item actually occurred and
   * should be stored or not. This is only used when the history item is created; it is not used when the history item
   * is loaded from the database.
   * @var AUDITABLE
   * @access private
   */
  protected $_object;

  /**
   * List of differences between the original and current object.
   * This is only used when the history item is being created; it is not used when the history item
   * is loaded from the database.
   * @var string[]
   * @access private
   */
  protected $_differences;

  /**
   * True if the object does not exist yet.
   * @var boolean
   * @access private
   */
  protected $_is_new;
}