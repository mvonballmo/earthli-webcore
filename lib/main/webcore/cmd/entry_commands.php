<?php

/**
 * @copyright Copyright (c) 2002-2010 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage command
 * @version 3.3.0
 * @since 2.7.0
 * @access private
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

/** */
require_once ('webcore/cmd/commands.php');

/**
 * Return the commands for an {@link ENTRY}.
 * @package webcore
 * @subpackage command
 * @version 3.3.0
 * @since 2.7.0
 * @access private
 */
class ENTRY_COMMANDS extends COMMANDS
{
  /**
   * @param ENTRY $entry Configure commands for this object.
   */
  public function __construct ($entry)
  {
    parent::__construct ($entry->app);

    $this->append_group ('Edit');
    $this->_add_editors ($entry);
    $this->append_group ('View');
    $this->_add_viewers ($entry);
    $this->append_group ('Create');
    $this->_add_creators ($entry);
  }

  /**
   * Add commands that edit the entry.
   * @param ENTRY $entry
   * @access private
   */
  protected function _add_editors ($entry)
  {
    $cmd = $this->make_command ();
    $cmd->id = 'edit';
    $cmd->caption = 'Edit';
    $cmd->link = "edit_entry.php?id=$entry->id";
    $cmd->icon = '{icons}buttons/edit';
    $cmd->executable = $this->login->is_allowed (Privilege_set_entry, Privilege_modify, $entry);
    $cmd->importance = Command_importance_high;
    $this->append ($cmd);

    $cmd = $this->make_command ();
    $cmd->id = 'delete';
    $cmd->caption = 'Delete';
    $cmd->link = "delete_entry.php?id=$entry->id";
    $cmd->icon = '{icons}buttons/delete';
    $cmd->executable = ! $entry->deleted () && $this->login->is_allowed (Privilege_set_entry, Privilege_delete, $entry);
    $cmd->importance = Command_importance_high - 2 * Command_importance_increment;
    $this->append ($cmd);

    $cmd = $this->make_command ();
    $cmd->id = 'purge';
    $cmd->caption = 'Purge';
    $cmd->link = "purge_entry.php?id=$entry->id";
    $cmd->icon = '{icons}buttons/purge';
    $cmd->executable = $this->login->is_allowed (Privilege_set_entry, Privilege_purge, $entry);
    $cmd->importance = Command_importance_high - 2 * Command_importance_increment;
    $this->append ($cmd);

    $folder_id = $entry->parent_folder_id ();

    $cmd = $this->make_command ();
    $cmd->id = 'move';
    $cmd->caption = 'Move';
    $cmd->link = "multiple_move.php?id=$folder_id&entry_ids=$entry->id";
    $cmd->icon = '{icons}buttons/move';
    $cmd->executable = $this->login->is_allowed (Privilege_set_entry, Privilege_modify, $entry);
    $cmd->importance = Command_importance_high - 2 * Command_importance_increment;
    $this->append ($cmd);
  }

  /**
   * Add commands that provide views on the entry.
   * @param ENTRY $entry
   * @access private
   */
  protected function _add_viewers ($entry)
  {
    $folder_id = $entry->parent_folder_id ();

    $cmd = $this->make_command ();
    $cmd->id = 'print';
    $cmd->caption = 'Print';
    $cmd->link = "multiple_print.php?id=$folder_id&entry_ids=$entry->id";
    $cmd->icon = '{icons}buttons/print';
    $cmd->executable = true;
    $cmd->importance = Command_importance_high - Command_importance_increment;
    $this->append ($cmd);

    $cmd = $this->make_command ();
    $cmd->id = 'send';
    $cmd->caption = 'Send';
    $cmd->link = "send_entry.php?id=$entry->id";
    $cmd->icon = '{icons}buttons/send';
    $cmd->executable = true;
    $cmd->importance = Command_importance_high - Command_importance_increment;
    $this->append ($cmd);

    $cmd = $this->make_command ();
    $cmd->id = 'plain_text';
    $cmd->caption = 'Plain text';
    $cmd->link = "view_entry_plain_text.php?id=$entry->id";
    $cmd->icon = '{icons}indicators/text';
    $cmd->executable = true;
    $cmd->importance = Command_importance_high - Command_importance_increment;
    $this->append ($cmd);

    $cmd = $this->make_command ();
    $cmd->id = 'history';
    $cmd->caption = 'History';
    $cmd->link = "view_entry_history.php?id=$entry->id";
    $cmd->icon = '{icons}buttons/history';
    $cmd->executable = $this->login->is_allowed (Privilege_set_entry, Privilege_view_history, $entry);
    $cmd->importance = Command_importance_low;
    $this->append ($cmd);
  }

