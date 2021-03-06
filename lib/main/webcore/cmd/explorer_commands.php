<?php

/**
 * @copyright Copyright (c) 2002-2014 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage command
 * @version 3.6.0
 * @since 2.7.0
 * @access private
 */

/****************************************************************************

Copyright (c) 2002-2014 Marco Von Ballmoos

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
 * Used to manipulate multiple items in the folder contents from the explorer
 * page.
 * @package webcore
 * @subpackage command
 * @version 3.6.0
 * @since 2.7.0
 * @access private
 */
class EXPLORER_COMMANDS extends COMMANDS
{
  /**
   * @param FOLDER $folder Configure commands for this object.
   * @param string $form_name Commands are created using this form name.
   */
  public function __construct ($folder, $form_name)
  {
    parent::__construct ($folder->app);

    $cmd = $this->make_command ();
    $cmd->id = 'print';
    $cmd->caption = 'Print';
    $cmd->link = '#';
    $cmd->on_click = "submit_explorer_form ('$form_name', 'multiple_print.php')";
    $cmd->icon = '{icons}buttons/print';
    $cmd->executable = true;
    $this->append ($cmd);

    $cmd = $this->make_command ();
    $cmd->id = 'move';
    $cmd->caption = 'Move';
    $cmd->link = '#';
    $cmd->on_click = "submit_explorer_form ('$form_name', 'multiple_move.php')";
    $cmd->icon = '{icons}buttons/move';
    $cmd->executable = ($this->app->login->is_allowed (Privilege_set_folder, Privilege_delete, $folder)
                        || $this->app->login->is_allowed (Privilege_set_entry, Privilege_delete, $folder));
    $this->append ($cmd);

    $cmd = $this->make_command ();
    $cmd->id = 'copy';
    $cmd->caption = 'Copy';
    $cmd->link = '#';
    $cmd->on_click = "submit_explorer_form ('$form_name', 'multiple_copy.php')";
    $cmd->icon = '{icons}buttons/copy';
    $cmd->executable = true;
    $this->append ($cmd);

    $cmd = $this->make_command ();
    $cmd->id = 'delete';
    $cmd->caption = 'Delete';
    $cmd->link = '#';
    $cmd->on_click = "submit_explorer_form ('$form_name', 'multiple_delete.php')";
    $cmd->icon = '{icons}buttons/delete';
    $cmd->importance = Command_importance_low;
    $cmd->executable = ($this->app->login->is_allowed (Privilege_set_folder, Privilege_delete, $folder)
                        || $this->app->login->is_allowed (Privilege_set_entry, Privilege_delete, $folder));
    $this->append ($cmd);

    $cmd = $this->make_command ();
    $cmd->id = 'restore';
    $cmd->caption = 'Restore';
    $cmd->link = '#';
    $cmd->on_click = "submit_explorer_form ('$form_name', 'multiple_restore.php')";
    $cmd->icon = '{icons}buttons/restore';
    $cmd->importance = Command_importance_low;
    $cmd->executable = ($this->app->login->is_allowed (Privilege_set_folder, Privilege_view_hidden, $folder)
                        && ($this->app->login->is_allowed (Privilege_set_entry, Privilege_create, $folder)
                            || $this->app->login->is_allowed (Privilege_set_folder, Privilege_create, $folder)));
    $this->append ($cmd);

    $cmd = $this->make_command ();
    $cmd->id = 'purge';
    $cmd->caption = 'Purge';
    $cmd->link = '#';
    $cmd->on_click = "submit_explorer_form ('$form_name', 'multiple_purge.php')";
    $cmd->icon = '{icons}buttons/purge';
    $cmd->importance = Command_importance_low;
    $cmd->executable = ($this->app->login->is_allowed (Privilege_set_folder, Privilege_purge, $folder)
                        || $this->app->login->is_allowed (Privilege_set_entry, Privilege_purge, $folder));
    $this->append ($cmd);
  }
}

/**
 * Return the commands for entries/folders in an {@link APPLICATION}.
 * Used to manipulate multiple items from the explorer page. Used by
 * applications that use {@link DRAFTABLE_ENTRY}s.
 * @package webcore
 * @subpackage command
 * @version 3.6.0
 * @since 2.7.1
 * @access private
 */
class DRAFTABLE_EXPLORER_COMMANDS extends EXPLORER_COMMANDS
{
  /**
   * @param FOLDER $folder Configure commands for this object.
   * @param string $form_name Commands are created using this form name.
   */
  public function __construct ($folder, $form_name)
  {
    parent::__construct ($folder, $form_name);

    $cmd = $this->make_command ();
    $cmd->id = 'publish';
    $cmd->caption = 'Publish';
    $cmd->link = '#';
    $cmd->on_click = "submit_explorer_form ('$form_name', 'multiple_publish.php')";
    $cmd->icon = '{icons}buttons/ship';
    $cmd->executable = $this->app->login->is_allowed (Privilege_set_entry, Privilege_modify, $folder);
    $this->append ($cmd);

    $cmd = $this->make_command ();
    $cmd->id = 'queue';
    $cmd->caption = 'Queue';
    $cmd->link = '#';
    $cmd->on_click = "submit_explorer_form ('$form_name', 'multiple_queue.php')";
    $cmd->icon = '{icons}buttons/queue';
    $cmd->executable = $this->app->login->is_allowed (Privilege_set_entry, Privilege_modify, $folder);
    $this->append ($cmd);

    $cmd = $this->make_command ();
    $cmd->id = 'abandon';
    $cmd->caption = 'Abandon';
    $cmd->link = '#';
    $cmd->on_click = "submit_explorer_form ('$form_name', 'multiple_abandon.php')";
    $cmd->icon = '{icons}buttons/abandon';
    $cmd->executable = $this->app->login->is_allowed (Privilege_set_entry, Privilege_modify, $folder);
    $this->append ($cmd);
  }
}

?>