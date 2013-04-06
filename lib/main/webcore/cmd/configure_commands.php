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
 * Return the commands for an {@link APPLICATION}.
 * Used on the configuration page to publish/migrate or otherwise configure the
 * application.
 * @package webcore
 * @subpackage command
 * @version 3.3.0
 * @since 2.7.0
 * @access private
 */
class CONFIGURE_COMMANDS extends COMMANDS
{
  /**
   * @param APPLICATION_CONFIGURATION_INFO $info
   */
  public function __construct ($info)
  {
    parent::__construct ($info->app);

    $cmd = $this->make_command ();
    $cmd->id = 'publish';
    $cmd->caption = 'Publish';
    $cmd->link = "publish.php";
    $cmd->icon = '{icons}indicators/published';
    $cmd->executable = $this->app->login->is_allowed (Privilege_set_global, Privilege_configure);
    $this->append ($cmd);

    $cmd = $this->make_command ();
    $cmd->id = 'upgrade_app';
    $cmd->caption = 'Upgrade Application';
    $cmd->link = "upgrade.php";
    $cmd->icon = '{icons}buttons/upgrade';
    $cmd->executable = $info->app_info->needs_upgrade () && $this->app->login->is_allowed (Privilege_set_global, Privilege_configure);
    $this->append ($cmd);

    $cmd = $this->make_command ();
    $cmd->id = 'upgrade_webcore';
    $cmd->caption = 'Upgrade WebCore';
    $cmd->link = "upgrade.php?framework=1";
    $cmd->icon = '{icons}buttons/upgrade';
    $cmd->executable = $info->lib_info->needs_upgrade () && $this->app->login->is_allowed (Privilege_set_global, Privilege_configure);
    $this->append ($cmd);

    $cmd = $this->make_command ();
    $cmd->id = 'test_suite';
    $cmd->caption = 'Test Suite';
    $cmd->link = "view_test_suite.php";
    $cmd->icon = '{icons}buttons/test';
    $cmd->executable = $this->app->login->is_allowed (Privilege_set_global, Privilege_configure);
    $this->append ($cmd);
  }
}

?>