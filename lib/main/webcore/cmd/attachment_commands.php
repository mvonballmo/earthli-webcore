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
 * Commands which apply to an {@link ATTACHMENT}.
 * @package webcore
 * @subpackage command
 * @version 3.3.0
 * @since 2.7.0
 * @access private
 */
class ATTACHMENT_COMMANDS extends COMMANDS
{
  /**
   * @param ATTACHMENT $attachment Configure commands for this attachment.
   */
  public function __construct ($attachment)
  {
    parent::__construct ($attachment->app);

    $cmd = $this->make_command ();
    $cmd->id = 'edit';
    $cmd->title = 'Edit';
    $cmd->link = "edit_attachment.php?id=$attachment->id&type=$attachment->type";
    $cmd->icon = '{icons}buttons/edit';
    $cmd->executable = $this->login->is_allowed (Privilege_set_attachment, Privilege_modify, $attachment);
    $cmd->importance = Command_importance_high;
    $this->append ($cmd);

    $cmd = $this->make_command ();
    $cmd->id = 'delete';
    $cmd->title = 'Delete';
    $cmd->link = "delete_attachment.php?id=$attachment->id&type=$attachment->type";
    $cmd->icon = '{icons}buttons/delete';
    $cmd->executable = ! $attachment->deleted () && $this->login->is_allowed (Privilege_set_attachment, Privilege_delete, $attachment);
    $cmd->importance = Command_importance_high - 2 * Command_importance_increment;
    $this->append ($cmd);

    $cmd = $this->make_command ();
    $cmd->id = 'purge';
    $cmd->title = 'Purge';
    $cmd->link = "purge_attachment.php?id=$attachment->id&type=$attachment->type";
    $cmd->icon = '{icons}buttons/purge';
    $cmd->executable = $this->login->is_allowed (Privilege_set_attachment, Privilege_purge, $attachment);
    $cmd->importance = Command_importance_high - 2 * Command_importance_increment;
    $this->append ($cmd);

    $cmd = $this->make_command ();
    $cmd->id = 'send';
    $cmd->title = 'Send';
    $cmd->link = "send_attachment.php?id=$attachment->id&type=$attachment->type";
    $cmd->icon = '{icons}buttons/send';
    $cmd->executable = true;
    $cmd->importance = Command_importance_high - Command_importance_increment;
    $this->append ($cmd);

    $cmd = $this->make_command ();
    $cmd->id = 'history';
    $cmd->title = 'History';
    $cmd->link = "view_attachment_history.php?id=$attachment->id&type=$attachment->type";
    $cmd->icon = '{icons}buttons/history';
    $cmd->executable = $this->login->is_allowed (Privilege_set_attachment, Privilege_view_history, $attachment);
    $cmd->importance = Command_importance_low;
    $this->append ($cmd);
  }
}

?>