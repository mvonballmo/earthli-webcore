<?php

/**
 * @copyright Copyright (c) 2002-2013 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage command
 * @version 3.3.0
 * @since 2.7.0
 * @access private
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
require_once ('webcore/cmd/commands.php');

/**
 * Return the commands for lists of users.
 * These commands apply either to individual {@link USER}s or to the special
 * "anonymous" or "registered" users.
 * @package webcore
 * @subpackage command
 * @version 3.3.0
 * @since 2.7.0
 * @access private
 */
class USER_LIST_COMMANDS extends COMMANDS
{
  /**
   * @param APPLICATION $app
   */
  public function __construct ($app)
  {
    parent::__construct ($app);

    $cmd = $this->make_command ();
    $cmd->id = 'create';
    $cmd->caption = 'New user';
    $cmd->link = "create_user.php";
    $cmd->icon = '{icons}buttons/create';
    $cmd->executable = $this->login->is_allowed (Privilege_set_user, Privilege_create);
    $cmd->importance = Command_importance_high + Command_importance_increment;
    $this->append ($cmd);

    $link_url = new URL ($this->env->url (Url_part_no_host_path));
    $link_url->replace_argument ('show_anon', '');

    $cmd = $this->make_command ();
    $cmd->id = 'show_registered';
    $cmd->caption = 'Show registered users';
    $cmd->link = $link_url->as_text ();
    $cmd->icon = '{icons}buttons/login';
    $cmd->executable = read_var ('show_anon');
    $cmd->importance = Command_importance_high;
    $this->append ($cmd);

    $link_url->replace_argument ('show_anon', 1);

    $cmd = $this->make_command ();
    $cmd->id = 'show_anonymous';
    $cmd->caption = 'Show anonymous users';
    $cmd->link = $link_url->as_text ();
    $cmd->icon = '{icons}buttons/anonymous';
    $cmd->executable = ! read_var ('show_anon');
    $cmd->importance = Command_importance_high;
    $this->append ($cmd);
  }
}

/**
 * Return the commands for the user manager.
 * These commands apply either to individual {@link USER}s or to the special
 * "anonymous" or "registered" users.
 * @package webcore
 * @subpackage command
 * @version 3.3.0
 * @since 2.7.0
 * @access private
 */
class USER_MANAGEMENT_COMMANDS extends USER_LIST_COMMANDS
{
  /**
   * @param APPLICATION $app
   */
  public function __construct ($app)
  {
    parent::__construct ($app);

    $cmd = $this->make_command ();
    $cmd->id = 'anon_permissions';
    $cmd->caption = 'Edit anonymous user permissions';
    $cmd->link = "edit_anon_user_permissions.php";
    $cmd->icon = '{icons}buttons/anon_user_permissions';
    $cmd->executable = $this->login->is_allowed (Privilege_set_user, Privilege_secure);
    $cmd->importance = Command_importance_low;
    $this->append ($cmd);

    $cmd = $this->make_command ();
    $cmd->id = 'reg_permissions';
    $cmd->caption = 'Edit registered user permissions';
    $cmd->link = "edit_all_users_permissions.php";
    $cmd->icon = '{icons}buttons/reg_user_permissions';
    $cmd->executable = $this->login->is_allowed (Privilege_set_user, Privilege_secure);
    $cmd->importance = Command_importance_low;
    $this->append ($cmd);
  }
}

?>