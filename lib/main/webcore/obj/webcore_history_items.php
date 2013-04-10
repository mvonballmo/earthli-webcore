<?php

/**
 * @copyright Copyright (c) 2002-2013 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage history
 * @version 3.4.0
 * @since 2.4.0
 * @access private
 */

/****************************************************************************

Copyright (c) 2002-2013 Marco Von Ballmoos

This file is part of earthli WebCore.

earthli WebCore is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

earthli Projects is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with earthli WebCore; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

For more information about the earthli WebCore, visit:

http://www.earthli.com/software/webcore/

****************************************************************************/

/** */
require_once ('webcore/obj/history_item.php');

/**
 * Manages the audit trail of a basic {@link AUDITABLE} object.
 * @package webcore
 * @subpackage history
 * @version 3.4.0
 * @since 2.5.0
 * @access private
 */
class AUDITABLE_HISTORY_ITEM extends HISTORY_ITEM
{
  /**
   * Record class-specific differences.
   * 'orig' is the same as {@link $_object}, but is passed here for convenience.
   * @param AUDITABLE $orig
   * @param AUDITABLE $new
   * @access private
   */
  protected function _record_differences ($orig, $new)
  {
    parent::_record_differences ($orig, $new);

    $this->_record_time_difference ('Time created', $orig->time_created, $new->time_created);

    if ($orig->creator_id != $new->creator_id)
    {
      $this->_record_object_difference ('Creator', $orig->creator (), $new->creator ());
    }
  }
}

/**
 * Manages the audit trail of a {@link CONTENT_OBJECT}.
 * Adds support for differencing the title and description.
 * @package webcore
 * @subpackage history
 * @version 3.4.0
 * @since 2.5.0
 * @access private
 */
class CONTENT_OBJECT_HISTORY_ITEM extends AUDITABLE_HISTORY_ITEM
{
  /**
   * Record class-specific differences.
   * 'orig' is the same as {@link $_object}, but is passed here for convenience.
   * @param OBJECT_IN_FOLDER $orig
   * @param OBJECT_IN_FOLDER $new
   * @access private
   */
  protected function _record_differences ($orig, $new)
  {
    parent::_record_differences ($orig, $new);
    $this->_record_string_difference ('Title', $orig->title, $new->title);
    $this->_record_text_difference ('Description', $orig->description, $new->description);
  }
}

/**
 * Manages the audit trail of a {@link OBJECT_IN_FOLDER}.
 * Adds support for the {@link History_item_deleted}, {@link History_item_restored}, {@link History_item_locked}
 * {@link History_item_hidden}, {@link History_item_hidden_update} history item kinds.
 * @package webcore
 * @subpackage history
 * @version 3.4.0
 * @since 2.5.0
 * @access private
 */
class OBJECT_IN_FOLDER_HISTORY_ITEM extends CONTENT_OBJECT_HISTORY_ITEM
{
  /**
   * Update the object id internally.
   * If the object was just created, then the id has just become available. Override this function to set
   * internal fields that depend on the object id.
   * @param OBJECT_IN_FOLDER $obj
   * @access private
   */
  public function update_object ($obj)
  {
    parent::update_object ($obj);
    $this->access_id = $obj->parent_folder_id ();
  }

  /**
   * Record class-specific differences.
   * 'orig' is the same as {@link $_object}, but is passed here for convenience.
   * @param OBJECT_IN_FOLDER $orig
   * @param OBJECT_IN_FOLDER $new
   * @access private
   */
  protected function _record_differences ($orig, $new)
  {
    parent::_record_differences ($orig, $new);

    if ($orig->state != $new->state)
    {
      $this->kind = $orig->history_item_kind_for_transition_to ($new->state);
      $orig_title = $orig->state_as_string ();
      $new_title = $new->state_as_string ();
      $this->_record_string_difference ('State', $orig_title, $new_title);
    }
    else
    {
      if ($new->invisible ())
      {
        $this->kind = History_item_hidden_update;
      }
      else
      {
        $this->kind = History_item_updated;
      }
    }

    if ($orig->owner_id != $new->owner_id)
    {
      $this->_record_object_difference ('Owner', $orig->owner (), $new->owner ());
    }

    if ($orig->parent_folder_id () != $new->parent_folder_id ())
    {
      $this->_record_object_difference ('Folder', $orig->parent_folder (), $new->parent_folder ());
    }
  }

