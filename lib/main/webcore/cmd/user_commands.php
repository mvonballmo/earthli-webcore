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
 * Return the commands for a {@link USER}.
 * @package webcore
 * @subpackage command
 * @version 3.0.0
 * @since 2.7.0
 * @access private
 */
class USER_COMMANDS extends COMMANDS
{
  /**
   * @param USER $user Configure commands for this object.
   */
  public function USER_COMMANDS ($user)
  {
    COMMANDS::COMMANDS ($user->app);

    $title = urlencode ($user->title);

    if (! $this->login->is_anonymous ())
    {
      $cmd = $this->make_command ();
      $cmd->id = 'edit';
      $cmd->title = 'Edit';
      $cmd->link = 'edit_user.php?name=' . $title;
      $cmd->icon = '{icons}buttons/edit';
      $cmd->executable = $this->login->is_allowed (Privilege_set_user, Privilege_modify, $user);
      $cmd->importance = Command_importance_high;
      $this->append ($cmd);

      $cmd = $this->make_command ();
      $cmd->id = 'password';
      $cmd->title = 'Password';
      $cmd->link = 'edit_password.php?name=' . $title;
      $cmd->icon = '{icons}buttons/password';
      $cmd->executable = $this->login->is_allowed (Privilege_set_global, Privilege_password, $user);
      $cmd->importance = Command_importance_high - Command_importance_increment;
      $this->append ($cmd);

      $cmd = $this->make_command ();
      $cmd->id = 'subscriptions';
      $cmd->title = 'Subscriptions';
      $cmd->link = 'view_user_subscriptions.php?email=' . urlencode ($user->email);
      $cmd->icon = '{icons}buttons/subscriptions';
      $cmd->executable = $user->email && $this->login->is_allowed (Privilege_set_global, Privilege_subscribe, $user);
      $cmd->importance = Command_importance_high - 2 * Command_importance_increment;
      $this->append ($cmd);

      $cmd = $this->make_command ();
      $cmd->id = 'history';
      $cmd->title = 'History';
      $cmd->link = 'view_user_history.php?name=' . $title;
      $cmd->icon = '{icons}buttons/history';
      $cmd->executable = $this->login->is_allowed (Privilege_set_user, Privilege_view_history, $user);
      $cmd->importance = Command_importance_low;
      $this->append ($cmd);
    }

    $cmd = $this->make_command ();
    $cmd->id = 'delete';
    $cmd->title = 'Delete';
    $cmd->link = 'delete_user.php?name=' . $title;
    $cmd->icon = '{icons}buttons/delete';
    $cmd->executable = ($this->login->is_allowed (Privilege_set_user, Privilege_delete, $user)
                        && $this->app->user_options->users_can_be_deleted);
    $cmd->importance = Command_importance_low;
    $this->append ($cmd);

    $cmd = $this->make_command ();
    $cmd->id = 'secure';
    $cmd->title = 'Security';
    $cmd->link = 'edit_user_permissions.php?name=' . $title;
    $cmd->icon = '{icons}buttons/security';
    $cmd->executable = $this->login->is_allowed (Privilege_set_user, Privilege_secure);
    $cmd->importance = Command_importance_low;
    $this->append ($cmd);
  }
}

?>