  /**
   * Add commands that attaches objects to the entry.
   * @param ENTRY $entry
   * @access private
   */
  protected function _add_creators ($entry)
  {
    $cmd = $this->make_command ();
    $cmd->id = 'comment';
    $cmd->caption = 'Add comment';
    $cmd->link = "create_comment.php?id=$entry->id";
    $cmd->icon = '{icons}buttons/reply';
    $cmd->executable = $this->login->is_allowed (Privilege_set_comment, Privilege_create, $entry);
    $cmd->importance = Command_importance_high - Command_importance_increment;
    $this->append ($cmd);

    $cmd = $this->make_command ();
    $cmd->id = 'attach';
    $cmd->caption = 'Attach';
    $cmd->link = "create_attachment.php?id=$entry->id&type=" . History_item_entry;
    $cmd->icon = '{icons}buttons/attach';
    $cmd->executable = $this->login->is_allowed (Privilege_set_attachment, Privilege_create, $entry)
                       && $this->login->is_allowed (Privilege_set_attachment, Privilege_upload, $entry);
    $cmd->importance = Command_importance_high;
    $this->append ($cmd);

    $folder_id = $entry->parent_folder_id ();

    $cmd = $this->make_command ();
    $cmd->id = 'copy';
    $cmd->caption = 'Copy';
    $cmd->link = "multiple_copy.php?id=$folder_id&entry_ids=$entry->id";
    $cmd->icon = '{icons}buttons/copy';
    $cmd->executable = true;
    $cmd->importance = Command_importance_high - 2 * Command_importance_increment;
    $this->append ($cmd);

    $folder = $entry->parent_folder ();

    if (! $folder->is_organizational ())
    {
      $cmd = $this->make_command ();
      $cmd->id = 'clone';
      $cmd->caption = 'Clone';
      $cmd->link = "clone_entry.php?id=$entry->id";
      $cmd->icon = '{icons}buttons/clone';
      $cmd->executable = $this->login->is_allowed (Privilege_set_entry, Privilege_create, $entry);
      $cmd->importance = Command_importance_low;
      $this->append ($cmd);
    }
  }
}

/**
 * Return the commands for an {@link DRAFTABLE_ENTRY}.
 * @package webcore
 * @subpackage command
 * @version 3.3.0
 * @since 2.7.1
 * @access private
 */
class DRAFTABLE_ENTRY_COMMANDS extends ENTRY_COMMANDS
{
  /**
   * Add commands that edit the entry.
   * @param ENTRY $entry
   * @access private
   */
  protected function _add_editors ($entry)
  {
    $last_page = urlencode ($this->env->url (Url_part_all));

    parent::_add_editors ($entry);

    $cmd = $this->make_command ();
    $cmd->id = 'publish';
    $cmd->caption = 'Publish';
    $cmd->link = "set_published.php?id=$entry->id&last_page=$last_page";
    $cmd->icon = '{icons}buttons/ship';
    $cmd->executable = $this->login->is_allowed (Privilege_set_entry, Privilege_modify, $entry) && ($entry->unpublished ());
    $cmd->importance = Command_importance_high;
    $this->append ($cmd);

    $cmd = $this->make_command ();
    $cmd->id = 'abandon';
    $cmd->caption = 'Abandon';
    $cmd->link = "set_abandoned.php?id=$entry->id&last_page=$last_page";
    $cmd->icon = '{icons}buttons/abandon';
    $cmd->executable = ! $entry->abandoned () && $this->login->is_allowed (Privilege_set_entry, Privilege_modify, $entry) && ($entry->unpublished ());
    $cmd->importance = Command_importance_default;
    $this->append ($cmd);

    $cmd = $this->make_command ();
    $cmd->id = 'queue';
    $cmd->caption = 'Queue';
    $cmd->link = "set_queued.php?id=$entry->id&last_page=$last_page";
    $cmd->icon = '{icons}buttons/queue';
    $cmd->executable = ! $entry->queued () && $this->login->is_allowed (Privilege_set_entry, Privilege_modify, $entry) && ($entry->unpublished ());
    $cmd->importance = Command_importance_default;
    $this->append ($cmd);

    $cmd = $this->make_command ();
    $cmd->id = 'unpublish';
    $cmd->caption = 'Unpublish';
    $cmd->link = "set_draft.php?id=$entry->id&last_page=$last_page";
    $cmd->icon = '{icons}buttons/unpublish';
    $cmd->executable = $this->login->is_allowed (Privilege_set_entry, Privilege_modify, $entry) && ($entry->visible ());
    $cmd->importance = Command_importance_default;
    $this->append ($cmd);
  }
}

?>