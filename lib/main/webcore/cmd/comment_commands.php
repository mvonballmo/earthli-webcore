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
 * Return the commands for a {@link COMMENT}.
 * @package webcore
 * @subpackage command
 * @version 3.3.0
 * @since 2.7.0
 * @access private
 */
class COMMENT_COMMANDS extends COMMANDS
{
  /**
   * @param COMMENT $comment Configure commands for this object.
   */
  public function __construct ($comment)
  {
    parent::__construct ($comment->app);

    $cmd = $this->make_command ();
    $cmd->id = 'reply';
    $cmd->caption = 'Reply';
    $cmd->link = "create_comment.php?id=$comment->entry_id&parent_id=$comment->id";
    $cmd->icon = '{icons}buttons/reply';
    $cmd->executable = $this->login->is_allowed (Privilege_set_comment, Privilege_create, $comment);
    $cmd->importance = Command_importance_high;
    $this->append ($cmd);

    $cmd = $this->make_command ();
    $cmd->id = 'subscribe';
    $cmd->caption = 'Subscribe';
    $cmd->link = 'subscribe_to_comment.php?id=' . $comment->id . '&email=' . $this->login->email . '&subscribed=1';
    $cmd->icon = '{icons}indicators/subscribed';
    $cmd->executable = $this->login->email && $this->login->is_allowed (Privilege_set_comment, Privilege_view, $comment);
    $cmd->importance = Command_importance_high - Command_importance_increment;
    $this->append ($cmd);

    $cmd = $this->make_command ();
    $cmd->id = 'edit';
    $cmd->caption = 'Edit';
    $cmd->link = "edit_comment.php?id=$comment->id";
    $cmd->icon = '{icons}buttons/edit';
    $cmd->executable = $this->login->is_allowed (Privilege_set_comment, Privilege_modify, $comment);
    $cmd->importance = Command_importance_high - Command_importance_increment;
    $this->append ($cmd);

    $cmd = $this->make_command ();
    $cmd->id = 'delete';
    $cmd->caption = 'Delete';
    $cmd->link = "delete_comment.php?id=$comment->id";
    $cmd->icon = '{icons}buttons/delete';
    $cmd->executable = ! $comment->deleted () && $this->login->is_allowed (Privilege_set_comment, Privilege_delete, $comment);
    $cmd->importance = Command_importance_high - 2 * Command_importance_increment;
    $this->append ($cmd);

    $cmd = $this->make_command ();
    $cmd->id = 'purge';
    $cmd->caption = 'Purge';
    $cmd->link = "purge_comment.php?id=$comment->id";
    $cmd->icon = '{icons}buttons/purge';
    $cmd->executable = $this->login->is_allowed (Privilege_set_comment, Privilege_purge, $comment);
    $cmd->importance = Command_importance_high - 2 * Command_importance_increment;
    $this->append ($cmd);

    $cmd = $this->make_command ();
    $cmd->id = 'attach';
    $cmd->caption = 'Attach';
    $cmd->link = "create_attachment.php?id=$comment->id&type=" . History_item_comment;
    $cmd->icon = '{icons}buttons/attach';
    $cmd->executable = $this->login->is_allowed (Privilege_set_attachment, Privilege_create, $comment)
                       && $this->login->is_allowed (Privilege_set_attachment, Privilege_upload, $comment);
    $cmd->importance = Command_importance_high - 2 * Command_importance_increment;
    $this->append ($cmd);

    $cmd = $this->make_command ();
    $cmd->id = 'send';
    $cmd->caption = 'Send';
    $cmd->link = "send_comment.php?id=$comment->id";
    $cmd->icon = '{icons}buttons/send';
    $cmd->executable = true;
    $cmd->importance = Command_importance_high - Command_importance_increment;
    $this->append ($cmd);

    $cmd = $this->make_command ();
    $cmd->id = 'history';
    $cmd->caption = 'History';
    $cmd->link = "view_comment_history.php?id=$comment->id";
    $cmd->icon = '{icons}buttons/history';
    $cmd->executable = $this->login->is_allowed (Privilege_set_comment, Privilege_view_history, $comment);
    $cmd->importance = Command_importance_low;
    $this->append ($cmd);
  }
}

?>