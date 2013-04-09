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
 * Return the commands for a {@link FOLDER}.
 * @package webcore
 * @subpackage command
 * @version 3.3.0
 * @since 2.7.0
 * @access private
 */
class FOLDER_COMMANDS extends COMMANDS
{
  /**
   * @param FOLDER $folder Return commands for this object.
   */
  public function __construct ($folder)
  {
    parent::__construct ($folder->app);

    $this->append_group ('Edit');
    $this->_add_editors ($folder);
    $this->append_group ('View');
    $this->_add_viewers ($folder);

    $this->append_group ('Create');

    $cmd = $this->make_command ();
    $cmd->id = 'new';
    $cmd->caption = 'New folder';
    $cmd->link = "create_folder.php?id=$folder->id";
    $cmd->icon = '{icons}buttons/new_folder';
    $cmd->executable = $this->login->is_allowed (Privilege_set_folder, Privilege_create, $folder);
    $cmd->importance = Command_importance_high + Command_importance_increment;
    $this->append ($cmd);

    $this->_add_creators ($folder);
  }

  /**
   * Add commands that edit the folder.
   * @param FOLDER $folder Show commands for this folder.
   * @access private
   */
  protected function _add_editors ($folder)
  {
    $cmd = $this->make_command ();
    $cmd->id = 'subscribe';
    $cmd->caption = 'Subscribe';
    $cmd->link = 'subscribe_to_folder.php?id=' . $folder->id . '&email=' . $this->login->email . '&subscribed=1';
    $cmd->icon = '{icons}indicators/subscribed';
    $cmd->executable = $this->login->email && $this->login->is_allowed (Privilege_set_folder, Privilege_view, $folder);
    $cmd->importance = Command_importance_high - Command_importance_increment;
    $this->append ($cmd);

    $cmd = $this->make_command ();
    $cmd->id = 'edit';
    $cmd->caption = 'Edit';
    $cmd->link = "edit_folder.php?id=$folder->id";
    $cmd->icon = '{icons}buttons/edit';
    $cmd->executable = $this->login->is_allowed (Privilege_set_folder, Privilege_modify, $folder);
    $cmd->importance = Command_importance_high;
    $this->append ($cmd);

    $cmd = $this->make_command ();
    $cmd->id = 'delete';
    $cmd->caption = 'Delete';
    $cmd->link = "delete_folder.php?id=$folder->id";
    $cmd->icon = '{icons}buttons/delete';
    $cmd->executable = (! $folder->deleted () && ! $folder->is_root () &&
                        $this->login->is_allowed (Privilege_set_folder, Privilege_delete, $folder));
    $cmd->importance = Command_importance_high - 2 * Command_importance_increment;
    $this->append ($cmd);

    $cmd = $this->make_command ();
    $cmd->id = 'purge';
    $cmd->caption = 'Purge';
    $cmd->link = "purge_folder.php?id=$folder->id";
    $cmd->icon = '{icons}buttons/purge';
    $cmd->executable = ($this->login->is_allowed (Privilege_set_folder, Privilege_purge, $folder)
                        && ! $folder->is_root ());
    $cmd->importance = Command_importance_high - 2 * Command_importance_increment;
    $this->append ($cmd);

    $parent_id = $folder->parent_folder_id ();
    
    if ($parent_id != 0)
    {
      $cmd = $this->make_command ();
      $cmd->id = 'move';
      $cmd->caption = 'Move';
      $cmd->link = "multiple_move.php?id=$parent_id&folder_ids=$folder->id";
      $cmd->icon = '{icons}buttons/move';
      $cmd->executable = $this->login->is_allowed (Privilege_set_folder, Privilege_modify, $folder);
      $cmd->importance = Command_importance_high - 2 * Command_importance_increment;
      $this->append ($cmd);
    }
  }

  /**
   * Add buttons that provide views on the folder.
   * @param FOLDER $folder
   * @access private
   */
  protected function _add_viewers ($folder)
  {
    $cmd = $this->make_command ();
    $cmd->id = 'security';
    $cmd->caption = 'Manage security';
    $cmd->link = "view_folder_permissions.php?id=$folder->id";
    $cmd->icon = '{icons}buttons/security';
    $cmd->executable = $this->login->is_allowed (Privilege_set_folder, Privilege_secure, $folder);
    $cmd->importance = Command_importance_low;
    $this->append ($cmd);

    $cmd = $this->make_command ();
    $cmd->id = 'subscribers';
    $cmd->caption = 'Manage subscribers';
    $cmd->link = "view_folder_subscriptions.php?id=$folder->id";
    $cmd->icon = '{icons}buttons/subscriptions';
    $cmd->executable = $this->login->is_allowed (Privilege_set_folder, Privilege_modify, $folder);
    $cmd->importance = Command_importance_low;
    $this->append ($cmd);

    $cmd = $this->make_command ();
    $cmd->id = 'history';
    $cmd->caption = 'History';
    $cmd->link = "view_folder_history.php?id=$folder->id";
    $cmd->icon = '{icons}buttons/history';
    $cmd->executable = $this->login->is_allowed (Privilege_set_folder, Privilege_view_history, $folder);
    $cmd->importance = Command_importance_low;
    $this->append ($cmd);

    $cmd = $this->make_command ();
    $cmd->id = 'explorer';
    $cmd->caption = 'Explorer';
    $cmd->link = "view_explorer.php?id=$folder->id";
    $cmd->icon = '{icons}buttons/explorer';
    $cmd->importance = Command_importance_low;
    $this->append ($cmd);
  }

  /**
   * Add commands that create items in the folder.
   * @param FOLDER $folder
   * @access private
   */
  protected function _add_creators ($folder)
  {
    $cmd = $this->make_command ();
    $cmd->id = 'new_entry';
    $cmd->caption = 'New entry';
    $cmd->link = "create_entry.php?id=$folder->id";
    $cmd->icon = '{icons}buttons/new_object';
    $cmd->executable = $this->login->is_allowed (Privilege_set_entry, Privilege_create, $folder);
    $cmd->importance = Command_importance_high;
    $this->append ($cmd);
    
    if ($folder->is_organizational ())
    {
      $cmd->link = "select_folder.php?entry_type=ENTRY";
    }
  }
}