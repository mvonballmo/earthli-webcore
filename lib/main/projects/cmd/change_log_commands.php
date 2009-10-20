<?php

/**
 * @copyright Copyright (c) 2002-2009 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package projects
 * @subpackage command
 * @version 3.2.0
 * @since 1.9.0
 * @access private
 */

/****************************************************************************

Copyright (c) 2002-2009 Marco Von Ballmoos

This file is part of earthli Projects.

earthli Projects is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

earthli Projects is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with earthli Projects; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

For more information about the earthli Projects, visit:

http://www.earthli.com/software/webcore/projects

****************************************************************************/

/** */
require_once ('webcore/cmd/commands.php');

/**
 * Return the commands for a {@link CHANGE_LOG}.
 * @package projects
 * @subpackage command
 * @version 3.2.0
 * @since 1.9.0
 * @access private
 */
class CHANGE_LOG_COMMANDS extends COMMANDS
{
  /**
   * @param APPLICATION $app
   */
  public function __construct ($app)
  {
    parent::__construct ($app);

    $cmd = $this->make_command ();
    $cmd->id = 'print';
    $cmd->title = 'Print';
    $url = new URL ($this->env->url (Url_part_no_host_path));
    $url->add_argument ('printable', '1');
    $cmd->link = $url->as_text ();
    $cmd->icon = '{icons}/buttons/print';
    $cmd->executable = true;
    $cmd->importance = Command_importance_high;
    $this->append ($cmd);

    $show_date = read_var ('show_date', true);

    $cmd = $this->make_command ();
    $cmd->id = 'show_dates';
    if ($show_date)
    {
      $cmd->title = 'Hide Dates';
    }
    else
    {
      $cmd->title = 'Show Dates';
    }
    $url = new URL ($this->env->url (Url_part_no_host_path));
    $url->replace_argument ('show_date', ! $show_date);
    $cmd->link = $url->as_text ();
    $cmd->icon = '{icons}/buttons/calendar';
    $cmd->executable = true;
    $cmd->importance = Command_importance_high - Command_importance_increment;
    $this->append ($cmd);

    $show_user = read_var ('show_user', true);

    $cmd = $this->make_command ();
    $cmd->id = 'show_users';
    if ($show_user)
    {
      $cmd->title = 'Hide Users';
    }
    else
    {
      $cmd->title = 'Show Users';
    }
    $url = new URL ($this->env->url (Url_part_no_host_path));
    $url->replace_argument ('show_user', ! $show_user);
    $cmd->link = $url->as_text ();
    $cmd->icon = '{icons}/buttons/login';
    $cmd->executable = true;
    $cmd->importance = Command_importance_high - Command_importance_increment;
    $this->append ($cmd);

    $show_description = read_var ('show_description', true);

    $cmd = $this->make_command ();
    $cmd->id = 'show_descriptions';
    if ($show_description)
    {
      $cmd->title = 'Hide descriptions';
    }
    else
    {
      $cmd->title = 'Show descriptions';
    }
    $url = new URL ($this->env->url (Url_part_no_host_path));
    $url->replace_argument ('show_description', ! $show_description);
    $cmd->link = $url->as_text ();
    $cmd->icon = '{icons}indicators/text';
    $cmd->executable = true;
    $cmd->importance = Command_importance_high - Command_importance_increment;
    $this->append ($cmd);
  }
}

/**
 * Return the commands for a {@link CHANGE_LOG} for a {@link BRANCH}.
 * @package projects
 * @subpackage command
 * @version 3.2.0
 * @since 1.9.0
 * @access private
 */
class BRANCH_CHANGE_LOG_COMMANDS extends CHANGE_LOG_COMMANDS
{
  /**
   * @param APPLICATION $app
   */
  public function __construct ($app)
  {
    parent::__construct ($app);

    $show_all = read_var ('show_all', 1);

    $cmd = $this->make_command ();
    $cmd->id = 'show_releases';
    if ($show_all)
    {
      $cmd->title = 'Show Unassigned';
      $cmd->icon = '{icons}indicators/question';
    }
    else
    {
      $cmd->title = 'Show Releases';
      $cmd->icon = '{app_icons}buttons/new_release';
    }
    $url = new URL ($this->env->url (Url_part_no_host_path));
    $url->replace_argument ('show_all', ! $show_all);
    $cmd->link = $url->as_text ();
    $cmd->executable = true;
    $cmd->importance = Command_importance_low;
    $this->append ($cmd);
  }
}

?>