<?php

/**
 * @copyright Copyright (c) 2002-2008 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage command
 * @version 3.0.0
 * @since 2.7.0
 * @access private
 */

/****************************************************************************

Copyright (c) 2002-2008 Marco Von Ballmoos

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
 * @version 3.0.0
 * @since 2.7.0
 * @access private
 */
class FOLDER_COMMANDS extends COMMANDS
{
  /**
   * @param FOLDER &$folder Return commands for this object.
   */
  function FOLDER_COMMANDS (&$folder)
  {
    COMMANDS::COMMANDS ($folder->app);

    $this->append_group ('Edit');
    $this->_add_editors ($folder);
    $this->append_group ('View');
    $this->_add_viewers ($folder);

    $this->append_group ('Create');

    $cmd = $this->make_command ();
    $cmd->id = 'new';
    $cmd->title = 'New folder';
    $cmd->link = "create_folder.php?id=$folder->id";
    $cmd->icon = '{icons}buttons/new_folder';
    $cmd->executable = $this->login->is_allowed (Privilege_set_folder, Privilege_create, $folder);
    $cmd->importance = Command_importance_high + Command_importance_increment;
    $this->append ($cmd);

    if (! $folder->is_organizational ())
    {
      $this->_add_creators ($folder);
    }
  }

  /**
   * Add commands that edit the folder.
   * @param FOLDER &$folder Show commands for this folder.
   * @access private
   */
  function _add_editors (&$folder)
  {
    $cmd = $this->make_command ();
    $cmd->id = 'edit';
    $cmd->title = 'Edit';
    $cmd->link = "edit_folder.php?id=$folder->id";
    $cmd->icon = '{icons}buttons/edit';
    $cmd->executable = $this->login->is_allowed (Privilege_set_folder, Privilege_modify, $folder);
    $cmd->importance = Command_importance_high + Command_importance_increment;
    $this->append ($cmd);

    $cmd = $this->make_command ();
    $cmd->id = 'delete';
    $cmd->title = 'Delete';
    $cmd->link = "delete_folder.php?id=$folder->id";
    $cmd->icon = '{icons}buttons/delete';
    $cmd->executable = (! $folder->deleted () && ! $folder->is_root () &&
                        $this->login->is_allowed (Privilege_set_folder, Privilege_delete, $folder));
    $cmd->importance = Command_importance_high - 2 * Command_importance_increment;
    $this->append ($cmd);

    $cmd = $this->make_command ();
    $cmd->id = 'purge';
    $cmd->title = 'Purge';
    $cmd->link = "purge_folder.php?id=$folder->id";
    $cmd->icon = '{icons}buttons/purge';
    $cmd->executable = ($this->login->is_allowed (Privilege_set_folder, Privilege_purge, $folder)
                        && ! $folder->is_root ());
    $cmd->importance = Command_importance_high - 2 * Command_importance_increment;
    $this->append ($cmd);
  }

  /**
   * Add buttons that provide views on the folder.
   * @param FOLDER &$folder
   * @param USER &$creator Folder belongs to this user (also available as $folder->creator ()).
   * @access private
   */
  function _add_viewers (&$folder)
  {
    $cmd = $this->make_command ();
    $cmd->id = 'security';
    $cmd->title = 'Security';
    $cmd->link = "view_folder_permissions.php?id=$folder->id";
    $cmd->icon = '{icons}buttons/security';
    $cmd->executable = $this->login->is_allowed (Privilege_set_folder, Privilege_secure, $folder);
    $cmd->importance = Command_importance_low;
    $this->append ($cmd);

    $cmd = $this->make_command ();
    $cmd->id = 'subscribers';
    $cmd->title = 'Subscribers';
    $cmd->link = "view_folder_subscriptions.php?id=$folder->id";
    $cmd->icon = '{icons}buttons/subscriptions';
    $cmd->executable = $this->login->is_allowed (Privilege_set_folder, Privilege_modify, $folder);
    $cmd->importance = Command_importance_low;
    $this->append ($cmd);

    $cmd = $this->make_command ();
    $cmd->id = 'history';
    $cmd->title = 'History';
    $cmd->link = "view_folder_history.php?id=$folder->id";
    $cmd->icon = '{icons}buttons/history';
    $cmd->executable = $this->login->is_allowed (Privilege_set_folder, Privilege_view_history, $folder);
    $cmd->importance = Command_importance_low;
    $this->append ($cmd);

    $cmd = $this->make_command ();
    $cmd->id = 'explorer';
    $cmd->title = 'Explorer';
    $cmd->link = "view_explorer.php?id=$folder->id";
    $cmd->icon = '{icons}buttons/explorer';
    $cmd->importance = Command_importance_low;
    $this->append ($cmd);
  }

  /**
   * Add commands that create items in the folder.
   * @param FOLDER &$folder
   * @access private
   */
  function _add_creators (&$folder)
  {
    $cmd = $this->make_command ();
    $cmd->id = 'new_entry';
    $cmd->title = 'New entry';
    $cmd->link = "create_entry.php?id=$folder->id";
    $cmd->icon = '{icons}buttons/new_object';
    $cmd->executable = $this->login->is_allowed (Privilege_set_entry, Privilege_create, $folder);
    $cmd->importance = Command_importance_high;
    $this->append ($cmd);
  }
}

?>