  /**
   * Returns text only if this history item uses that kind.
   * @return string
   */
  public function supported_kind_as_text ()
  {
    switch ($this->kind)
    {
    case History_item_deleted:
      return 'deleted';
    case History_item_hidden:
      return 'hidden';
    case History_item_restored:
      return 'restored';
    case History_item_locked:
      return 'locked';
    default:
      return parent::supported_kind_as_text ();
    }
  }

  /**
   * Return the basic name of this kind's icon.
   * @return string
   */
  public function kind_icon_url ()
  {
    switch ($this->kind)
    {
    case History_item_deleted:
      return '{icons}indicators/deleted';
    case History_item_restored:
      return '{icons}indicators/restored';
    case History_item_hidden:
      return '{icons}indicators/hidden';
    case History_item_hidden_update:
      return '{icons}indicators/hidden_update';
    case History_item_locked:
      return '{icons}indicators/locked';
    default:
      return parent::kind_icon_url ();
    }
  }
}

/**
 * Manages the audit trail of a {@link FOLDER}.
 * @package webcore
 * @subpackage history
 * @version 3.4.0
 * @since 2.4.0
 * @access private
 */
class FOLDER_HISTORY_ITEM extends OBJECT_IN_FOLDER_HISTORY_ITEM
{
  /**
   * Which kind of object is this?
   * @var string
   */
  public $object_type = History_item_folder;

  /**
   * Record class-specific differences.
   * 'orig' is the same as {@link $_object}, but is passed here for convenience.
   * @param FOLDER $orig
   * @param FOLDER $new
   * @access private
   */
  protected function _record_differences ($orig, $new)
  {
    parent::_record_differences ($orig, $new);

    if ($orig->permissions_id != $new->permissions_id)
    {
      $this->_record_object_difference ('Permissions', $orig->permissions_folder (), $new->permissions_folder ());
    }

    if ($orig->parent_id != $new->parent_id)
    {
      $this->_record_object_difference ('Parent', $orig->parent_folder (), $new->parent_folder ());
    }

    $this->_record_boolean_difference ('Organizational', $orig->organizational, $new->organizational);
    $this->_record_string_difference ('Icon', $orig->icon_url, $new->icon_url);
    $this->_record_text_difference ('Summary', $orig->summary, $new->summary);
  }
}

/**
 * Manages the audit trail of a {@link ENTRY}.
 * Adds support for the {@link History_item_published} history item kind.
 * @package webcore
 * @subpackage history
 * @version 3.4.0
 * @since 2.4.0
 * @access private
 */
class ENTRY_HISTORY_ITEM extends OBJECT_IN_FOLDER_HISTORY_ITEM
{
  /**
   * Which kind of object is this?
   * @var string
   */
  public $object_type = History_item_entry;

  /**
   * Returns text only if this history item uses that kind.
   * @return string
   */
  public function supported_kind_as_text ()
  {
    switch ($this->kind)
    {
    case History_item_abandoned:
      return 'abandoned';
    case History_item_queued:
      return 'queued';
    case History_item_published:
      return 'published';
    case History_item_unpublished:
      return 'unpublished';
    default:
      return parent::supported_kind_as_text ();
    }
  }

  /**
   * Return the basic name of this kind's icon.
   * @return string
   */
  public function kind_icon_url ()
  {
    switch ($this->kind)
    {
    case History_item_published:
      return '{icons}indicators/released';
    case History_item_abandoned:
      return '{icons}buttons/abandon';
    case History_item_queued:
      return '{icons}buttons/queue';
    case History_item_unpublished:
      return '{icons}buttons/unpublish';
    default:
      return parent::kind_icon_url ();
    }
  }
}

/**
 * Manages the audit trail of a {@link COMMENT}.
 * @package webcore
 * @subpackage history
 * @version 3.4.0
 * @since 2.4.0
 * @access private
 */
class COMMENT_HISTORY_ITEM extends OBJECT_IN_FOLDER_HISTORY_ITEM
{
  /**
   * Which kind of object is this?
   * @var string
   */
  public $object_type = History_item_comment;

  /**
   * Record class-specific differences.
   * 'orig' is the same as {@link $_object}, but is passed here for convenience.
   * @param COMMENT $orig
   * @param COMMENT $new
   * @access private
   */
  protected function _record_differences ($orig, $new)
  {
    parent::_record_differences ($orig, $new);

    if ($orig->kind != $new->kind)
    {
      $orig_kind = $orig->icon_properties ();
      $new_kind = $new->icon_properties ();
      $this->_record_string_difference ('Icon', $orig_kind->title, $new_kind->title);
    }
  }
}

/**
 * Manages the audit trail of an {@link ATTACHMENT}.
 * @package webcore
 * @subpackage history
 * @version 3.4.0
 * @since 2.5.0
 * @access private
 */
class ATTACHMENT_HISTORY_ITEM extends OBJECT_IN_FOLDER_HISTORY_ITEM
{
  /**
   * Which kind of object is this?
   * @var string
   */
  public $object_type = History_item_attachment;

  /**
   * Record class-specific differences.
   * 'orig' is the same as {@link $_object}, but is passed here for convenience.
   * @param COMMENT $orig
   * @param COMMENT $new
   * @access private
   */
  protected function _record_differences ($orig, $new)
  {
    parent::_record_differences ($orig, $new);

    if ($orig->original_file_name != $new->original_file_name)
    {
      $this->_record_string_difference ('File name', $orig->original_file_name, $new->original_file_name);
    }
    else
    {
      if ($orig->file_name != $new->file_name)
      {
        $this->_record_string_difference ('File name', $orig->file_name, $new->file_name);
      }
    }
  }
}

/**
 * Manages the audit trail of a {@link USER}.
 * @package webcore
 * @subpackage history
 * @version 3.4.0
 * @since 2.4.0
 * @access private
 */
class USER_HISTORY_ITEM extends CONTENT_OBJECT_HISTORY_ITEM
{
  /**
   * Which kind of object is this?
   * @var string
   */
  public $object_type = History_item_user;

  /**
   * Set the object on which the history item occurs.
   * @param USER $obj
   * @access private
   */
  public function update_object ($obj)
  {
    parent::update_object ($obj);
    $this->access_id = $obj->id;
  }

  /**
   * Record class-specific differences.
   * 'orig' is the same as {@link $_object}, but is passed here for convenience.
   * @param USER $orig
   * @param USER $new
   * @access private
   */
  protected function _record_differences ($orig, $new)
  {
    parent::_record_differences ($orig, $new);

    $this->_record_text_difference ('Password', $orig->password, $new->password);

    if ($orig->real_first_name != $new->real_first_name)
    {
      $this->_record_string_difference ('First name', $orig->real_first_name, $new->real_first_name);
    }

    if ($orig->real_last_name != $new->real_last_name)
    {
      $this->_record_string_difference ('Last name', $orig->real_last_name, $new->real_last_name);
    }

    if ($orig->home_page_url != $new->home_page_url)
    {
      $this->_record_string_difference ('Home page', $orig->home_page_url, $new->home_page_url);
    }

    if ($orig->picture_url != $new->picture_url)
    {
      $this->_record_string_difference ('Picture', $orig->picture_url, $new->picture_url);
    }

    if ($orig->email != $new->email)
    {
      $this->_record_string_difference ('Email', $orig->email, $new->email);
    }

    if ($orig->signature != $new->signature)
    {
      $this->_record_string_difference ('Signature', $orig->signature, $new->signature);
    }

    if ($orig->icon_url != $new->icon_url)
    {
      $this->_record_string_difference ('Icon', $orig->icon_url, $new->icon_url);
    }

    if ($orig->email_visibility != $new->email_visibility)
    {
      $this->record_difference ('Email visibility was changed');
    }
  }
}

/**
 * Manages the audit trail of a {@link GROUP}.
 * @package webcore
 * @subpackage history
 * @version 3.4.0
 * @since 2.4.0
 * @access private
 */
class GROUP_HISTORY_ITEM extends CONTENT_OBJECT_HISTORY_ITEM
{
  /**
   * Which kind of object is this?
   * @var string
   */
  public $object_type = History_item_group;

  /**
   * Set the object on which the history item occurs.
   * @param GROUP $obj
   * @access private
   */
  public function update_object ($obj)
  {
    parent::update_object ($obj);
    $this->access_id = $obj->id;
  }
}

?>